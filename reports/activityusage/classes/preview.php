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
                    table::fancyNumberCell(
                        $hits,
                        $maxhits,
                        self::$markercolorstext['page'] ?? self::$markercolortextdefault
                    )
                ]);
            }
        }

        $dominants = query_helper::query_dominant_activity();
        $activitiescount = array();
        $activitiesname = array();
        foreach($dominants as $dominant) {
            array_push($activitiescount, $dominant->hits);
            array_push($activitiesname, $dominant->name);
        }
        $sales = new \core\chart_series('Dominant Activities', $activitiescount);
        $labels = $activitiesname;
        $chart = new \core\chart_pie();
        $chart->add_series($sales);
        $chart->set_labels($labels);

        $dominants2 = query_helper::query_dominant_activity_type();
        $activitiescount2 = array();
        $activitiesname2 = array();
        foreach($dominants2 as $dominant) {
            array_push($activitiescount2, $dominant->modulecount);
            array_push($activitiesname2, $dominant->name);
        }
        $sales2 = new \core\chart_series('Dominant Activities', $activitiescount2);
        $labels2 = $activitiesname2;
        $chart2 = new \core\chart_pie();
        $chart2->add_series($sales2);
        $chart2->set_labels($labels2);

        return [
            '<h3 class="text">Most visited activity</h3>',
            $tabletypes,
            '<h3 class="text">Activity Pie Charts</h3>',
            '<h4 class="text">Most visited activity types</h4>',
            $chart,
            '<h4 class="text">Most created activity types</h4>',
            $chart2
        ];
    }
}