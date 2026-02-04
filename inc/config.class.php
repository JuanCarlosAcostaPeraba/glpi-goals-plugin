<?php

class PluginGoalsConfig extends CommonDBTM
{
    public static function getTypeName($nb = 0)
    {
        return 'Configuración de Logros';
    }

    public function showConfigForm()
    {
        global $DB;

        $config = $DB->request([
            'FROM' => 'glpi_plugin_goals_configs',
            'WHERE' => ['id' => 1]
        ])->current();

        echo "<div class='center'>";
        echo "<form method='post' action='config.php'>";
        echo "<input type='hidden' name='_glpi_csrf_token' value='" . Session::getNewCSRFToken() . "'>";
        echo "<table class='tab_cadre_fixe'>";
        echo "<tr><th colspan='2'>Ajustes</th></tr>";

        echo "<tr class='tab_bg_1'>";
        echo "<td>Mostrar técnicos en los resultados</td>";
        echo "<td>";
        Dropdown::showYesNo('show_technicians', $config['show_technicians'] ?? 1);
        echo "</td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td colspan='2' class='center'>";
        echo "<input type='submit' name='update' value='Guardar' class='btn btn-primary'>";
        echo "</td>";
        echo "</tr>";

        echo "</table>";
        Html::closeForm();
        echo "</div>";
    }

    public static function updateConfig(array $data)
    {
        global $DB;

        return $DB->update('glpi_plugin_goals_configs', $data, ['id' => 1]);
    }
}
