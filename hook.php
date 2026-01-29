<?php

/**
 * -------------------------------------------------------------------------
 * goals plugin for GLPI
 * -------------------------------------------------------------------------
 *
 * LICENSE
 *
 * This file is part of goals.
 *
 * goals is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * goals is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with goals. If not, see <http://www.gnu.org/licenses/>.
 * -------------------------------------------------------------------------
 * @author    Juan Carlos Acosta Peraba
 * @copyright Copyright (C) 2026 Juan Carlos Acosta Peraba
 * @license   GPLv2+
 * @link      https://github.com/JuanCarlosAcostaPeraba/glpi-goals-plugin
 * -------------------------------------------------------------------------
 */

/**
 * Install hook
 *
 * @return boolean
 */
function plugin_goals_install()
{
    global $DB;

    // Create configuration table
    if (!$DB->tableExists('glpi_plugin_goals_configs')) {
        $query = "CREATE TABLE `glpi_plugin_goals_configs` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `show_technicians` TINYINT(1) NOT NULL DEFAULT 1,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        $DB->doQuery($query);

        // Insert default configuration
        $DB->insert('glpi_plugin_goals_configs', [
            'show_technicians' => 1
        ]);
    }

    return true;
}

/**
 * Uninstall hook
 *
 * @return boolean
 */
function plugin_goals_uninstall()
{
    global $DB;

    // Drop configuration table
    $DB->dropTable('glpi_plugin_goals_configs');

    return true;
}
