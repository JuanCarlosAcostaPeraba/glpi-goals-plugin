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

include("../../../inc/includes.php");

global $CFG_GLPI;

// Security checks
Session::checkLoginUser();
// You can add more specific permission checks here
// e.g., Session::checkRight('plugin_goals', READ);

Html::header(
    __('Goals', 'goals'),
    $CFG_GLPI['root_doc'] . "/plugins/goals/front/report.php",
    "tools",
    "goals"
);

$report = new PluginGoalsReport();

echo "<div class='spaced-container'>";
echo "<h1>" . __('HUC Goals - Informatics Department', 'goals') . "</h1>";

$report->showFilterForm();

if (isset($_POST['show_report'])) {
    echo "<hr/>";
    $report->handleAndDisplay($_POST);
}

echo "</div>";

Html::footer();
