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

namespace elareport_eventstatistics;

defined('MOODLE_INTERNAL') || die();

class query_helper {

    public static function query_event_activity() : array {
        global $DB;

        $startdate = new \DateTime();
        $lifetimeInWeeks = get_config('local_extended_learning_analytics', 'lifetimeInWeeks');
        $startdate->modify('-' . $lifetimeInWeeks . ' weeks');
        $startdate->modify('Monday this week'); // Get start of week.

        $query = <<<SQL
        SELECT e.eventid AS eventid,
            le.eventname,
            SUM(e.hits) AS hits
        FROM {elanalytics_eventstatistics} e
        JOIN {logstore_lanalytics_evtname} le
        WHERE e.timecreated >= ?
        GROUP BY e.eventid
        ORDER BY hits DESC
SQL;

        return $DB->get_records_sql($query, [$startdate->getTimestamp()]);
    }

    public static function query_events($date) : array {
        global $DB;
        $timestamp = $date->getTimestamp();
        $endtimestamp = $timestamp + 86400;

        $query = <<<SQL
        SELECT l.eventid AS eventid, COUNT(*) AS hits
        FROM {logstore_lanalytics_log} l
        JOIN {logstore_lanalytics_evtname} e
        ON l.eventid = e.id
        WHERE l.timecreated >= ?
        AND l.timecreated < ?
        GROUP BY eventid
        ORDER BY hits DESC
SQL;

        return $DB->get_records_sql($query, [$timestamp, $endtimestamp]);
    }
}
