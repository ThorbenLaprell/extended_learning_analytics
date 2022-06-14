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

namespace elareport_usagestatistics;

defined('MOODLE_INTERNAL') || die();

use elareport_usagestatistics\query_helper;
use stdClass;

class logger {

    public static function run() {
        global $DB;
        $query = <<<SQL
        SELECT id
        FROM {elanalytics_reports} r
        WHERE r.name = 'usagestatistics'
SQL;
        try {
            $reportid = $DB->get_record_sql($query)->id;
            $begindate = new \DateTime();
            $begindate->modify('today');
            $begindate->format('Ymd');
            $lifetimeInWeeks = explode(':', get_config('local_extended_learning_analytics', 'lifetimeInWeeks'))[1];
            $begindate->modify('-' . $lifetimeInWeeks . ' weeks');
            if($DB->record_exists('elanalytics_history', array('reportid' => $reportid))) {
                $inputs = $DB->get_records('elanalytics_history', array('reportid' => $reportid));
                $max = self::findMaxDate($inputs);
                $begindate = new \DateTime($max);
            }
            self::query_and_save_from_date_to_today($begindate, $reportid);
        } catch (Exception $e) {
            return 'catch';
        }
    }

    public static function makeInsertText($hits, $weekday) {
        return $weekday . "," . $hits;
    }

    public static function returnInputTextAsVars($inputtext) {
        return explode(',', $inputtext);
    }

    public static function findMaxDate($inputs) {
        $max = 0;
        foreach ($inputs as $input) {
            $max = max($max, self::returnInputTextAsVars($input->input)[0]);
        }
        return $max;
    }

    //saves the number of hits globally for each day between timestamps and now
    public static function query_and_save_from_date_to_today($startdate, $reportid) {
        $end = new \DateTime();
        $end->modify('today');
        $end->modify('+1 day');
        $interval = new \DateInterval('P1D');
        $daterange = new \DatePeriod($startdate, $interval ,$end);
        foreach($daterange as $date){
            self::query_and_save_dayX($date, $reportid);
        }
    }

    //saves the number of hits globally for the day which starts with date
    public static function query_and_save_dayX($date, $reportid) {
        global $DB;
        $queryreturn = query_helper::query_activity_at_dayX($date);
        $firstProp = current( (Array)$queryreturn );
        $hits = (int)$firstProp->hits;
        $inserttext = self::makeInsertText($hits, $date->format('Ymd'));
        $entry = new stdClass();
        $entry->reportid = $reportid;
        $entry->timecreated = $date->getTimestamp()+43200;
        $entry->input = $inserttext;
        self::insert_or_update($entry, $date, $reportid);
    }

    public static function insert_or_update($entry, $date, $reportid) {
        global $DB;
        $query = <<<SQL
        SELECT *
        FROM {elanalytics_history} h
        WHERE h.input LIKE ?
        AND h.reportid = ?
SQL;
        $questionmark = explode(',', $date->format('Ymd'))[0] . "%";
        $record = $DB->get_record_sql($query, [$questionmark, $reportid]);
        if($record != false) {
            $entry->id = $record->id;
            $DB->update_record('elanalytics_history', $entry);
        } else {
            $DB->insert_record('elanalytics_history', $entry);
        }
    }
}