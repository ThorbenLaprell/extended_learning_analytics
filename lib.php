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

defined('MOODLE_INTERNAL') || die();

// Callback to extend navigation.
/**
 * @param global_navigation  $nav {@link global_navigation}
 * @return void
 */
function local_extended_learning_analytics_extend_navigation(global_navigation $nav) {
    global $PAGE, $COURSE, $DB, $USER, $CFG;

    $courseid = $PAGE->course->id;
    $context = context_course::instance($courseid, MUST_EXIST);

    if (has_capability('local/extended_learning_analytics:view_statistics', $context, $USER->id)) {
        $coursenode = $PAGE->navigation->find($courseid, navigation_node::TYPE_COURSE);
        $thingnode = $coursenode->add(get_string('navigation_link', 'local_extended_learning_analytics'), new moodle_url('/local/extended_learning_analytics/index.php'));
        $thingnode->make_active();
    }

    /*$coursenode = $PAGE->navigation->find($PAGE->course->id, navigation_node::TYPE_COURSE);
    if ($coursenode === false) return;
    $branch = $coursenode->add('My custom branch');
    $url = new moodle_url($CFG->wwwroot.'/local/extended_learning_analytics/index.php', Array('id'=>$coursenode->key));
    $mynode = $branch->add('Sam Hemelryk', $url, 'Sam', null, navigation_node::TYPE_CUSTOM);
    $branch->forceopen = true;
    $mynode->add_class('mycustomclass');
    $mynode->make_active();

    $previewnode = $PAGE->navigation->add(get_string('navigation_link', 'local_extended_learning_analytics'), new moodle_url('/local/extended_learning_analytics/index.php'), navigation_node::TYPE_CONTAINER);
    $thingnode = $previewnode->add(get_string('navigation_link', 'local_extended_learning_analytics'), new moodle_url('/local/extended_learning_analytics/index.php'));
    $thingnode->make_active();*/

    /*$systemcontext = context_system::instance();
    $extended_learning_analytics_url = new moodle_url('/local/extended_learning_analytics/index.php');
    $main_node = $nav->add(get_string('navigation_link', 'local_extended_learning_analytics'),$extended_learning_analytics_url, navigation_node::TYPE_CONTAINER, null, 'extended_learning_analytics');
    $main_node->nodetype = 1;
    /*$courseid = required_param('course', PARAM_INT);
    $showtour = optional_param('tour', 0, PARAM_INT) === 1;
    $context = context_course::instance($courseid, MUST_EXIST);

    require_capability('local/extended_learning_analytics:view_statistics', $context, $USER->id);*/

    /*if (isset($COURSE->id) && $COURSE->id == SITEID) {
        $systemcontext = context_system::instance();
        $extended_learning_analytics_url = new moodle_url('/local/extended_learning_analytics/index.php');
        $main_node = $nav->add(get_string('navigation_link', 'local_extended_learning_analytics'),$extended_learning_analytics_url, navigation_node::TYPE_CONTAINER, null, 'extended_learning_analytics');
        $main_node->nodetype = 1;
    }*/
}

/*function local_extended_learning_analytics_extend_navigation(global_navigation $nav) {
    global $PAGE, $COURSE, $DB, $USER;

    $courseid = required_param('course', PARAM_INT);
    $showtour = optional_param('tour', 0, PARAM_INT) === 1;
    $context = context_course::instance($courseid, MUST_EXIST);

    //require_capability('local/extended_learning_analytics:view_statistics', $context, $USER->id);

    if (isset($COURSE->id) && $COURSE->id == SITEID) {

        $previewnode = $PAGE->navigation->add(get_string('preview'), new moodle_url('/local/extended_learning_analytics/index.php'), navigation_node::TYPE_CONTAINER);
        $thingnode = $previewnode->add(get_string('name of thing'), new moodle_url('/local/extended_learning_analytics/index.php'));
        $thingnode->make_active();

//$node = $nav->find($COURSE->id, navigation_node::TYPE_COURSE);

        /*$settingbeforekey = get_config('local_learning_analytics', 'navigation_position_beforekey');
        $beforekey = null;
        if ($settingbeforekey === false || $settingbeforekey === '') {
            // Find first section node, and add our node before that (to be the last non-section node)
            $children = $node->children->type(navigation_node::TYPE_SECTION);
            if (count($children) !== 0) {
                $beforekey = reset($children)->key;
            }
        } else { // use setting
            $beforekey = $settingbeforekey;
        }*/
        /*if ($node) {
            $node->add_node(navigation_node::create(
                    get_string('navigation_link', 'local_extended_learning_analytics'),
                    new moodle_url('/local/extended_learning_analytics/index.php'),
                    navigation_node::TYPE_CUSTOM,
                    null, 'extended_learning_analytics'
                ),
                143
            );
        }*/
    //}
//}
