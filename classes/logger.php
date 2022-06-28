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
 * Version info for the Elanalytics Dashboard
 *
 * @package     local_extended_learning_analytics
 * @copyright   Lehr- und Forschungsgebiet Ingenieurhydrologie - RWTH Aachen University
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_extended_learning_analytics;

defined('MOODLE_INTERNAL') || die();

class logger {

    public static function run() {
        global $PAGE, $DB, $OUTPUT, $CFG;
        $reports = \core_plugin_manager::instance()->get_plugins_of_type('elareport');
        foreach ($reports as $report) {
            $logger = "{$CFG->dirroot}/local/extended_learning_analytics/reports/{$report->name}/classes/logger.php";
            if (file_exists($logger)) {
                include_once($logger);
                $loggerClass = "elareport_{$report->name}\\logger";
                $loggerClass::run();
            }
        }
    }
}
