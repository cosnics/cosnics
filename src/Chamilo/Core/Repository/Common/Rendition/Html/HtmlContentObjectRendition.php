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

        if ($percent >= 100)
        {
            $percent = 100;
        }

        if ($percent >= 90)
        {
            $class = 'progress-bar-danger';
        }
        elseif ($percent >= 80)
        {
            $class = 'progress-bar-warning';
        }
        else
        {
            $class = 'progress-bar-success';
        }

        $displayPercent = round($percent);

        $html[] = '<div class="progress">';
        $html[] = '<div class="progress-bar progress-bar-striped ' . $class . '" role="progressbar" aria-valuenow="' .
             $displayPercent . '" aria-valuemin="0" aria-valuemax="100" style="width: ' . $displayPercent .
             '%; min-width: 2em;">';
        $html[] = $status . ' &ndash; ' . $displayPercent . '%';
        $html[] = '</div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }
}
