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

// Include GLPI core
include("../../../inc/includes.php");

// Send JSON header
header("Content-Type: application/json; charset=UTF-8");

// Security check
if (!Session::haveRight('config', READ)) {
    http_response_code(403);
    echo json_encode(['error' => 'Permission denied']);
    exit();
}

$groups_id = $_GET['groups_id'] ?? 0;

if ($groups_id <= 0) {
    echo json_encode([]);
    exit();
}

global $DB;

$iterator = $DB->request([
    'SELECT' => ['glpi_users.id', 'glpi_users.name', 'glpi_users.realname', 'glpi_users.firstname'],
    'FROM' => 'glpi_users',
    'INNER JOIN' => [
        'glpi_groups_users' => [
            'ON' => [
                'glpi_groups_users' => 'users_id',
                'glpi_users' => 'id'
            ]
        ]
    ],
    'WHERE' => [
        'glpi_groups_users.groups_id' => $groups_id
    ],
    'ORDER' => 'glpi_users.name'
]);

$users = [];
foreach ($iterator as $user) {
    $displayName = $user['name'];
    if (!empty($user['realname']) || !empty($user['firstname'])) {
        $displayName = trim($user['firstname'] . ' ' . $user['realname']);
    }
    $users[] = [
        'id' => $user['id'],
        'text' => $displayName
    ];
}

echo json_encode($users);
