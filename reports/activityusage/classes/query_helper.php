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

namespace elareport_activityusage;

defined('MOODLE_INTERNAL') || die();

class query_helper {

    public static function query_weekly_activity() : array {
        global $DB;

        $startdate = new \DateTime();
        $lifetimeInWeeks = get_config('local_extended_learning_analytics', 'lifetimeInWeeks');
        $startdate->modify('-' . $lifetimeInWeeks . ' weeks');
        $startdate->modify('Monday this week'); // Get start of week.

        $query = <<<SQL
        SELECT activityid AS activityid,
        moduleid AS moduleid,
        SUM(hits) AS hits
        FROM {elanalytics_activityusage}
        WHERE timecreated >= ?
        GROUP BY activityid
        ORDER BY hits DESC
SQL;

        return $DB->get_records_sql($query, [$startdate->getTimestamp()]);
    }

    public static function query_activity_at_dayX($date) : array {
        global $DB;
        $timestamp = $date->getTimestamp();
        $endtimestamp = $timestamp + 86400;

        $query = <<<SQL
        SELECT COUNT(*) AS hits,
        m.id AS id,
        m.module AS moduleid
        FROM {logstore_lanalytics_log} l
        JOIN {context} c
        ON l.contextid = c.id
        JOIN {course_modules} m
        ON c.instanceid = m.id
        WHERE l.timecreated >= ?
        AND l.timecreated < ?
        GROUP BY instanceid
        ORDER BY instanceid
SQL;

        return $DB->get_records_sql($query, [$timestamp, $endtimestamp]);
    }
}
