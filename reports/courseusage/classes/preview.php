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
 * Version info for the Sections report
 *
 * @package     local_learning_analytics
 * @copyright   Lehr- und Forschungsgebiet Ingenieurhydrologie - RWTH Aachen University
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 namespace elareport_courseusage;

defined('MOODLE_INTERNAL') || die();

use \local_learning_analytics\local\outputs\plot;
use \local_learning_analytics\local\outputs\table;
use \local_extended_learning_analytics\report_preview;
use elareport_courseusage\query_helper;

class preview extends report_preview {

    const X_MIN = -1;
    const X_MAX = 30;
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
        $date = new \DateTime();
        $date->modify('-1 week');
        $now = $date->getTimestamp();
        $date->modify('-28 week');

        $date->modify('Monday this week'); // Get start of week.

        $endoflastweek = new \DateTime();
        $endoflastweek->modify('Sunday last week');

        $weeks = query_helper::query_course_activity();

        $tabletypes = new table();
        $tabletypes->set_header_local(['courses']);
        $maxhits = (current($weeks))->hits;
        $i = 0;
        foreach ($weeks as $item) {
            if($i == 20) {
                break;
            } else {
                $hits = $item->hits;
                $url = new \moodle_url('/course/view.php', ['id' => $item->courseid]);
                $typestr = $DB->get_record('course', array('id' => $item->courseid))->fullname;
                $tabletypes->add_row([
                    "<a href='{$url}'>{$typestr}</a>",
                    table::fancyNumberCell(
                        $hits,
                        $maxhits,
                        self::$markercolorstext['page'] ?? self::$markercolortextdefault
                    )
                ]);
                $i++;
            }
        }

        $weeks2 = query_helper::query_course_category_activity();

        $tabletypes2 = new table();
        $tabletypes2->set_header_local(['courses']);
        $maxhits = (current($weeks2))->hits;
        $i = 0;
        foreach ($weeks2 as $item) {
            if($i == 20) {
                break;
            } else {
                $hits = $item->hits;
                $url = new \moodle_url('/course/management.php', ['categoryid' => $item->id]);
                $typestr = $DB->get_record('course_categories', array('id' => $item->id))->name;
                $tabletypes2->add_row([
                    "<a href='{$url}'>{$typestr}</a>",
                    table::fancyNumberCell(
                        $hits,
                        $maxhits,
                        self::$markercolorstext['page'] ?? self::$markercolortextdefault
                    )
                ]);
                $i++;
            }
        }

        return [
            '<h3 class="text">Most visited courses</h3>',
            $tabletypes,
            '<h3 class="text">Most visited Toplevel Course Categories</h3>',
            $tabletypes2
        ];
    }
}