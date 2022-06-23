<?php
namespace local_extended_learning_analytics\task;

use \local_extended_learning_analytics\logger;

/**
 * An example of a scheduled task.
 */
class run_query_helpers extends \core\task\scheduled_task {

    /**
     * Return the task's name as shown in admin screens.
     *
     * @return string
     */
    public function get_name() {
        return "run_query_helpers";
        return get_string('run_query_helpers', 'local_extended_learning_analytics');
    }

    /**
     * Execute the task.
     */
    public function execute() {
        var_dump("cron running");
        logger::run();
    }
}