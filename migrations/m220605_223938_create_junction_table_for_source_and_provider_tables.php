<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%source_provider}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%source}}`
 * - `{{%provider}}`
 */
class m220605_223938_create_junction_table_for_source_and_provider_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable('{{%source_provider}}', [
            'source_id' => $this->integer(),
            'provider_id' => $this->integer(),
            'PRIMARY KEY(source_id, provider_id)',
        ]);

        // creates index for column `source_id`
        $this->createIndex(
            '{{%idx-source_provider-source_id}}',
            '{{%source_provider}}',
            'source_id'
        );

        // add foreign key for table `{{%source}}`
        $this->addForeignKey(
            '{{%fk-source_provider-source_id}}',
            '{{%source_provider}}',
            'source_id',
            '{{%source}}',
            'id',
            'CASCADE'
        );

        // creates index for column `provider_id`
        $this->createIndex(
            '{{%idx-source_provider-provider_id}}',
            '{{%source_provider}}',
            'provider_id'
        );

        // add foreign key for table `{{%provider}}`
        $this->addForeignKey(
            '{{%fk-source_provider-provider_id}}',
            '{{%source_provider}}',
            'provider_id',
            '{{%provider}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        // drops foreign key for table `{{%source}}`
        $this->dropForeignKey(
            '{{%fk-source_provider-source_id}}',
            '{{%source_provider}}'
        );

        // drops index for column `source_id`
        $this->dropIndex(
            '{{%idx-source_provider-source_id}}',
            '{{%source_provider}}'
        );

        // drops foreign key for table `{{%provider}}`
        $this->dropForeignKey(
            '{{%fk-source_provider-provider_id}}',
            '{{%source_provider}}'
        );

        // drops index for column `provider_id`
        $this->dropIndex(
            '{{%idx-source_provider-provider_id}}',
            '{{%source_provider}}'
        );

        $this->dropTable('{{%source_provider}}');
    }
}
