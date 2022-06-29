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

use local_extended_learning_analytics\dashboard;
use \local_extended_learning_analytics\logger;
use \local_extended_learning_analytics\cleaner;

require(__DIR__ . '/../../config.php');

defined('MOODLE_INTERNAL') || die;

require_login();

global $PAGE, $DB, $OUTPUT, $CFG;

$url = new moodle_url('/local/extended_learning_analytics/index.php/reports/dashboard');
$PAGE->set_url($url);

// Header of page (we simply use the course name to be consitent with other pages)
$PAGE->set_pagelayout('report');

// title of page.
$title = get_string('navigation_link', 'local_extended_learning_analytics');
$PAGE->set_title($title);

$output = $PAGE->get_renderer('local_learning_analytics');

$PAGE->requires->css('/local/learning_analytics/static/styles.css?4');

$PAGE->requires->css('/local/learning_analytics/reports/coursedashboard/static/styles.css?3');

$subpluginsboxes = [];

$logger = new logger(); //TO BE REMOVED
$logger->run();
$cleaner = new cleaner(); //TO BE REMOVED
$cleaner->run();

$reports = \core_plugin_manager::instance()->get_plugins_of_type('elareport');
foreach ($reports as $report) {
    $pluginsize = 12;
    $previewfile = "{$CFG->dirroot}/local/extended_learning_analytics/reports/{$report->name}/classes/preview.php";
    if (file_exists($previewfile)) {
        include_once($previewfile);
        $previewClass = "elareport_{$report->name}\\preview";
        $subpluginsboxes = array_merge($subpluginsboxes, ["<div class='col-lg-{$pluginsize}'>"], $previewClass::content(), ["</div>"]);
    }
}

$ret = "<div class='container-fluid'>";
$renderer = $PAGE->get_renderer('local_learning_analytics');
$ret .= $renderer->render_output_list($subpluginsboxes);
$ret .= "</div>";

echo $output->header();
echo $ret;
echo $output->footer();




    