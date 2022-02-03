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

use local_extended_learning_analytics\router;

require(__DIR__ . '/../../config.php');

defined('MOODLE_INTERNAL') || die;

require_login();

global $PAGE, $USER, $DB;

$courseid = 30;
$showtour = optional_param('tour', 0, PARAM_INT) === 1;
$context = context_course::instance($courseid, MUST_EXIST);

$PAGE->set_context($context);

// Set URL to main path of analytics.
$currentparams = ['course' => $courseid];
if ($showtour) {
    $currentparams = ['tour' => 1, 'course' => $courseid];
}
$url = new moodle_url('/local/extended_learning_analytics/index.php/reports/dashboard', $currentparams);
//var_dump($url);
$PAGE->set_url($url);
//var_dump($PAGE->url);

// For now, all statistics are shown on course level.
$course = get_course($courseid);
$PAGE->set_course($course);

// Header of page (we simply use the course name to be consitent with other pages)
$PAGE->set_pagelayout('course');
$PAGE->set_heading($course->fullname);

// title of page.
$coursename = format_string($course->fullname, true, array('context' => context_course::instance($course->id)));
$title = $coursename . ': ' . get_string('navigation_link', 'local_extended_learning_analytics');
$PAGE->set_title($title);

//var_dump($_SERVER['REQUEST_URI']);

$resultinghtml = router::run($_SERVER['REQUEST_URI'] . "/reports/dashboard?course=30");
//var_dump($resultinghtml);

$output = $PAGE->get_renderer('local_learning_analytics');

$PAGE->requires->css('/local/learning_analytics/static/styles.css?4');
$mainoutput = $output->render_from_template('local_learning_analytics/course', [
    'content' => $resultinghtml
]);
echo $output->header();
echo $mainoutput;
echo $output->footer();

//string(76) "/moodle/local/         learning_analytics/index.php/reports/coursedashboard?course=30"
//string(51) "/moodle/local/extended_learning_analytics/index.php" string(0) ""




    