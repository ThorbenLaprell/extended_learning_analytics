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

namespace elareport_trendchart;

defined('MOODLE_INTERNAL') || die();

class query_helper {

    public static function preview_hits_per_learner_in_last_seven_days(int $courseid) : array {
        global $DB;

        $course = get_course($courseid);

        $now = date("Y-m-d H:i:s");

        $nowtimestamp = time();

        $query = <<<SQL
        SELECT COUNT(*) / ? As hitsPerLearner
        FROM {logstore_lanalytics_log} l
        WHERE l.courseid = ?
        AND l.timecreated > ?
SQL;

        $enrols = $DB->get_records('enrol', array('courseid'=>$courseid));
        $enrolids = [];
        foreach($enrols as $enrol) {
            array_push($enrolids, (int)$enrol->id);
        }
        $peopleincourse = [];
        foreach($enrolids as $enrol){
            $helper = $DB->get_records('user_enrolments', array('enrolid'=>$enrol));
            foreach($helper as $user){
                array_push($peopleincourse, (int)$user->userid);
            }
        }
        $studentrole = (int)$DB->get_record('role', array('shortname'=>'student'))->id;
        $students = [];
        $helper = $DB->get_records('role_assignments', array('roleid'=>$studentrole));
        foreach($helper as $user){
            array_push($students, (int)$user->userid);
        }
        $onlystudents = array_intersect($peopleincourse, $students);
        $studentcount = count($onlystudents);
        return $DB->get_records_sql($query, [$studentcount, $courseid, $nowtimestamp-7*24*60*60]);
    }

}
