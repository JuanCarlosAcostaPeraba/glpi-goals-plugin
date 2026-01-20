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

// Check if user has update permission on config
Session::checkRight("config", UPDATE);

Html::header(
    __('Goals', 'goals'),
    $_SERVER['PHP_SELF'],
    "config",
    "goals"
);

// For now, just a placeholder as requested.
// Professionals skeletons usually show something even if static.
echo "<div class='center spaced-container'>";
echo "<h2>" . __('Goals Configuration', 'goals') . "</h2>";
echo "<p>" . __('No special configuration is required for this plugin yet.', 'goals') . "</p>";
echo "</div>";

Html::footer();
