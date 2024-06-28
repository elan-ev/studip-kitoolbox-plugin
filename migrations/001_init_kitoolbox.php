<?php

final class InitKitoolbox extends Migration
{
    public function up()
    {
        DBManager::get()->exec("CREATE TABLE IF NOT EXISTS `kit_tools` (
            `id`                    INT(11) NOT NULL AUTO_INCREMENT,
            `name`                  VARCHAR(255) NOT NULL,
            `description`           MEDIUMTEXT NOT NULL,
            `url`                  MEDIUMTEXT NOT NULL,
            `mkdate`                INT(11) UNSIGNED NOT NULL,
            `chdate`                INT(11) UNSIGNED NOT NULL,

            PRIMARY KEY (`id`)
            )"
        );
    }
    public function down()
    {
        DBManager::get()->exec("DROP TABLE IF EXISTS `kit_tools`");
    }
}