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

require(__DIR__ . '/../../config.php');
require(__DIR__ . '/../learning_analytics/testfunction.php');
require(__DIR__ . '/reports/extended_learning_analyticsreport_coursedashboard.php');

defined('MOODLE_INTERNAL') || die;

require_login();
var_dump(testreturn());
$extended_learning_analytics = new extended_learning_analytics_coursedashboard();
var_dump($extended_learning_analytics->activiyoverweeks());
/*
global $PAGE, $USER, $DB;

$courseid = $PAGE->course->id;
$context = context_course::instance($courseid, MUST_EXIST);

require_capability('local/extern:view_statistics', $context, $USER->id);

global $PAGE, $USER, $DB;


$time = time();

//994 Sekunden => 3.790.979.310
$query = <<<SQL
    SELECT COUNT(*) AS hits
    FROM {logstore_lanalytics_log} l
    JOIN {course} c
    ON c.id = l.courseid
    JOIN {context} ctx
    ON ctx.id = l.contextid
    JOIN {context} ctx2
    ON ctx2.contextlevel = ctx.contextlevel
SQL;

//48 Sekunden 179.434.566
$shortquery = <<<SQL
    SELECT COUNT(*) AS hits
    FROM {logstore_lanalytics_log} l
    JOIN {course} c
    ON c.id = l.courseid
    JOIN {context} ctx
    ON ctx.id = l.contextid
    JOIN {context} ctx2
    ON ctx2.contextlevel = ctx.contextlevel
    WHERE l.id LIKE "5%"
    AND ctx.path LIKE "%1/%"
    AND c.fullname LIKE "%asser%"
SQL;

//$result = array_pop($DB->get_records_sql($shortquery, []))->hits;

$PAGE->set_url(new moodle_url('/local/extend_learning_analytics/index.php', array('key' => 'value', 'id' => 3)));
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('report');
$PAGE->set_title('Extend Learning Analytics');
$PAGE->set_heading(get_string('pluginname', 'local_extend_learning_analytics'));
$output = $PAGE->get_renderer('local_extend_learning_analytics');
echo $output->header();
//echo $result . " hits in ";
echo time() - $time . " seconds.";
echo $output->footer();