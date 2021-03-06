<?php
declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @since         2.2.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Utility;

use ArrayAccess;
use InvalidArgumentException;
use RuntimeException;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\Mailer\Mailer;
use Exception;
use Cake\Datasource\ConnectionManager;
use Cake\Core\Configure;

class Emailer
{

    use LocatorAwareTrait;

	/**
	 * Verify that all connections are available
	 * 
	 * @return boolean Success on all connections
	 */
	public function connected() {
		$manager = new ConnectionManager();
		foreach ($manager->configured() as $name => $object) {
			$connection = $manager->get($name);
			$driver = $connection->getDriver();
			$connected = $driver->isConnected();
			if (!$connected) {
				return false;
			}
		}
		return true;
	}

	/**
	 * List any connection that is failing
	 * 
	 * @return array Names of failed connections
	 */
	public function getFailedConnections() {
		$manager = new ConnectionManager();
		$problems = array();
		foreach ($manager->configured() as $name => $object) {
			$connection = $manager->get($name);
			$driver = $connection->getDriver();
			$connected = $driver->isConnected();
			if (!$connected) {
				$problems[] = $connection->configName();
			}
		}
		return $problems;
	}

	/** 
	 * Send a batch message to a person, optionally marking an email as sent
	 * 
	 * @param string $toRecipient The email address to send the mail to
	 * @param \App\Model\Entity\OrcidBatch $orcidBatch The batch to send, may contain an OrcidEmail to mark sent
	 * @return boolean Successful send
	 */
	public function sendBatch($toRecipient, $orcidBatch) {
		$Mailer = new Mailer();
		if (Configure::read('debug')) {
			$toRecipient = str_replace('@', '.', $toRecipient).'@mailinator.com';
		}
		$Mailer
			->setFrom('noreply@orcid-dev.pitt.edu','ORCID @ Pitt')
			->setTo($toRecipient)
			->setSubject("Preview");
		$Mailer
			->setEmailFormat('html')
			->viewBuilder()
			->setTemplate('rendered')
			->setLayout('default')
			->setVar('body', $orcidBatch->BODY);
		try {
			$Mailer->send();
			return true;
		} catch (Exception $e) {
			return false;
		}
	}

	/**
	 * Execute a trigger to queue emails
	 * 
	 * @param \App\Model\Entity\OrcidBatchTrigger $trigger with at least one recursion
	 * @return boolean
	 */
    public function executeTrigger($trigger) {
        // Abort if OrcidTrigger does not contain expected information
		if (!isset($trigger) || !isset($trigger->orcid_status_type)) {
			return false;
		}
		// Trigger may not run prior to begin_date
		if (isset($trigger->BEGIN_DATE) && $trigger->BEGIN_DATE > time() ) {
			return false;
		}
		$failures = 0;
		// We'll use OrcidEmailTable to create new emails
		$OrcidEmailTable = $this->fetchTable('OrcidEmails');
		// We'll use OrcidStatusTable to ensure the user is at the trigger criteria
		$CurrentOrcidStatusTable = $this->fetchTable('CurrentOrcidStatuses');
		// We'll use OrcidBatchGroupTable to collect relevant users
		$OrcidBatchGroupTable = $this->fetchTable('OrcidBatchGroups');
		// If sequence is 0 a group is required.  We can't initialize everyone.
		if ($trigger->orcid_status_type->SEQ == 0 && !isset($trigger->orcid_batch_group)) {
			return false;
		}
		// Process each user at the status for the trigger_delay days
		$options = ['conditions' => ['CurrentOrcidStatuses.ORCID_STATUS_TYPE_ID' => $trigger->ORCID_STATUS_TYPE_ID]];
		// This will be our selection of users
		$users = [];
		if (isset($trigger->orcid_batch_group->id)) {
			$users = $OrcidBatchGroupTable->getAssociatedUsers($trigger->ORCID_BATCH_GROUP_ID, 'CurrentOrcidStatuses.ORCID_USER_ID');
			$options['conditions'][] = $users;
		}
		$userStatuses = $this->CurrentOrcidStatus->find('all', $options);
		foreach ($userStatuses as $userStatus) {
			// If a prior email is required, check for it
			if ($trigger->require_batch_id) {
				$options = ['conditions' => ['OrcidEmail.ORCID_USER_ID' => $userStatus->ORCID_USER_ID]];
				if ($trigger->REQUIRE_BATCH_ID !== -1) {
					$options['conditions']['OrcidEmail.ORCID_BATCH_ID'] = $trigger->REQUIRE_BATCH_ID;
				}
				if (!$this->OrcidEmail->find('first', $options)) {
					// if the prior email was not found, skip
					continue;
				}
			}
			// Create unless the email already exists
			$options = ['conditions' => ['OrcidEmail.ORCID_USER_ID' => $userStatus->ORCID_USER_ID, 'OrcidEmail.ORCID_BATCH_ID' => $trigger->OrcidBatch->ID]];
			// If a maximum repeat is set, count the number of times sent
			if ($trigger->MAXIMUM_REPEAT) {
				if ($this->OrcidEmail->find('count', $options) >= $trigger->maximum_repeat) {
					// if already at or past the limit, skip
					continue;
				}
			}
			// If this email is repeating, also check the last sent date
			if ($trigger->REPEAT) {
				$options['conditions']['TRUNC(NVL(OrcidEmail.SENT, SYSDATE) + '.$trigger->REPEAT.') >'] = date('Y-m-d');
			}
			if (!$this->OrcidEmail->find('first', $options)) {
				$this->OrcidEmail->create();
				$newEmail = ['ORCID_USER_ID' => $userStatus->ORCID_USER_ID, 'ORCID_BATCH_ID' => $trigger->OrcidBatch->ID, 'queued' => date('Y-m-d H:i:s')];
				if (!$this->OrcidEmail->save($newEmail)) {
					$failures++;
				}
			}
		}
		return !$failures;
    }
}
