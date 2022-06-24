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

namespace elareport_forumusage;

defined('MOODLE_INTERNAL') || die();

use elareport_forumusage\query_helper;
use stdClass;

class logger {

    public static function run() {
        global $DB;
        try {
            $begindate = new \DateTime();
            $begindate->modify('today');
            $begindate->format('Ymd');
            $lifetimeInWeeks = get_config('local_extended_learning_analytics', 'lifetimeInWeeks');
            $begindate->modify('-' . $lifetimeInWeeks . ' weeks');
            if($DB->record_exists('elanalytics_forumusage', array())) {
                $inputs = $DB->get_records('elanalytics_forumusage');
                $max = self::findMaxDate($inputs);
                $begindate = new \DateTime($max);
            }
            self::query_and_save_from_date_to_today($begindate);
        } catch (Exception $e) {
            return 'catch';
        }
    }

    public static function findMaxDate($dates) {
        $max = 0;
        foreach ($dates as $date) {
            $max = max($max, $date->date);
        }
        return $max;
    }

    //saves the number of hits globally for each day between timestamps and now
    public static function query_and_save_from_date_to_today($startdate) {
        $end = new \DateTime();
        $end->modify('today');
        $end->modify('+1 day');
        $interval = new \DateInterval('P1D');
        $daterange = new \DatePeriod($startdate, $interval ,$end);
        foreach($daterange as $date){
            self::query_and_save_dayX($date);
        }
    }

    //saves the number of hits globally for the day which starts with date
    public static function query_and_save_dayX($date) {
        global $DB;
        $formatedDate = $date->format('Ymd');
        $entry = new stdClass();
        $entry->timecreated = $date->getTimestamp()+43200;
        $entry->date = $formatedDate;
        $resolves = query_helper::query_activity_at_dayX($date);
        foreach($resolves as $resolve) {
            if($resolve) {
                $entry->activityid = $resolve->id;
                $entry->moduleid = $resolve->moduleid;
                $entry->hits = $resolve->hits;
                self::insert_or_update($entry);
            }
        }
    }

    public static function insert_or_update($entry) {
        global $DB;
        $query = <<<SQL
        SELECT *
        FROM {elanalytics_forumusage} h
        WHERE h.date = ?
        AND h.activityid = ?
SQL;
        $record = $DB->get_record_sql($query, [$entry->date, $entry->activityid]);
        if($record != false) {
            $entry->id = $record->id;
            $DB->update_record('elanalytics_forumusage', $entry);
        } else {
            $DB->insert_record('elanalytics_forumusage', $entry);
        }
    }
}