<?php
namespace Chamilo\Core\Repository\Common\Rendition\Html;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;

class HtmlContentObjectRendition extends ContentObjectRendition
{

    /**
     * Build a bar-view of the used quota.
     * 
     * @param float $percent The percentage of the bar that is in use
     * @param string $status A status message which will be displayed below the
     *        bar.
     * @return string HTML representation of the requested bar.
     */
    private function get_bar($percent, $status)
    {
        $html = array();
        $html[] = '<div class="usage_information">';
        $html[] = '<div class="usage_bar">';
        for ($i = 0; $i < 100; $i ++)
        {
            if ($percent > $i)
            {
                if ($i >= 90)
                {
                    $class = 'very_critical';
                }
                elseif ($i >= 80)
                {
                    $class = 'critical';
                }
                else
                {
                    $class = 'used';
                }
            }
            else
            {
                $class = '';
            }
            $html[] = '<div class="' . $class . '"></div>';
        }
        $html[] = '</div>';
        $html[] = '<div class="usage_status">' . $status . ' &ndash; ' . round($percent, 2) . ' %</div>';
        $html[] = '</div>';
        return implode(PHP_EOL, $html);
    }
}
