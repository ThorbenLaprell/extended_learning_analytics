<?php
defined('MOODLE_INTERNAL') || die();
$tasks = [
    [
        'classname' => 'local_extended_learning_analytics\task\elanalytics_logger',
        'blocking' => 0,
        'minute' => '0',
        'hour' => '0',
        'day' => '*',
        'month' => '*',
        'dayofweek' => '*',
    ],
    [
        'classname' => 'local_extended_learning_analytics\task\elanalytics_cleaner',
        'blocking' => 0,
        'minute' => '0',
        'hour' => '0',
        'day' => '*',
        'month' => '*',
        'dayofweek' => '*',
    ]
];