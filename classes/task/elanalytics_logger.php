<?php
namespace local_extended_learning_analytics\task;

use \local_extended_learning_analytics\logger;

/**
 * An example of a scheduled task.
 */
class elanalytics_logger extends \core\task\scheduled_task {

    /**
     * Return the task's name as shown in admin screens.
     *
     * @return string
     */
    public function get_name() {
        return get_string('elanalytics_logger', 'local_extended_learning_analytics');
    }

    /**
     * Execute the task.
     */
    public function execute() {
        logger::run();
    }
}