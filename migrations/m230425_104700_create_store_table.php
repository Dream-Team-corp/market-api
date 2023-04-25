<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%store}}`.
 */
class m230425_104700_create_store_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('store', [
            'id' => $this->primaryKey(),
            'store_name' => $this->string(),
            'user_id' => $this->integer()->notNull(),
            'created_at' => $this->dateTime()->notNull(),
        ]);
 
        // create index for user_id column 
        $this->createIndex(
            'idx-store-user_id',
            'store',
            'user_id'
        );

        // add foreign key for user_id 
        $this->addForeignKey(
            'fk-store-user_id',
            'store',
            'user_id',
            'user',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex(
            'idx-store-user_id',
            'store'
        );
        $this->dropForeignKey(
            'fk-store-user_id',
            'store'
        );
        $this->dropTable('{{%store}}');
    }
}
