<?php
namespace local_extended_learning_analytics\task;

use \local_extended_learning_analytics\cleaner;

/**
 * An example of a scheduled task.
 */
class elanalytics_cleaner extends \core\task\scheduled_task {

    /**
     * Return the task's name as shown in admin screens.
     *
     * @return string
     */
    public function get_name() {
        return "elanalytics_cleaner";
        return get_string('elanalytics_logger', 'local_extended_learning_analytics');
    }

    /**
     * Execute the task.
     */
    public function execute() {
        cleaner::run();
    }
}