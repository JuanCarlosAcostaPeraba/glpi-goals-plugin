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

define('PLUGIN_GOALS_VERSION', '1.0.0');

/**
 * Init the plugin of the array of plugins
 *
 * @return void
 */
function plugin_init_goals()
{
   global $PLUGIN_HOOKS;

   $PLUGIN_HOOKS['csrf_compliant']['goals'] = true;

   if (Plugin::isPluginActive('goals')) {
      Plugin::registerClass('PluginGoalsReport');
      Plugin::registerClass('PluginGoalsConfig');

      $PLUGIN_HOOKS['config_page']['goals'] = 'front/config.php';

      $PLUGIN_HOOKS['menu_toadd']['goals'] = [
         'tools' => 'PluginGoalsReport'
      ];

      $PLUGIN_HOOKS['page_header_asset']['goals'] = [
         'css' => ['public/css/goals.css']
      ];
   }
}

/**
 * Get the name and the version of the plugin
 *
 * @return array
 */
function plugin_version_goals()
{
   return [
      'name' => 'Goals',
      'version' => PLUGIN_GOALS_VERSION,
      'author' => 'Juan Carlos Acosta Peraba',
      'license' => 'GPLv2+',
      'homepage' => 'https://github.com/JuanCarlosAcostaPeraba/glpi-goals-plugin',
      'requirements' => [
         'glpi' => [
            'min' => '11.0',
            'max' => '12.0',
         ],
      ],
   ];
}

/**
 * Check pre-requisites before install
 *
 * @return boolean
 */
function plugin_goals_check_prerequisites()
{
   return true;
}

/**
 * Check configuration process
 *
 * @param boolean $verbose Whether to display message or not
 * @return boolean
 */
function plugin_goals_check_config($verbose = false)
{
   if (true) { // No special configuration required yet
      return true;
   }
   if ($verbose) {
      echo 'Instalado / no configurado';
   }
   return false;
}
