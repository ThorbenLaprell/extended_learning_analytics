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

 namespace elareport_activityusage;

defined('MOODLE_INTERNAL') || die();

use \local_learning_analytics\local\outputs\plot;
use \local_learning_analytics\local\outputs\table;
use \local_extended_learning_analytics\report_preview;
use elareport_activityusage\query_helper;

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
        global $DB, $CFG;
        $CFG->chart_colorset = ['#6495ED', '#B0C4DE', '#B0E0E6', '#5F9EA0'];
        $date = new \DateTime();
        $date->modify('-1 week');
        $now = $date->getTimestamp();
        $date->modify('-28 week');

        $date->modify('Monday this week'); // Get start of week.

        $endoflastweek = new \DateTime();
        $endoflastweek->modify('Sunday last week');

        $weeks = query_helper::query_weekly_activity();

        $tabletypes = new table();
        $tabletypes->set_header_local(['activities']);
        $maxhits = (current($weeks))->hits;

        $i = 0;
        foreach ($weeks as $item) {
            if($i == 20) {
                break;
            } else {
                $hits = $item->hits;
                $typestr = $DB->get_record('modules', array('id' => $item->moduleid))->name;
                $courseid = $DB->get_record('course_modules', array('id' => $item->activityid))->course;
                $modinfo = get_fast_modinfo($courseid);
                $cm = $modinfo->get_cm($item->activityid);
                $name = $cm->name;

                $url = new \moodle_url('/mod/' . $typestr . '/view.php', ['id' => $item->activityid]);
                $tabletypes->add_row([
                    "<a href='{$url}'>{$name}</a>",
                    "<a'>{$typestr}</a>",
                    table::fancyNumberCell(
                        $hits,
                        $maxhits,
                        self::$markercolorstext['page'] ?? self::$markercolortextdefault
                    )
                ]);
            }
        }

        $weeks2 = query_helper::query_dominant_activity();
        $weeks3 = query_helper::query_dominant_activity_type();

        $tabletypes2 = new table();
        $tabletypes2->set_header(['Most visited activity types']);
        $maxhits = (current($weeks2))->hits;
        $i = 0;
        foreach ($weeks2 as $item) {
            if($i == 20) {
                break;
            } else {
                $hits = $item->hits;
                $tabletypes2->add_row([
                    "<a'>{$item->name}</a>",
                    table::fancyNumberCell(
                        $hits,
                        $maxhits,
                        self::$markercolorstext['page'] ?? self::$markercolortextdefault
                    )
                ]);
                $i++;
            }
        }

        $tabletypes3 = new table();
        $tabletypes3->set_header(['Most visited activity types']);
        $maxhits = (current($weeks3))->modulecount;
        $i = 0;
        foreach ($weeks3 as $item) {
            if($i == 20) {
                break;
            } else {
                $hits = $item->modulecount;
                $tabletypes3->add_row([
                    "<a'>{$item->name}</a>",
                    table::fancyNumberCell(
                        $hits,
                        $maxhits,
                        self::$markercolorstext['page'] ?? self::$markercolortextdefault
                    )
                ]);
                $i++;
            }
        }

        $activitiescount2 = array();
        $expensescount2 = array();
        $activitiesname2 = array();
        $sum2 = 0;
        foreach($weeks2 as $week) {
            $sum2 += $week->hits;
        }
        $sum3 = 0;
        foreach($weeks3 as $week) {
            $sum3 += $week->modulecount;
        }
        $max2 = $sum2 * 0.01;
        $max3 = $sum3 * 0.01;
        foreach($weeks3 as $week) {
            array_push($expensescount2, $week->modulecount/$max3);
            if($weeks2[$week->name]) {
                array_push($activitiescount2, $weeks2[$week->name]->hits/$max2);
                array_push($activitiesname2, $week->name);
            } else {
                array_push($activitiescount2, 0);
                array_push($activitiesname2, $week->name);
            }
        }
        $sales2 = new \core\chart_series('Most visited activity types', $activitiescount2);
        $expenses2 = new \core\chart_series('Most created activity types', $expensescount2);
        $labels2 = $activitiesname2;
        $chart = new \core\chart_bar();
        $chart->set_horizontal(true);
        $chart->add_series($sales2);
        $chart->add_series($expenses2);
        $chart->set_labels($labels2);
        $chart->get_xaxis(0, true)->set_label("relative hits/instances in %");
        $chart->get_yaxis(0, true)->set_label("Activity types");

        return [
            '<h3 class="text">Most visited activity</h3>',
            $tabletypes,
            '<h3 class="text">Most visited activity type</h3>',
            $tabletypes2,
            '<h3 class="text">Most used activity type</h3>',
            $tabletypes3,
            '<h3 class="text">Activity type chart</h3>',
            $chart
        ];
    }
}