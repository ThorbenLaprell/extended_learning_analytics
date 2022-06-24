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

 namespace elareport_weekheatmap;

defined('MOODLE_INTERNAL') || die();

use \local_learning_analytics\local\outputs\plot;
use \local_extended_learning_analytics\report_preview;
use elareport_weekheatmap\query_helper;

class preview extends report_preview {

    const X_MIN = -1;
    const X_MAX = 30;

    public static function content(): array {
        return self::activiyoverweeks();
    }

    private function activiyoverweeks() : array {
        global $USER, $OUTPUT, $DB;

        $plotdata = [];
        $textdata = [];
        $xstrs = [];
        $texts = [];

        $calendar = \core_calendar\type_factory::get_calendar_instance();
        $startOfWeek = $calendar->get_starting_weekday(); // 0 -> Sunday, 1 -> Monday

        $days = array('sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday');
        if ($startOfWeek !== 0) {
            $days = array_merge(array_slice($days, $startOfWeek), array_slice($days, 0, $startOfWeek));
        }
        $days = array_reverse($days);
        
        $ystrs = [];
        foreach ($days as $day) {
            $ystrs[] = get_string($day, 'calendar');
        }

        $date = new \DateTime();
        $date->modify('-1 week');
        $now = $date->getTimestamp();
        $date->modify('-28 week');

        $date->modify('Monday this week'); // Get start of week.

        $endoflastweek = new \DateTime();
        $endoflastweek->modify('Sunday last week');

        $weeks = query_helper::query_weekly_activity();

        for ($d = 0; $d < 7; $d += 1) {
            // we need to start the plot at the bottom (sun -> sat -> fri -> ...)
            $dbweekday = (6 + $startOfWeek - $d) % 7; // 0 (Sun) -> 6 (Sat) -> 5 (Fri) -> ...
            $daydata = [];
            $textdata = [];
            for ($h = 0; $h < 24; $h += 1) {
                $dbkey = $dbweekday . '-' . $h;
                $datapoint = empty($weeks[$dbkey]) ? 0 : $weeks[$dbkey]->value;
                $text = $datapoint;
                $daydata[] = $datapoint;
                $maxvalue = max($datapoint, $maxvalue);
                $hourstr = str_pad($h, 2, '0', STR_PAD_LEFT);
                $x = "{$hourstr}:00 - {$hourstr}:59";
                $xstrs[] = $x;
                $textdata[] = "<b>{$text} {$hitsstr}</b><br>{$ystrs[$d]}, {$x}";
            }
            $plotdata[] = $daydata;
            $texts[] = $textdata;
        }
        $plot = new plot();
        $plot->add_series([
            'type' => 'heatmap',
            'z' => $plotdata,
            'x' => $xstrs,
            'y' => $ystrs,
            'text' => $texts,
            'hoverinfo' => 'text',
            'colorscale' => [
                [0,    "#F3F3F3"],
                [.125, "#D4DFE8"],
                [.25,  "#B6CBDE"],
                [.375, "#97B7D3"],
                [.5,   "#79A3C9"],
                [.625, "#5B8FBE"],
                [.75,  "#3C7BB4"],
                [.875, "#1E67A9"],
                [1,    "#00549F"], // RWTH-blue
            ],
            'xgap' => 3,
            'ygap' => 3,
            'zmin' => 0,
            'zmax' => max(1, $maxvalue),
        ]);
        $layout = new \stdClass();
        $layout->margin = [ 't' => 10, 'r' => 20, 'l' => 80, 'b' => 80 ];
        $plot->set_layout($layout);
        $plot->set_height(400);
        return [
            '<h3 class="text">Hits at daytime in week</h3>',
            $plot
        ];
    }
}