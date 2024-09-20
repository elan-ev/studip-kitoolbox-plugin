<?php

final class AddRules extends Migration
{
    public function up()
    {
        DBManager::get()->exec("CREATE TABLE IF NOT EXISTS `kit_rules` (
            `id`                    INT(11) NOT NULL AUTO_INCREMENT,
            `course_id`             CHAR(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
            `content`               MEDIUMTEXT NOT NULL,
            `released`              TINYINT(1) NOT NULL DEFAULT '0',
            `last_edit_id`          CHAR(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
            `mkdate`                INT(11) UNSIGNED NOT NULL,
            `chdate`                INT(11) UNSIGNED NOT NULL,

            PRIMARY KEY (`id`)
            )"
        );
    }
    public function down()
    {
        DBManager::get()->exec("DROP TABLE IF EXISTS `kit_rules`");
    }
}