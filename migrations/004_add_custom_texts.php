<?php

final class AddCustomTexts extends Migration
{
    public function description()
    {
        return 'Add configs for KI Toolbox plugin';
    }
    public function up()
    {
        $query = 'INSERT INTO `config` (`field`, `value`, `type`, `section`, `range`, `description`, `mkdate`, `chdate`)
                  VALUES (:name, :value, :type, :section, :range, :description, UNIX_TIMESTAMP(), UNIX_TIMESTAMP())';
        $statement = DBManager::get()->prepare($query);
        $statement->execute([
            'name' => 'KITOOLBOX_TEXT_LANDING_PAGE_TEACHER',
            'value' => '',
            'type' => 'i18n',
            'section' => 'KIToolbox',
            'range' => 'global',
            'description' => 'Text für die Landing-Page, diese wird Lehrenden angezeigt solange das Tool in der Veranstaltung noch nicht vom Lehrenden konfigureirt wurde.'
        ]);
        $statement->execute([
            'name' => 'KITOOLBOX_TEXT_LANDING_PAGE_TEACHER_SETTINGS',
            'value' => '',
            'type' => 'i18n',
            'section' => 'KIToolbox',
            'range' => 'global',
            'description' => 'Text für die Erklärung der Einstellungen, diese wird Lehrenden angezeigt solange das Tool in der Veranstaltung noch nicht vom Lehrenden konfigureirt wurde.'
        ]);
        $statement->execute([
            'name' => 'KITOOLBOX_TEXT_LANDING_PAGE_STUDENT',
            'value' => '',
            'type' => 'i18n',
            'section' => 'KIToolbox',
            'range' => 'global',
            'description' => 'Text für die Landing-Page, diese wird Studierenden angezeigt solange das Tool in der Veranstaltung noch nicht vom Lehrenden konfigureirt wurde.'
        ]);
        $statement->execute([
            'name' => 'KITOOLBOX_TEXT_RULES_FOR_TOOLS_TEMPLATE',
            'value' => '',
            'type' => 'i18n',
            'section' => 'KIToolbox',
            'range' => 'global',
            'description' => 'Text der in die Rules for Tools eingefügt werden kann.'
        ]);
        $statement->execute([
            'name' => 'KITOOLBOX_TEXT_ESSENTIAL',
            'value' => '',
            'type' => 'i18n',
            'section' => 'KIToolbox',
            'range' => 'global',
            'description' => 'Text der im Bereich "Grundlegendes" angezeigt wird.'
        ]);

    }
    public function down()
    {
         $query = "DELETE `config`, `config_values`, `i18n`
        FROM `config`
        LEFT JOIN `config_values` USING (`field`)
        LEFT JOIN `i18n`
          ON `table` = 'config'
              AND `i18n`.`field` = 'value'
              AND `object_id` = CAST(MD5(`config`.`field`) AS CHAR CHARACTER SET latin1)
        WHERE `config`.`field` IN (
             'KITOOLBOX_TEXT_ESSENTIAL',
             'KITOOLBOX_TEXT_RULES_FOR_TOOLS_TEMPLATE',
             'KITOOLBOX_TEXT_LANDING_PAGE_STUDENT',
             'KITOOLBOX_TEXT_LANDING_PAGE_TEACHER_SETTINGS',
             'KITOOLBOX_TEXT_LANDING_PAGE_TEACHER'
        )";
        DBManager::get()->exec($query);

    }

}