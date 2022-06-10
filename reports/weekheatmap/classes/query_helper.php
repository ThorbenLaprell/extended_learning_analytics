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
 * Version info for the Course Dashboard
 *
 * @package     local_learning_analytics
 * @copyright   Lehr- und Forschungsgebiet Ingenieurhydrologie - RWTH Aachen University
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace elareport_weekheatmap;

defined('MOODLE_INTERNAL') || die();

class query_helper {

    public static function query_weekly_activity() : array {
        global $DB;
        $query = <<<SQL
        SELECT id
        FROM {elanalytics_reports} r
        WHERE r.name = 'weekheatmap'
SQL;
        $reportid = $DB->get_record_sql($query)->id;

        $startdate = new \DateTime();
        $lifetimeInWeeks = explode(':', get_config('local_extended_learning_analytics', 'lifetimeInWeeks'))[1];
        $startdate->modify('-' . $lifetimeInWeeks . ' weeks');
        $startdate->modify('Monday this week'); // Get start of week.
        $mondaytimestamp = $startdate->format('U');

        $query = <<<SQL
        SELECT (FLOOR((h.timecreated - {$mondaytimestamp}) / (7 * 60 * 60 * 24)) + 1)
        AS WEEK,
        SUM(SUBSTRING(h.input, 1, LOCATE(',', h.input)-1)) AS clicks
        FROM {elanalytics_history} h
        WHERE h.reportid = ?
        GROUP BY week
        ORDER BY week;
SQL;

        return $DB->get_records_sql($query, [$reportid]);
    }

    public static function query_activity_at_weekX($date) : array {
        global $DB;
        $timestamp = $date->getTimestamp();
        $endtimestamp = $timestamp + 7*86400;

        $query = <<<SQL
        SELECT (FLOOR((l.timecreated - {$timestamp}) / (60 * 60)))
        AS HOUR,
        COUNT(*) AS hits
        FROM {logstore_lanalytics_log} l
        WHERE l.timecreated >= ?
        AND l.timecreated < ?
        GROUP BY hour
        ORDER BY hour;
SQL;

        return $DB->get_records_sql($query, [$timestamp, $endtimestamp]);
    }
}
