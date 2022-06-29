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

class cleaner {

    public static function run() {
        global $PAGE, $DB, $OUTPUT, $CFG;
        $reports = $DB->get_tables();
        $begindate = new \DateTime();
        $begindate->modify('today');
        $lifetimeInWeeks = get_config('local_extended_learning_analytics', 'lifetimeInWeeks');
        $begindate->modify('-' . $lifetimeInWeeks . ' weeks');
        $cutoffdate = $begindate->getTimestamp();
        foreach ($reports as $report) {
            if(substr($report, 0, 12) == 'elanalytics_') {
                $DB->delete_records_select($report, "timecreated < " . $cutoffdate);
            }
        }
    }
}
