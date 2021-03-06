<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * OrcidBatchGroupCachesFixture
 */
class OrcidBatchGroupCachesFixture extends TestFixture
{
    /**
     * Table name
     *
     * @var string
     */
    public $table = 'ULS.ORCID_BATCH_GROUP_CACHES';
    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->records = [
            [
                'ID' => 1,
                'ORCID_BATCH_GROUP_ID' => 1,
                'ORCID_USER_ID' => 1,
                'DEPRECATED' => '2022-07-21 19:49:28',
            ],
        ];
        parent::init();
    }
}
