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

namespace elareport_courseusage;

defined('MOODLE_INTERNAL') || die();

class query_helper {

    public static function query_course_activity() : array {
        global $DB;

        $startdate = new \DateTime();
        $lifetimeInWeeks = get_config('local_extended_learning_analytics', 'lifetimeInWeeks');
        $startdate->modify('-' . $lifetimeInWeeks . ' weeks');
        $startdate->modify('Monday this week'); // Get start of week.

        $query = <<<SQL
        SELECT courseid AS courseid,
            SUM(hits) AS hits
        FROM {elanalytics_courseusage}
        WHERE timecreated >= ?
        GROUP BY courseid
        ORDER BY hits DESC
SQL;

        return $DB->get_records_sql($query, [$startdate->getTimestamp()]);
    }

    public static function query_course_category_activity() : array {
        global $DB;

        $startdate = new \DateTime();
        $lifetimeInWeeks = get_config('local_extended_learning_analytics', 'lifetimeInWeeks');
        $startdate->modify('-' . $lifetimeInWeeks . ' weeks');
        $startdate->modify('Monday this week'); // Get start of week.

        $query = <<<SQL
        SELECT cc.id, SUM(ec.hits) AS hits
        FROM {course_categories} cc
        JOIN {course} c
        ON cc.id = c.category
        JOIN {elanalytics_courseusage} ec
        ON c.id = ec.courseid
        WHERE cc.depth = 1
        GROUP BY cc.id
        ORDER BY hits DESC
SQL;

        return $DB->get_records_sql($query, [$startdate->getTimestamp()]);
    }

    public static function query_activity_at_dayXInCourse($date, $courseid) : array {
        global $DB;
        $timestamp = $date->getTimestamp();
        $endtimestamp = $timestamp + 86400;

        $query = <<<SQL
        SELECT COUNT(*) hits
        FROM {logstore_lanalytics_log} l
        WHERE l.timecreated >= ?
        AND l.timecreated < ?
        AND l.courseid = ?
SQL;

        return $DB->get_records_sql($query, [$timestamp, $endtimestamp, $courseid]);
    }
}
