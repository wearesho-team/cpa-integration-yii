<?php

use yii\db\Migration;

/**
 * Handles the creation of table `stored_conversion`.
 */
class m170910_122053_create_stored_conversion_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('stored_conversion', [
            'id' => $this->integer()->unsigned()->notNull(),
            'type' => $this->string()->notNull(),
            'conversion_serialized' => $this->text()->notNull(),
            'response_serialized' => $this->text()->notNull(),
        ]);

        if ($this->getDb()->getDriverName() !== 'sqlite') {
            $this->addPrimaryKey(
                'stored_conversion_pair',
                'stored_conversion',
                ['id', 'type',]
            );
        } else {
            $this->createIndex(
                'stored_conversion_pair',
                'stored_conversion',
                ['id', 'type',],
                true
            );
        }
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('stored_conversion');
    }
}
