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

 namespace elareport_eventstatistics;

defined('MOODLE_INTERNAL') || die();

use \local_learning_analytics\local\outputs\table;
use \local_extended_learning_analytics\report_preview;
use elareport_eventstatistics\query_helper;

class preview extends report_preview {

    private static $markercolorstext = [
        'quiz' => 'green',
        'resource' => 'blue',
        'page' => 'red',
        'url' => 'orange',
        'forum' => 'yellow',
        'wiki' => 'yellow',
        'assign' => 'navy',
        'pdfannotator' => 'pdfred',
    ];
    private static $markercolortextdefault = 'gray';

    public static function content(): array {
        return self::activiyoverweeks();
    }

    private function activiyoverweeks() : array {
        global $DB;

        $endoflastweek = new \DateTime();
        $endoflastweek->modify('Sunday last week');

        $events = query_helper::query_event_activity();

        $tabletypes = new table();
        $tabletypes->set_header([get_string('Lanalytics_Events_logged', 'elareport_eventstatistics')]);
        $maxhits = (current($events))->hits;
        $i = 0;
        foreach ($events as $item) {
            if($i == 20) {
                break;
            } else {
                $tabletypes->add_row([
                    "<a>{$item->eventname}</a>",
                    "<a>{$item->eventid}</a>",
                    table::fancyNumberCell(
                        $item->hits,
                        $maxhits,
                        self::$markercolorstext['page'] ?? self::$markercolortextdefault
                    )
                ]);
                $i++;
            }
        }

        return [
            '<h1 class="text">' . get_string('Eventstatistics', 'elareport_eventstatistics') . '</h1>',
            '<h3 class="text">' . get_string('Most_logged_events', 'elareport_eventstatistics') . '</h3>',
            $tabletypes,
        ];
    }
}