<?php

final class InitKitoolbox extends Migration
{
    public function up()
    {
        DBManager::get()->exec("CREATE TABLE IF NOT EXISTS `kit_tools` (
            `id`                    INT(11) NOT NULL AUTO_INCREMENT,
            `name`                  VARCHAR(255) NOT NULL,
            `description`           MEDIUMTEXT NOT NULL,
            `url`                   MEDIUMTEXT NOT NULL,
            `active`                TINYINT(1) NOT NULL DEFAULT '1',
            `mkdate`                INT(11) UNSIGNED NOT NULL,
            `chdate`                INT(11) UNSIGNED NOT NULL,

            PRIMARY KEY (`id`)
            )"
        );

        DBManager::get()->exec("CREATE TABLE IF NOT EXISTS `kit_course_tools` (
            `id`                    INT(11) NOT NULL AUTO_INCREMENT,
            `tool_id`               INT(11) NOT NULL,
            `cid`                   CHAR(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
            `active`                TINYINT(1) NOT NULL DEFAULT '1',
            `max_tokens`            INT(11) NOT NULL,
            `tokens_per_user`       INT(11) NOT NULL,
            `mkdate`                INT(11) UNSIGNED NOT NULL,
            `chdate`                INT(11) UNSIGNED NOT NULL,

            PRIMARY KEY (`id`),
            INDEX index_parent_id (`tool_id`),
            INDEX index_range_id (`cid`)
            )"
        );

        DBManager::get()->exec("CREATE TABLE IF NOT EXISTS `kit_quotas` (
            `id`                    INT(11) NOT NULL AUTO_INCREMENT,
            `mkdate`                INT(11) UNSIGNED NOT NULL,
            `chdate`                INT(11) UNSIGNED NOT NULL,

            PRIMARY KEY (`id`)
            )"
        );
    }
    public function down()
    {
        DBManager::get()->exec("DROP TABLE IF EXISTS `kit_tools`");
        DBManager::get()->exec("DROP TABLE IF EXISTS `kit_course_tools`");
        DBManager::get()->exec("DROP TABLE IF EXISTS `kit_quotas`");
    }
}

