<?php
namespace Chamilo\Core\Repository\ContentObject\PeerAssessment;

use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Platform\Translation;
use pChart;
use pData;

require_once Path :: getInstance()->getPluginPath() . "pChart/pChart/pData.class";
require_once Path :: getInstance()->getPluginPath() . "pChart/pChart/pChart.class";

/**
 * draws a radar graph of peer assessment results displays personal score + average of the scores of other participants
 * extends pChart library
 * 
 * @author jevdheyd
 */
class PeerAssessmentGraph extends pChart
{

    private $title;

    private $competences = array();

    private $personal_score = null;

    private $average_total_score = null;

    private $offset = null;

    private $range = null;
    const PERSONAL_SCORE = 'PersonalScore';
    const AVG_TOTAL_SCORE = 'AverageTotalScore';

    public function __construct($title)
    {
        parent :: __construct(500, 700);
        
        $this->title = $title;
    }

    public function set_range($range)
    {
        $this->range = $range;
    }

    public function render()
    {
        $dataset = new pData();
        
        if (count($this->competences) > 0)
        {
            if (! is_null($this->competences))
            {
                $dataset->addPoint($this->competences, 'Label');
            }
            
            $dataset->SetAbsciseLabelSerie('Label');
            
            if (! is_null($this->personal_score))
            {
                $dataset->addPoint($this->personal_score, 'Serie1');
                $dataset->addSerie('Serie1');
                $dataset->setSerieName(Translation :: get(self :: PERSONAL_SCORE), 'Serie1');
            }
            else
            {
                // error
            }
            if (! is_null($this->average_total_score))
            {
                $dataset->addPoint($this->average_total_score, 'Serie2');
                $dataset->addSerie('Serie2');
                $dataset->setSerieName(Translation :: get(self :: AVG_TOTAL_SCORE), 'Serie2');
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
        
        $font = Path :: getInstance()->getPluginPath() . '/pChart/Fonts/tahoma.ttf';
        
        $this->setFontProperties($font, 8);
        $this->setGraphArea(100, 30, 370, 370);
        
        // Draw the radar graph
        $this->drawRadarAxis(
            $dataset->GetData(), 
            $dataset->GetDataDescription(), 
            TRUE, 
            20, 
            120, 
            120, 
            120, 
            230, 
            230, 
            230, 
            $this->range);
        $this->drawFilledRadar($dataset->GetData(), $dataset->GetDataDescription(), 50, 20, $this->range);
        
        // Finish the graph
        $this->drawLegend(15, 15, $dataset->GetDataDescription(), 255, 255, 255);
        $this->setFontProperties($font, 10);
        $this->drawTitle(0, 22, $this->title, 50, 50, 50, 400);
        
        $image_id = md5(serialize($this->title . $this->personal_score . $this->average_total_score));
        $image_path = Path :: getInstance()->getStoragePath() . 'temp/';
        $image_file = strtolower('peer_assessment_' . ereg_replace(' ', '_', $this->title) . '_' . $image_id . '.png');
        
        parent :: Render($image_path . $image_file);
        
        $web_path = Path :: getInstance()->getStoragePath(true) . 'temp/' . $image_file;
        
        return '<div style="float: left;"><img src="' . $web_path . '" border="0" alt="' . $this->title . '" title="' .
             $this->title . '" /></div>';
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

    /**
     * Overrides base library! This function draw radar axis centered on the graph area
     */
    public function drawRadarAxis($Data, $DataDescription, $Mosaic = TRUE, $BorderOffset = 10, $A_R = 60, $A_G = 60, $A_B = 60, 
        $S_R = 200, $S_G = 200, $S_B = 200, $MaxValue = -1)
    {
        /* Validate the Data and DataDescription array */
        $this->validateDataDescription("drawRadarAxis", $DataDescription);
        $this->validateData("drawRadarAxis", $Data);
        
        $C_TextColor = $this->AllocateColor($this->Picture, $A_R, $A_G, $A_B);
        
        /* Draw radar axis */
        $Points = count($Data);
        $Radius = ($this->GArea_Y2 - $this->GArea_Y1) / 2 - $BorderOffset;
        $XCenter = ($this->GArea_X2 - $this->GArea_X1) / 2 + $this->GArea_X1;
        $YCenter = ($this->GArea_Y2 - $this->GArea_Y1) / 2 + $this->GArea_Y1;
        
        /* Search for the max value */
        if ($MaxValue == - 1)
        {
            foreach ($DataDescription["Values"] as $Key2 => $ColName)
            {
                foreach ($Data as $Key => $Values)
                {
                    if (isset($Data[$Key][$ColName]))
                        if ($Data[$Key][$ColName] > $MaxValue)
                        {
                            $MaxValue = $Data[$Key][$ColName];
                        }
                }
            }
        }
        
        /* Draw the mosaic */
        if ($Mosaic)
        {
            $RadiusScale = $Radius / $MaxValue;
            for ($t = 1; $t <= $MaxValue - 1; $t ++)
            {
                $TRadius = $RadiusScale * $t;
                $LastX1 = - 1;
                $LastY1 = - 1;
                $LastX2 = - 1;
                $LastY2 = - 1;
                
                for ($i = 0; $i <= $Points; $i ++)
                {
                    $Angle = - 90 + $i * 360 / $Points;
                    $X1 = cos($Angle * 3.1418 / 180) * $TRadius + $XCenter;
                    $Y1 = sin($Angle * 3.1418 / 180) * $TRadius + $YCenter;
                    $X2 = cos($Angle * 3.1418 / 180) * ($TRadius + $RadiusScale) + $XCenter;
                    $Y2 = sin($Angle * 3.1418 / 180) * ($TRadius + $RadiusScale) + $YCenter;
                    
                    if ($t % 2 == 1 && $LastX1 != - 1)
                    {
                        $Plots = "";
                        $Plots[] = $X1;
                        $Plots[] = $Y1;
                        $Plots[] = $X2;
                        $Plots[] = $Y2;
                        $Plots[] = $LastX2;
                        $Plots[] = $LastY2;
                        $Plots[] = $LastX1;
                        $Plots[] = $LastY1;
                        
                        $C_Graph = $this->AllocateColor($this->Picture, 250, 250, 250);
                        imagefilledpolygon($this->Picture, $Plots, (count($Plots) + 1) / 2, $C_Graph);
                    }
                    
                    $LastX1 = $X1;
                    $LastY1 = $Y1;
                    $LastX2 = $X2;
                    $LastY2 = $Y2;
                }
            }
        }
        
        /* Draw the spider web */
        for ($t = 1; $t <= $MaxValue; $t ++)
        {
            $TRadius = ($Radius / $MaxValue) * $t;
            $LastX = - 1;
            $LastY = - 1;
            
            for ($i = 0; $i <= $Points; $i ++)
            {
                $Angle = - 90 + $i * 360 / $Points;
                $X = cos($Angle * 3.1418 / 180) * $TRadius + $XCenter;
                $Y = sin($Angle * 3.1418 / 180) * $TRadius + $YCenter;
                
                if ($LastX != - 1)
                    $this->drawDottedLine($LastX, $LastY, $X, $Y, 4, $S_R, $S_G, $S_B);
                
                $LastX = $X;
                $LastY = $Y;
            }
        }
        
        /* Draw the axis */
        for ($i = 0; $i <= $Points; $i ++)
        {
            $Angle = - 90 + $i * 360 / $Points;
            $X = cos($Angle * 3.1418 / 180) * $Radius + $XCenter;
            $Y = sin($Angle * 3.1418 / 180) * $Radius + $YCenter;
            
            $this->drawLine($XCenter, $YCenter, $X, $Y, $A_R, $A_G, $A_B);
            
            $XOffset = 0;
            $YOffset = 0;
            if (isset($Data[$i][$DataDescription["Position"]]))
            {
                $Label = $Data[$i][$DataDescription["Position"]];
                
                $Positions = imagettfbbox($this->FontSize, 0, $this->FontName, $Label);
                $Width = $Positions[2] - $Positions[6];
                $Height = $Positions[3] - $Positions[7];
                
                if ($Angle >= 0 && $Angle <= 90)
                    $YOffset = $Height;
                
                if ($Angle > 90 && $Angle <= 180)
                {
                    $YOffset = $Height;
                    $XOffset = - $Width;
                }
                
                if ($Angle > 180 && $Angle <= 270)
                {
                    $XOffset = - $Width;
                }
                
                imagettftext(
                    $this->Picture, 
                    $this->FontSize, 
                    0, 
                    $X + $XOffset, 
                    $Y + $YOffset, 
                    $C_TextColor, 
                    $this->FontName, 
                    $Label);
            }
        }
        
        /* Write the values */
        for ($t = 1; $t <= $MaxValue; $t ++)
        {
            $TRadius = ($Radius / $MaxValue) * $t;
            
            $Angle = - 90 + 360 / $Points;
            $X1 = $XCenter;
            $Y1 = $YCenter - $TRadius;
            $X2 = cos($Angle * 3.1418 / 180) * $TRadius + $XCenter;
            $Y2 = sin($Angle * 3.1418 / 180) * $TRadius + $YCenter;
            
            $XPos = floor(($X2 - $X1) / 2) + $X1;
            $YPos = floor(($Y2 - $Y1) / 2) + $Y1;
            
            $Positions = imagettfbbox($this->FontSize, 0, $this->FontName, $t);
            $X = $XPos - ($X + $Positions[2] - $X + $Positions[6]) / 2;
            $Y = $YPos + $this->FontSize;
            
            $this->drawFilledRoundedRectangle(
                $X + $Positions[6] - 2, 
                $Y + $Positions[7] - 1, 
                $X + $Positions[2] + 4, 
                $Y + $Positions[3] + 1, 
                2, 
                240, 
                240, 
                240);
            $this->drawRoundedRectangle(
                $X + $Positions[6] - 2, 
                $Y + $Positions[7] - 1, 
                $X + $Positions[2] + 4, 
                $Y + $Positions[3] + 1, 
                2, 
                220, 
                220, 
                220);
            
            imagettftext($this->Picture, $this->FontSize, 0, $X, $Y, $C_TextColor, $this->FontName, $t - $this->offset);
        }
    }
}
