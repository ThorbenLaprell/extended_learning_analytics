<?php
defined('MOODLE_INTERNAL') || die();
$tasks = [
    [
        'classname' => 'local_extended_learning_analytics\task\run_query_helpers',
        'blocking' => 0,
        'minute' => '1',
        'hour' => '*',
        'day' => '*',
        'month' => '*',
        'dayofweek' => '*',
    ],
];