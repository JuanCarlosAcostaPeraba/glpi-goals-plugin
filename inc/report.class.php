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

class PluginGoalsReport extends CommonGLPI
{

    public static function getTypeName($nb = 0)
    {
        return __('Goals Report', 'goals');
    }

    public static function canView(): bool
    {
        return Session::haveRight('config', READ);
    }

    public static function getMenuName()
    {
        return __('Goals', 'goals');
    }

    public static function getMenuContent()
    {
        return [
            'title' => self::getMenuName(),
            'page' => '/plugins/goals/front/report.php',
            'icon' => 'fas fa-bullseye',
        ];
    }

    /**
     * Display the filter form
     *
     * @return void
     */
    public function showFilterForm()
    {
        global $CFG_GLPI;

        echo "<div class='center'>";
        echo "<form method='post' action='" . $_SERVER['PHP_SELF'] . "'>";
        echo "<table class='tab_cadre_fixe'>";
        echo "<tr><th colspan='4'>" . __('Filter achievements', 'goals') . "</th></tr>";

        echo "<tr class='tab_bg_1'>";
        echo "<td>" . __('Date from', 'goals') . "</td>";
        echo "<td>";
        Html::showDateField('date_from', ['value' => date('Y-01-01')]);
        echo "</td>";
        echo "<td>" . __('Date to', 'goals') . "</td>";
        echo "<td>";
        Html::showDateField('date_to', ['value' => date('Y-m-d')]);
        echo "</td>";
        echo "</tr>";

        echo "<tr class='tab_bg_1'>";
        echo "<td>" . __('Group', 'goals') . "</td>";
        echo "<td>";
        Group::dropdown(['name' => 'groups_id']);
        echo "</td>";
        echo "<td colspan='2'></td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td colspan='4' class='center'>";
        echo "<input type='submit' name='show_report' value=\"" . _sx('button', 'Show') . "\" class='btn btn-primary'>";
        echo "</td>";
        echo "</tr>";

        echo "</table>";
        Html::closeForm();
        echo "</div>";
    }

    /**
     * Handle form submission and display results
     *
     * @param array $post
     * @return void
     */
    public function handleAndDisplay($post)
    {
        if (!isset($post['show_report'])) {
            return;
        }

        $date_from = $post['date_from'] ?? date('Y-01-01');
        $date_to = $post['date_to'] ?? date('Y-m-d');
        $groups_id = $post['groups_id'] ?? 0;

        $results = $this->fetchAchievements($date_from, $date_to, $groups_id);

        echo "<div class='center'>";
        echo "<table class='tab_cadre_fixehov'>";
        echo "<tr>";
        echo "<th>" . __('Technician', 'goals') . "</th>";
        echo "<th>" . __('Tasks Done', 'goals') . "</th>";
        echo "</tr>";

        foreach ($results as $row) {
            $style = ($row['tecnico'] === 'TOTAL') ? "style='font-weight:bold; background-color:#f0f0f0;'" : "";
            echo "<tr class='tab_bg_1' $style>";
            echo "<td>" . $row['tecnico'] . "</td>";
            echo "<td>" . $row['tareas_hechas'] . "</td>";
            echo "</tr>";
        }

        echo "</table>";
        echo "</div>";

        echo "</div>";
    }

    public function fetchAchievements($dateFrom, $dateTo, $groups_id)
    {
        global $DB;

        $start = $dateFrom . " 00:00:00";
        $end = $dateTo . " 23:59:59";

        $join_group = "";
        $group_condition = "";
        if ($groups_id > 0) {
            $join_group = "JOIN glpi_groups_users gu ON gu.users_id = u.id";
            $group_condition = "AND gu.groups_id = '$groups_id'";
        }

        $query = "
         SELECT
            u.name AS tecnico,
            COUNT(DISTINCT tt.id) AS tareas_hechas
         FROM glpi_tickettasks tt
         JOIN glpi_users u ON u.id = tt.users_id
         $join_group
         JOIN glpi_tickets t ON t.id = tt.tickets_id
         LEFT JOIN glpi_itilcategories c ON c.id = t.itilcategories_id
         WHERE tt.state = 2
            $group_condition
            AND tt.date_mod >= '$start'
            AND tt.date_mod <= '$end'
            AND (c.name IS NULL OR c.name <> 'Importados desde Track-It')
         GROUP BY u.name
         
         UNION ALL
         
         SELECT
            'TOTAL' AS tecnico,
            COUNT(DISTINCT tt.id) AS tareas_hechas
         FROM glpi_tickettasks tt
         JOIN glpi_users u ON u.id = tt.users_id
         $join_group
         JOIN glpi_tickets t ON t.id = tt.tickets_id
         LEFT JOIN glpi_itilcategories c ON c.id = t.itilcategories_id
         WHERE tt.state = 2
            $group_condition
            AND tt.date_mod >= '$start'
            AND tt.date_mod <= '$end'
            AND (c.name IS NULL OR c.name <> 'Importados desde Track-It')
      ";

        $iterator = $DB->request($query);
        $results = [];
        foreach ($iterator as $row) {
            $results[] = $row;
        }

        return $results;
    }
}
