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

$config = new PluginGoalsConfig();

// Update configuration if form submitted
if (isset($_POST['update'])) {
    Session::checkRight("config", UPDATE);

    $updated = PluginGoalsConfig::updateConfig([
        'show_technicians' => $_POST['show_technicians']
    ]);

    Session::addMessageAfterRedirect('Configuración guardada exitosamente', false, INFO);
    Html::back();
} else {
    // Check permissions for display
    Session::checkRight("config", UPDATE);

    Html::header(
        'Logros',
        $_SERVER['PHP_SELF'],
        "config",
        "goals"
    );

    $config->showConfigForm();

    Html::footer();
}
