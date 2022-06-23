<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Version info for the Sections report
 *
 * @package     local_learning_analytics
 * @copyright   Lehr- und Forschungsgebiet Ingenieurhydrologie - RWTH Aachen University
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use \local_extended_learning_analytics\report_base;
use \local_extended_learning_analytics\logger;

class elareport_dashboard extends report_base {

    public function run(): array {
        global $PAGE, $DB, $OUTPUT, $CFG;
        $PAGE->requires->css('/local/learning_analytics/reports/coursedashboard/static/styles.css?3');

        $previewboxes = get_config('local_extended_learning_analytics', 'dashboard_boxes');
        $splitpreviewkeys = explode(',', $previewboxes);

        $subpluginsboxes = [];

        //$logger = new logger();
        //$logger->run();

        foreach ($splitpreviewkeys as $plugininfo) {
            $pluginsplit = explode(':', $plugininfo);
            $pluginkey = $pluginsplit[0];
            $pluginsize = 12;
            $previewfile = "{$CFG->dirroot}/local/extended_learning_analytics/reports/{$pluginkey}/classes/preview.php";
            if (file_exists($previewfile)) {
                include_once($previewfile);
                $previewClass = "elareport_{$pluginkey}\\preview";
                $subpluginsboxes = array_merge($subpluginsboxes, ["<div class='col-lg-{$pluginsize}'>"], $previewClass::content(), ["</div>"]);
            }
        }

        return array_merge(
            [self::heading(get_string('pluginname', 'elareport_dashboard'), true)],
            ["<div class='row reportboxes'>"],
            $subpluginsboxes,
            ["</div>"]
        );
    }

}