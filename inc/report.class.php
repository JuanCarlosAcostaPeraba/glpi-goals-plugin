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
        return Session::haveRight('ticket', READ);
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
        echo "<form method='post' action='" . $CFG_GLPI['root_doc'] . "/plugins/goals/front/report.php'>";
        echo "<input type='hidden' name='_glpi_csrf_token' value='" . Session::getNewCSRFToken() . "'>";
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
        echo "<td>" . __('Group') . "</td>";
        echo "<td>";
        Group::dropdown([
            'name' => 'groups_id_helper',
            'display_emptychoice' => true,
            'on_change' => 'loadTechniciansFromGroup(this.value)'
        ]);
        echo "</td>";
        echo "<td>" . __('Technician', 'goals') . "</td>";
        echo "<td>";

        $users_id = [];

        User::dropdown([
            'name' => 'users_id[]', // Important for multiple selection
            'multiple' => true,
            'value' => $users_id,
            'right' => 'all',
            'rand' => 'users_id_field'
        ]);

        $ajax_url = $CFG_GLPI['root_doc'] . "/plugins/goals/front/ajax_members.php";
        echo Html::scriptBlock("
            function loadTechniciansFromGroup(groupId) {
                if (!groupId || groupId <= 0) return;
                
                fetch('$ajax_url?groups_id=' + groupId)
                .then(response => response.json())
                .then(users => {
                    const selectElement = $('[name=\"users_id[]\"]');
                    users.forEach(user => {
                        // Check if already exists to avoid duplicates
                        if (selectElement.find(\"option[value='\" + user.id + \"']\").length === 0) {
                            const newOption = new Option(user.text, user.id, true, true);
                            selectElement.append(newOption);
                        } else {
                            // Just ensure it's selected if it exists
                            const existingOptions = selectElement.val() || [];
                            if (!existingOptions.includes(user.id.toString())) {
                                existingOptions.push(user.id.toString());
                                selectElement.val(existingOptions);
                            }
                        }
                    });
                    selectElement.trigger('change');
                })
                .catch(error => console.error('Error fetching group members:', error));
            }
        ");

        echo "</td>";
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
        $users_id = $post['users_id'] ?? [];

        $results = $this->fetchAchievements($date_from, $date_to, $users_id);

        if (empty($results) || (count($results) === 1 && $results[0]['tecnico'] === 'TOTAL' && $results[0]['tareas_hechas'] == 0)) {
            echo "<div class='center'><div class='warning box'><i class='fas fa-exclamation-triangle'></i> " . __('No results found for the selected criteria.', 'goals') . "</div></div>";
            return;
        }

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
            echo "<td>" . ($row['tareas_hechas'] ?? 0) . "</td>";
            echo "</tr>";
        }

        echo "</table>";

        // Debug SQL (only for super-admins or for this dev phase)
        if (defined('GLPI_DEBUG_MODE') && GLPI_DEBUG_MODE) {
            // We can reconstructed the query roughly or just note to check debug log
            echo "<div class='center'><small>Check GLPI SQL Logs for specific QueryBuilder output</small></div>";
        }

        echo "</div>";
    }

    /**
     * Fetch achievements using the provided SQL logic
     *
     * @param string $dateFrom
     * @param string $dateTo
     * @param int $users_id
     * @return array
     */
    public function fetchAchievements($dateFrom, $dateTo, $users_id)
    {
        global $DB;

        $start = $dateFrom . " 00:00:00";
        $end = $dateTo . " 23:59:59";

        $criteria = [
            'SELECT' => [
                'glpi_users.name AS tecnico',
                new \Glpi\DBAL\QueryExpression('COUNT(*) AS tareas_hechas')
            ],
            'FROM' => 'glpi_tickettasks',
            'INNER JOIN' => [
                'glpi_users' => [
                    'ON' => [
                        'glpi_users' => 'id',
                        'glpi_tickettasks' => 'users_id'
                    ]
                ],
                'glpi_tickets' => [
                    'ON' => [
                        'glpi_tickets' => 'id',
                        'glpi_tickettasks' => 'tickets_id'
                    ]
                ]
            ],
            'LEFT JOIN' => [
                'glpi_itilcategories' => [
                    'ON' => [
                        'glpi_itilcategories' => 'id',
                        'glpi_tickets' => 'itilcategories_id'
                    ]
                ]
            ],
            'WHERE' => [
                'glpi_tickettasks.state' => 2,
                ['glpi_tickettasks.date_mod' => ['>=', $start]],
                ['glpi_tickettasks.date_mod' => ['<=', $end]]
            ],
            'GROUPBY' => 'glpi_users.name'
        ];

        // Add category filter separately
        $criteria['WHERE'][] = [
            'OR' => [
                'glpi_itilcategories.name' => null,
                ['glpi_itilcategories.name' => ['<>', 'Importados desde Track-It']]
            ]
        ];

        if (!empty($users_id)) {
            $criteria['WHERE']['glpi_tickettasks.users_id'] = $users_id;
        }

        $iterator = $DB->request($criteria);
        $results = [];
        $total_tasks = 0;

        foreach ($iterator as $row) {
            $results[] = [
                'tecnico' => $row['tecnico'],
                'tareas_hechas' => $row['tareas_hechas']
            ];
            $total_tasks += (int) $row['tareas_hechas'];
        }

        // Add TOTAL row at the end
        if (count($results) > 0) {
            $results[] = [
                'tecnico' => 'TOTAL',
                'tareas_hechas' => $total_tasks
            ];
        }

        return $results;
    }
}
