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
 * Strings for component 'tool_uploadcoursecategory', language 'en', branch 'MOODLE_22_STABLE'
 *
 * @package    local
 * @subpackage local_extended_learning_analytics
 * @copyright  2021 Thorben Laprell
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $ADMIN->add('localplugins', new admin_category('local_extended_learning_analytics_settings', new lang_string('pluginname', 'local_extended_learning_analytics')));
    $settingspage = new admin_settingpage('managelocal_extended_learning_analytics', new lang_string('pluginname', 'local_extended_learning_analytics'));

    if ($ADMIN->fulltree) {
        $settingspage->add(new admin_setting_configcheckbox(
            'local_extended_learning_analytics/showinnavigation',
            new lang_string('showinnavigation', 'local_extended_learning_analytics'),
            new lang_string('showinnavigation_desc', 'local_extended_learning_analytics'),
            1
        ));
    }

    $ADMIN->add('localplugins', $settingspage);
}