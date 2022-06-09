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

defined('MOODLE_INTERNAL') || die;

// Empty $settings to prevent a single settings page from being created by lib/classes/plugininfo/block.php
// because we will create several settings pages now.
$settings = null;

$settingscategory = new admin_category('local_extended_learning_analytics', get_string('pluginname', 'local_extended_learning_analytics'));
$ADMIN->add('localplugins', $settingscategory);

$settings = new admin_settingpage('local_extended_learning_analytics_general_settings',
        get_string('general_settings', 'local_extended_learning_analytics'));

if ($ADMIN->fulltree) {

    $settings->add(new admin_setting_configtext(
        'local_extended_learning_analytics/dashboard_boxes',
        'dashboard_boxes',
        get_string('setting_reports_to_show', 'local_extended_learning_analytics'),
        'usagestatistics:10',
        PARAM_RAW,
        60
    ));

    $settings->add(new admin_setting_configtext(
        'local_extended_learning_analytics/reports_to_log',
        'reports_to_log',
        get_string('setting_reports_to_log', 'local_extended_learning_analytics'),
        'usagestatistics',
        PARAM_RAW,
        60
    ));

    $settings->add(new admin_setting_configtext(
        'local_extended_learning_analytics/lifetimeInWeeks',
        'lifetimeInWeeks',
        get_string('setting_lifetimeInWeeks', 'local_extended_learning_analytics'),
        'lifetimeInWeeks:156',
        PARAM_RAW,
        60
    ));

}

$ADMIN->add('local_extended_learning_analytics', $settings);

foreach (core_plugin_manager::instance()->get_plugins_of_type('elareport') as $plugin) {
    /** @var \editor_atto\plugininfo\atto $plugin */
    $plugin->load_settings($ADMIN, 'local_extended_learning_analytics', $hassiteconfig);
}

$ADMIN->add('reports',
    new admin_externalpage (
        'local_extended_learning_analytics',
        "Extended learning Analytics",
        new moodle_url('/local/extended_learning_analytics/index.php'),
        'moodle/site:config'
    )
);

// Required or the editor plugininfo will add this section twice.
unset($settings);
$settings = null;