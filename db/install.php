<?php

defined('MOODLE_INTERNAL') || die;

require_once(__DIR__ . '/../lib.php');

function xmldb_local_extended_learning_analytics_install() {
    global $DB;
    $records = get_report_names();
    foreach ($records as $record) {
        $DB->insert_record('elanalytics_reports', $record);
    }
    return true;
}