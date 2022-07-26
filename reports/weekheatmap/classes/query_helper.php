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
 * Version details.
 *
 * @package     local_extended_learning_analytics
 * @copyright   Lehr- und Forschungsgebiet Ingenieurhydrologie - RWTH Aachen University
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace elareport_weekheatmap;

defined('MOODLE_INTERNAL') || die();

class query_helper {

    public static function query_weekly_activity() : array {
        global $DB;
        $query = <<<SQL
        SELECT CONCAT(date, "-", hour) AS heatpoint,
            SUM(hits) AS value
        FROM {elanalytics_weekheatmap}
        GROUP BY heatpoint
        ORDER BY heatpoint
SQL;

        return $DB->get_records_sql($query, []);
    }

    public static function query_activity_at_weekX($date) : array {
        global $DB, $CFG;
        $timestamp = $date->getTimestamp();
        $endtimestamp = $timestamp + (7*86400);

        $weekstatement = "FROM_UNIXTIME(l.timecreated, '%w-%k')";

        if ($CFG->dbtype === 'pgsql') {
            $date = new DateTime();        
            $timezone = $date->getTimezone()->getName();
            $weekstatement = "TO_CHAR(TO_TIMESTAMP(l.timecreated) at time zone '".$timezone."', 'D-HH24')";
        }

        $query = <<<SQL
        SELECT
            {$weekstatement} AS heatpoint,
            COUNT(1) AS value
        FROM {logstore_lanalytics_log} AS l
        WHERE l.timecreated >= ?
        AND l.timecreated < ?
        GROUP BY heatpoint
        ORDER BY heatpoint
SQL;

        return $DB->get_records_sql($query, [$timestamp, $endtimestamp]);
    }
}
