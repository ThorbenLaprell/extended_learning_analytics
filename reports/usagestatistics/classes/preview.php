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

 namespace elareport_usagestatistics;

defined('MOODLE_INTERNAL') || die();

use \local_learning_analytics\local\outputs\plot;
use \local_extended_learning_analytics\report_preview;
use elareport_usagestatistics\query_helper;

class preview extends report_preview {

    public static function content(): array {
        return self::activiyoverweeks();
    }

    private function activiyoverweeks() : array {
        $lifetimeInWeeks = get_config('local_extended_learning_analytics', 'lifetimeInWeeks');
        $date = new \DateTime();
        $now = $date->getTimestamp();
        $date->modify('-' . $lifetimeInWeeks . ' week');

        $date->modify('Monday this week'); // Get start of week.

        $endoflastweek = new \DateTime();
        $endoflastweek->modify('Sunday last week');

        $weeks = query_helper::query_weekly_activity();
        //$weeks = array_slice($weeks, count($weeks) - $lifetimeInWeeks);

        $plot = new plot();
        $x = [];
        $yclicks = [];

        $texts = [];

        $shapes = [
            [ // Line showing the start of the lecture.
                'type' => 'line',
                'xref' => 'x',
                'yref' => 'paper',
                'x0' => 0.5,
                'x1' => 0.5,
                'y0' => -0.07,
                'y1' => 1,
                'line' => [
                    'color' => 'rgb(0, 0, 0)',
                    'width' => 1.5
                ]
            ]
        ];

        $ymax = 1;

        foreach ($weeks as $week) {
            $ymax = max($ymax, $week->clicks);
        }
        $ymax = $ymax * 1.1;

        $xmin = 0;
        for ($i=0; $i<count($weeks); $i++) {
            if($weeks[$i]->clicks > 0) {
                $xmin = $i-1;
                break;
            }
        }
        $xmax = $lifetimeInWeeks;

        $tickvals = [];
        $ticktext = [];

        $dateformat = get_string('strftimedate', 'langconfig');
        $thousandssep = get_string('thousandssep', 'langconfig');
        $decsep = get_string('decsep', 'langconfig');

        $tstrweek = get_string('week', 'lareport_coursedashboard');
        $strclicks = get_string('clicks', 'lareport_coursedashboard');

        $date->modify(($xmin) . ' week');

        $lastweekinpast = -100;

        for ($i = $xmin; $i <= $xmax; $i++) {
            $week = $weeks[$i+1] ?? new \stdClass();

            $weeknumber = ($i <= 0) ? ($i - 1) : $i;

            $x[] = $i;
            $tickvals[] = $i;
            $ticktext[] = $weeknumber;

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
            'b' => 40
        ];
        $layout->xaxis = [
            'ticklen' => 0,
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
        $plot->set_height(300);

        return [
            '<h3 class="text">Visits per Week</h3>',
            $plot
        ];
    }
}