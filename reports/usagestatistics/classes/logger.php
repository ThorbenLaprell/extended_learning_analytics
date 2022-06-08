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
        $reportid = $DB->get_record('elanalytics_reports', array('name' => 'usagestatistics'))->id;
        if($DB->record_exists('elanalytics_history', array('reportid' => $reportid))) {
            $inputs = $DB->get_records('elanalytics_history', array('reportid' => $reportid))->input;
            $max = findMaxDate($inputs);
        }
        var_dump($reportid);
    }

    public static function makeInsertText($hits, $weekday) {
        return $hits . "," . $weekday;
    }

    public static function returnInputTextAsVars($inputtext) {
        return explode(',', $inputtext);
    }

    public static function findMaxDate($inputs) {
        $max = 0;
        foreach ($inputs as $input) {
            $max = max($max, returnInputTextAsVars($input)[1]);
        }
        return $max;
    }

    //saves the number of hits globally for the day which starts with date
    public static function query_and_save_dayX($date) {
        global $DB;
        $queryreturn = query_helper::query_activity_at_dayX($date);
        $firstProp = current( (Array)$queryreturn );
        $hits = (int)$firstProp->hits;
        $record = new stdClass();
        $record->timecreated = (new \DateTime())->getTimestamp();
        $record->hits = $hits;
        $record->date = $date->format('Ymd');
        insert_or_update($record);
    }
    
    //saves the number of hits globally for each day between these timestamps
    public static function query_and_save_from_date_to_date($startdate, $enddate) {
        $begin = new \DateTime($startdate);
        $end = new \DateTime($enddate);
        $end->modify( '+1 day' );
        $interval = new \DateInterval('P1D');
        $daterange = new \DatePeriod($begin, $interval ,$end);
        foreach($daterange as $date){
            query_and_save_dayX($date);
        }
    }

    //saves the number of hits globally for each day between timestamps and now
    public static function query_and_save_from_date_to_date($startdate) {
        $end = new \DateTime($enddate);
        $end->modify( '+1 day' );
        $interval = new \DateInterval('P1D');
        $daterange = new \DatePeriod($startdate, $interval ,$end);
        foreach($daterange as $date){
            query_and_save_dayX($date);
        }
    }

    //saves the number of hits globally for today
    public static function query_and_save_today() {
        global $DB;
        $time = new \DateTime();
        $time->modify('today');
        $queryreturn = query_helper::query_activity_from_date_till_now($time);
        $firstProp = current( (Array)$queryreturn );
        $hits = (int)$firstProp->hits;
        $record = new stdClass();
        $record->timecreated = (new \DateTime())->getTimestamp();
        $record->hits = $hits;
        $record->date = $time->format('Ymd');
        insert_or_update($record);
    }

    public static function insert_or_update($entry) {
        global $DB;
        if($DB->record_exists('elanalytics_history', array('date'=>$entry->date))) {
            $recordwithtimecreated = $DB->get_field('elanalytics_history_dashb', 'id', array('date'=>$entry->date));
            $entry->id = $recordwithtimecreated;
        }
        $DB->update_record('elanalytics_history_dashb', $entry);
    }

    public static function insert_if_not_existing($entry) {
        global $DB;
        if($DB->record_exists('elanalytics_history_dashb', array('date'=>$entry->date))) {
            return;
        } else {
            $DB->insert_record('elanalytics_history_dashb', $entry);
        }
    }
}