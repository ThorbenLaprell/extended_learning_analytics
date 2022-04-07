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

namespace elareport_dashboard;

defined('MOODLE_INTERNAL') || die();

use elareport_dashboard\query_helper;
use stdClass;

class cronlogger {

    //saves the number of hits globally for the day which starts with timestamp
    public static function query_and_save_dayX($timestamp) {
        global $DB;
        $queryreturn = query_helper::query_activity_at_dayX($timestamp);
        $firstProp = current( (Array)$queryreturn );
        $hits = (int)$firstProp->hits;
        $record = new stdClass();
        $record->timecreated = $timestamp+86399;
        $record->hits = $hits;
        $DB->insert_record('elanalytics_history_dashb', $record);
    }
    
    //saves the number of hits globally for each day between these timestamps
    public static function query_and_save_from_date_to_date($startdate, $enddate) {
        $begin = new \DateTime($startdate);
        $end = new \DateTime($enddate);
        $end->modify( '+1 day' );
        $interval = new \DateInterval('P1D');
        $daterange = new \DatePeriod($begin, $interval ,$end);
        foreach($daterange as $date){
            query_and_save_dayX($date->getTimestamp());
        }
    }

    //saves the number of hits globally for today
    public static function query_and_save_today() {
        global $DB;
        $time = new \DateTime();
        $time->modify('today');
        $timestamp = $time->getTimestamp();
        $queryreturn = query_helper::query_activity_from_timestamp_till_now($timestamp);
        $firstProp = current( (Array)$queryreturn );
        $hits = (int)$firstProp->hits;
        $record = new stdClass();
        $record->timecreated = $timestamp+86399;
        $record->hits = $hits;
        $DB->insert_record('elanalytics_history_dashb', $record);
    }

    public static function insert_or_update($entry) {
        global $DB;
        if($DB->record_exists('elanalytics_history_dashb', array('timecreated'=>$entry->timecreated))) {
            $recordwithtimecreated = $DB->get_field('elanalytics_history_dashb', 'id', array('timecreated'=>$entry->timecreated));
            $entry->id = $recordwithtimecreated;
        }
        $DB->insert_record('elanalytics_history_dashb', $entry);
    }

    public static function insert_if_not_existing($entry) {
        global $DB;
        if($DB->record_exists('elanalytics_history_dashb', array('timecreated'=>$entry->timecreated))) {
            return;
        } else {
            $DB->insert_record('elanalytics_history_dashb', $entry);
        }
    }
}
