<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Core\Configure;

/**
 * OrcidBatchTriggers Model
 *
 * @property \App\Model\Table\OrcidStatusTypesTable&\Cake\ORM\Association\BelongsTo $OrcidStatusTypes
 * @property \App\Model\Table\OrcidBatchesTable&\Cake\ORM\Association\BelongsTo $OrcidBatches
 * @property \App\Model\Table\OrcidBatchGroupsTable&\Cake\ORM\Association\BelongsTo $OrcidBatchGroups
 *
 * @method \App\Model\Entity\OrcidBatchTrigger newEmptyEntity()
 * @method \App\Model\Entity\OrcidBatchTrigger newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\OrcidBatchTrigger[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\OrcidBatchTrigger get($primaryKey, $options = [])
 * @method \App\Model\Entity\OrcidBatchTrigger findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\OrcidBatchTrigger patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\OrcidBatchTrigger[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\OrcidBatchTrigger|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\OrcidBatchTrigger saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\OrcidBatchTrigger[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\OrcidBatchTrigger[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\OrcidBatchTrigger[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\OrcidBatchTrigger[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class OrcidBatchTriggersTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('ULS.ORCID_BATCH_TRIGGERS');
        $this->setDisplayField('NAME');
        $this->setPrimaryKey('ID');

        $this->belongsTo('OrcidStatusTypes', [
            'foreignKey' => 'ORCID_STATUS_TYPE_ID',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('OrcidBatches', [
            'foreignKey' => 'ORCID_BATCH_ID',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('OrcidBatchGroups', [
            'foreignKey' => 'ORCID_BATCH_GROUP_ID',
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('ID')
            ->notEmptyString('ID')
            ->add('ID', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->scalar('NAME')
            ->maxLength('NAME', 512)
            ->requirePresence('NAME', 'create')
            ->notEmptyString('NAME', 'The trigger name must be provided.');

        $validator
            ->integer('ORCID_STATUS_TYPE_ID')
            ->requirePresence('ORCID_STATUS_TYPE_ID', 'create')
            ->notEmptyString('ORCID_STATUS_TYPE_ID', 'The status type must be provided.');

        $validator
            ->integer('ORCID_BATCH_ID')
            ->requirePresence('ORCID_BATCH_ID', 'create')
            ->notEmptyString('ORCID_BATCH_ID', 'The batch must be provided.');

        $validator
            ->integer('TRIGGER_DELAY')
            ->requirePresence('TRIGGER_DELAY', 'create')
            ->notEmptyString('TRIGGER_DELAY')
            ->add('TRIGGER_DELAY', 'naturalNumber', ['rule' => ['naturalNumber', true], 'message' => 'The triggering days must be a natural number.', ]);

        $validator
            ->integer('ORCID_BATCH_GROUP_ID')
            ->allowEmptyString('ORCID_BATCH_GROUP_ID');

        $validator
            ->dateTime('BEGIN_DATE')
            ->allowEmptyDateTime('BEGIN_DATE');

        $validator
            ->integer('REQUIRE_BATCH_ID')
            ->allowEmptyString('REQUIRE_BATCH_ID');

        $validator
            ->integer('REPEAT')
            ->notEmptyString('REPEAT')
            ->add('TRIGGER_DELAY', 'naturalNumber', ['rule' => ['naturalNumber', true], 'message' => 'The repetition days must be a natural number.']);

        $validator
            ->integer('MAXIMUM_REPEAT')
            ->notEmptyString('MAXIMUM_REPEAT')
            ->add('TRIGGER_DELAY', 'naturalNumber', ['rule' => ['naturalNumber', true], 'message' => 'The repetition limit must be a natural number.']);

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->isUnique(['ID']), ['errorField' => 'ID']);
        $rules->add($rules->existsIn('ORCID_STATUS_TYPE_ID', 'OrcidStatusTypes'), ['errorField' => 'ORCID_STATUS_TYPE_ID']);
        $rules->add($rules->existsIn('ORCID_BATCH_ID', 'OrcidBatches'), ['errorField' => 'ORCID_BATCH_ID']);
        $rules->add($rules->existsIn('ORCID_BATCH_GROUP_ID', 'OrcidBatchGroups'), ['errorField' => 'ORCID_BATCH_GROUP_ID']);

        return $rules;
    }

    /**
     * Returns the database connection name to use by default.
     *
     * @return string
     */
    public static function defaultConnectionName(): string
    {
        return (Configure::read('debug')) ? 'default' : 'production-default';
    }
}
