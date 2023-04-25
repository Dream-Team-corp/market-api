<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%store_info}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%store}}`
 */
class m230425_105221_create_store_info_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%store_info}}', [
            'id' => $this->primaryKey(),
            'store_id' => $this->integer(),
            'location' => $this->string()->notNull(),
            'store_type' => $this->string()->notNull(),
            'created_at' => $this->integer()->notNull(),
        ]);

        // creates index for column `store_id`
        $this->createIndex(
            '{{%idx-store_info-store_id}}',
            '{{%store_info}}',
            'store_id'
        );

        // add foreign key for table `{{%store}}`
        $this->addForeignKey(
            '{{%fk-store_info-store_id}}',
            '{{%store_info}}',
            'store_id',
            '{{%store}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%store}}`
        $this->dropForeignKey(
            '{{%fk-store_info-store_id}}',
            '{{%store_info}}'
        );

        // drops index for column `store_id`
        $this->dropIndex(
            '{{%idx-store_info-store_id}}',
            '{{%store_info}}'
        );

        $this->dropTable('{{%store_info}}');
    }
}
