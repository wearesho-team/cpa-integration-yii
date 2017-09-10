<?php

use yii\db\Migration;

/**
 * Handles the creation of table `stored_lead`.
 */
class m170910_122042_create_stored_lead_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('stored_lead', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->unsigned()->notNull(),
            'serialized_lead' => $this->text()->notNull(),
        ]);

        $this->createIndex(
            'stored_lead_unique_user',
            'stored_lead',
            'user_id',
            true
        );
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropIndex('stored_lead_unique_user', 'stored_lead');
        $this->dropTable('stored_lead');
    }
}
