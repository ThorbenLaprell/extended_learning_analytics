<?php

defined('MOODLE_INTERNAL') || die();

require(__DIR__ . '/../../learning_analytics/classes/local/outputs/plot.php');
require(__DIR__ . '/../../learning_analytics/classes/report_base.php');
require(__DIR__ . '/../../learning_analytics/reports/coursedashboard/classes/query_helper.php');

class extended_learning_analytics_coursedashboard {

    const X_MIN = -1;
    const X_MAX = 30;

    public function activiyoverweeks() : array {
        $course = get_course(89);

        $date = new DateTime();
        $date->modify('-1 week');
        $now = $date->getTimestamp();

        $date->setTimestamp($course->startdate);
        $date->modify('Monday this week'); // Get start of week.

        $endoflastweek = new DateTime();
        $endoflastweek->modify('Sunday last week');

        $query_helper = new query_helper();
        $weeks = $query_helper->query_weekly_activity(89);

        $privacythreshold = 1;

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

        $xmin = self::X_MIN;
        $xmax = self::X_MAX;

        $tickvals = [];
        $ticktext = [];

        /*$dateformat = get_string('strftimedate', 'langconfig');
        $thousandssep = get_string('thousandssep', 'langconfig');
        $decsep = get_string('decsep', 'langconfig');

        $tstrweek = get_string('week', 'extended_learning_analytics_coursedashboard');
        $strclicks = get_string('clicks', 'extended_learning_analytics_coursedashboard');*/

        $date->modify(($xmin - 1) . ' week');

        $lastweekinpast = -100;

        for ($i = $xmin; $i <= $xmax; $i++) {
            $week = $weeks[$i] ?? new stdClass();

            $weeknumber = ($i <= 0) ? ($i - 1) : $i;

            $x[] = $i;
            $tickvals[] = $i;
            $ticktext[] = $weeknumber;

            $clickcount = $week->clicks ?? 0;
            if ($clickcount < $privacythreshold) {
                $clickcount = 0;
            }

            $startofweektimestamp = $date->getTimestamp();
            $date->modify('+6 days');

            if ($startofweektimestamp < $now) {
                // Date is in the past.
                $yclicks[] = $clickcount;

                $weekstarttext = 'T';
                $weekendtext = 'N';
                $textClicks = $clickcount;
                if ($clickcount < $privacythreshold) {
                    $textClicks = "< {$privacythreshold}";
                }

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
            'x0' => (self::X_MIN - 0.5),
            'x1' => ($lastweekinpast + 0.5),
            'y0' => -0.07,
            'y1' => 1,
            'opacity' => '0.25',
            'fillcolor' => '#ddd',
            'line' => [ 'width' => 0 ],
            'layer' => 'below'
        ];
        if ($lastweekinpast !== self::X_MIN && $lastweekinpast !== self::X_MAX) {
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
            'name' => get_string('clicks', 'extended_learning_analytics_coursedashboard'),
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

        $layout = new stdClass();
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
            $plot
        ];
    }

    public function run(array $params): array {
        var_dump("test");
        global $PAGE, $DB, $OUTPUT, $CFG;
        $PAGE->requires->css('/local/learning_analytics/reports/coursedashboard/static/styles.css?3');

        var_dump(activiyoverweeks());
    }

}