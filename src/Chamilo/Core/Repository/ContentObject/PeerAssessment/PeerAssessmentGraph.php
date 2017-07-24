<?php
namespace Chamilo\Core\Repository\ContentObject\PeerAssessment;

use Chamilo\Core\Reporting\Viewer\Chart\pChamiloImage;
use Chamilo\Libraries\File\Path;
use CpChart\Classes\pData;
use CpChart\Classes\pRadar;

/**
 * draws a radar graph of peer assessment results displays personal score + average of the scores of other participants
 * extends pChart library
 *
 * @author jevdheyd
 */
class PeerAssessmentGraph
{
    const PERSONAL_SCORE = 'PersonalScore';
    const AVG_TOTAL_SCORE = 'AverageTotalScore';

    private $title;

    private $competences = array();

    private $personal_score = null;

    private $average_total_score = null;

    private $offset = null;

    private $range = null;

    public function __construct($title)
    {
        $this->title = $title;
    }

    public function set_range($range)
    {
        $this->range = $range;
    }

    public function render()
    {
        $chart_data = new pData();

        if (count($this->competences) > 0)
        {
            if (! is_null($this->competences))
            {
                $chart_data->addPoints($this->competences, 'Label');
            }

            $chart_data->setAbscissa('Label');

            if (! is_null($this->personal_score))
            {
                $chart_data->addPoints($this->personal_score, 'Serie1');
                $chart_data->setAbscissa('Serie1');
            }
            else
            {
                // error
            }
            if (! is_null($this->average_total_score))
            {
                $chart_data->addPoints($this->average_total_score, 'Serie2');
                $chart_data->setAbscissa('Serie2');
            }
            else
            {
                // error
            }
        }
        else
        {
            // error
        }

        $base_path = 'temp/' . md5(serialize(array('radar_chart', $chart_data))) . '.png';
        $path = Path::getInstance()->getStoragePath(__NAMESPACE__) . $base_path;

        if (! file_exists($path))
        {
            $graph_area_left = 70;
            $height = 700;

            /* Create the pChart object */
            $chart_canvas = new pChamiloImage(500, $height, $chart_data);

            /* Draw a solid background */
            $format = array('R' => 240, 'G' => 240, 'B' => 240);
            $chart_canvas->drawFilledRectangle(0, 0, 599, $height - 1, $format);

            /* Add a border to the picture */
            $format = array('R' => 255, 'G' => 255, 'B' => 255);
            $chart_canvas->drawRectangle(1, 1, 698, $height - 2, $format);

            /* Set the default font properties */
            $chart_canvas->setFontProperties(
                array(
                    'FontName' => Path::getInstance()->getVendorPath() .
                         'szymach/c-pchart/src/Resources/fonts/verdana.ttf',
                        'FontSize' => 8,
                        'R' => 0,
                        'G' => 0,
                        'B' => 0));

            $radar_chart = new pRadar();

            /* Draw a polar chart */
            $chart_canvas->setGraphArea($graph_area_left, 20, 579, $height - 21);
            $options = array(
                'BackgroundGradient' => array(
                    'StartR' => 255,
                    'StartG' => 255,
                    'StartB' => 255,
                    'StartAlpha' => 100,
                    'EndR' => 172,
                    'EndG' => 214,
                    'EndB' => 239,
                    'EndAlpha' => 50),
                'FontSize' => 6);
            $radar_chart->drawRadar($chart_canvas, $chart_data, $options);

            $chart_canvas->drawLegend(
                20,
                26,
                array(
                    'Style' => LEGEND_BOX,
                    'Mode' => LEGEND_VERTICAL,
                    'R' => 250,
                    'G' => 250,
                    'B' => 250,
                    'Margin' => 5));

            /* Render the picture */
            $chart_canvas->render($path);
        }

        return '<div style="float: left;"><img src="data:image/png;base64,' . base64_encode(file_get_contents($path)) .
             '" border="0" alt="' . $this->title . '" title="' . htmlentities($this->title) . '" /></div>';
    }

    /**
     * sets competences + breaks sentence according to maxlength
     *
     * @param array $competences
     */
    public function add_competences($competences)
    {
        $maxlength = 12;

        foreach ($competences as $competence)
        {
            $this->competences[] = wordwrap($competence, $maxlength, "\n");
        }
    }

    /**
     * sets score and corrects it wth offset
     *
     * @param array $score
     */
    public function set_personal_score($score)
    {
        foreach ($score as $point)
        {
            $scores[] = $point + $this->offset;
        }

        $this->personal_score = $scores;
    }

    /**
     * sets score and corrects it wth offset
     *
     * @param array $score
     */
    public function set_average_total_score($score)
    {
        foreach ($score as $point)
        {
            $scores[] = $point + $this->offset;
        }

        $this->average_total_score = $scores;
    }

    /**
     * corrects the scale of the radar graph
     *
     * @param int $offset
     */
    public function set_offset($offset)
    {
        $this->offset = $offset;
    }
}
