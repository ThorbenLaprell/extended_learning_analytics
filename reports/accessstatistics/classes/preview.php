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

 namespace elareport_accessstatistics;

defined('MOODLE_INTERNAL') || die();

use \local_learning_analytics\local\outputs\plot;
use \local_extended_learning_analytics\report_preview;
use elareport_accessstatistics\query_helper;

class preview extends report_preview {

    public static function content(): array {
        return self::activiyoverweeks();
    }

    private function activiyoverweeks() : array {
        $lifetimeInWeeks = get_config('local_extended_learning_analytics', 'lifetimeInWeeks');
        $date = new \DateTime();
        $now = $date->getTimestamp();
        $date->modify('-' . $lifetimeInWeeks . ' week');

        $date->modify('Monday this week');

        $endoflastweek = new \DateTime();
        $endoflastweek->modify('Sunday last week');

        $weeks = query_helper::query_weekly_activity();

        $plot = new plot();
        $x = [];
        $yclicks = [];

        $texts = [];

        $ymax = 1;

        foreach ($weeks as $week) {
            $ymax = max($ymax, $week->clicks);
        }
        $ymax = $ymax * 1.1;

        $xmin = 0;
        for ($i=0; $i<count($weeks); $i++) {
            if($weeks[$i]->clicks > 0) {
                $xmin = $i;
                break;
            }
        }
        $xmax = $lifetimeInWeeks;

        $tickvals = [];
        $ticktext = [];

        $date->modify(($xmin) . ' week');

        $lastweekinpast = -100;
        $lastmonth;

        $ticktext = array();
        for ($i = $xmin; $i <= $xmax; $i++) {
            $week = $weeks[$i+1] ?? new \stdClass();

            $weeknumber = ($i <= 0) ? ($i - 1) : $i;

            $x[] = $i;
            $tickvals[] = $i;
            $thatdate = new \DateTime($week->date);
            array_push($ticktext, $thatdate->format("d M Y"));

            $clickcount = $week->clicks ?? 0;

            $startofweektimestamp = $date->getTimestamp();
            $date->modify('+6 days');

            if ($startofweektimestamp < $now) {
                // Date is in the past.
                $yclicks[] = $clickcount;

                $weekstarttext = userdate($startofweektimestamp, $dateformat);
                $weekendtext = userdate($date->getTimestamp(), $dateformat);
                $textClicks = $clickcount;

                $texts[] = "<b>{$tstrweek} {$weeknumber}</b> ({$weekstarttext} - {$weekendtext})<br><br>{$textClicks} {$strclicks}";
                $lastweekinpast = $i;
            }

            $date->modify('+1 day');

            if($lastmonth != $thatdate->format("M")) {
                $lastmonth = $thatdate->format("M");
                $shapes[] = [
                    'type' => 'line',
                    'xref' => 'x',
                    'yref' => 'paper',
                    'x0' => ($i - 0.5),
                    'x1' => ($i - 0.5),
                    'y0' => -0.07,
                    'y1' => 1,
                    'line' => [ 'color' => '#2B2B2B', 'width' => 1.5 ],
                    'layer' => 'below'
                ];
            } else {
                $shapes[] = [
                    'type' => 'line',
                    'xref' => 'x',
                    'yref' => 'paper',
                    'x0' => ($i - 0.5),
                    'x1' => ($i - 0.5),
                    'y0' => -0.07,
                    'y1' => 1,
                    'line' => [ 'color' => '#aaa', 'width' => 1 ],
                    'layer' => 'below'
                ];
            }
        }

        $shapes[] = [
            'type' => 'rect',
            'xref' => 'x',
            'yref' => 'paper',
            'x0' => ($xmin - 0.5),
            'x1' => ($lastweekinpast + 0.5),
            'y0' => -0.07,
            'y1' => 1,
            'opacity' => '0.25',
            'fillcolor' => '#ddd',
            'line' => [ 'width' => 0 ],
            'layer' => 'below'
        ];
        if ($lastweekinpast !== $xmin && $lastweekinpast !== $xmax) {
            $shapes[] = [ // Line shows in which week are currently are.
                'type' => 'line',
                'xref' => 'x',
                'yref' => 'paper',
                'x0' => ($lastweekinpast + 0.5),
                'x1' => ($lastweekinpast + 0.5),
                'y0' => -0.07,
                'y1' => 1,
                'line' => [
                    'color' => 'rgb(0, 0, 0)',
                    'width' => 1,
                    'dash' => 'dot'
                ]
            ];
        }

        // Current course.
        $plot->add_series([
            'type' => 'scatter',
            'mode' => 'lines+markers',
            'name' => get_string('clicks', 'lareport_coursedashboard'),
            'x' => $x,
            'y' => $yclicks,
            'text' => $texts,
            'marker' => [ 'color' => 'rgb(31, 119, 180)' ],
            'line' => [ 'color' => 'rgb(31, 119, 180)' ],
            'hoverinfo' => 'text',
            'hoverlabel' => [
                'bgcolor' => '#eee',
                'font' => [
                    'size' => 15
                ]
            ],
            'legendgroup' => 'a'
        ]);

        $layout = new \stdClass();
        $layout->margin = [
            't' => 10,
            'r' => 0,
            'l' => 40,
            'b' => 120
        ];
        $layout->xaxis = [
            'ticklen' => 10,
            'showgrid' => false,
            'zeroline' => false,
            'range' => [ ($xmin - 0.5), ($xmax + 0.5) ],
            'tickmode' => 'array',
            'tickvals' => $tickvals,
            'ticktext' => $ticktext,
            'fixedrange' => true
        ];
        $layout->yaxis = [
            'range' => [ (-1 * $ymax * 0.01), $ymax ],
            'fixedrange' => true
        ];
        $layout->showlegend = true;
        $layout->legend = [
            'bgcolor' => 'rgba(255, 255, 255, 0.8)',
            'orientation' => 'v',
            'xanchor' => 'right',
            'yanchor' => 'top',
            'x' => (1 - 0.0021),
            'y' => 1,
            'bordercolor' => 'rgba(255, 255, 255, 0)',
            'borderwidth' => 10,
            'traceorder' => 'grouped'
        ];

        $layout->shapes = $shapes;

        $plot->set_layout($layout);
        $plot->set_height(400);

        return [
            '<h1 class="text">' . get_string('accessstatistics', 'elareport_accessstatistics') . '</h1>',
            '<h3 class="text">' . get_string('Visits_per_week', 'elareport_accessstatistics') . '</h3>',
            $plot
        ];
    }
}