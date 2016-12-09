<?php
namespace Chamilo\Core\Reporting\Viewer\Chart;

use CpChart\Classes\pImage;

/**
 * Special wrapper class to catch a small fluke in the scale rendering of pChart in such cases where the passed on value
 * is extremely small and should actually be 0
 * 
 * @author Hans De Bisschop & Magali Gillard
 */
class pChamiloImage extends pImage
{

    /**
     *
     * @see @see pDraw::scaleFormat()
     */
    public function scaleFormat($Value, $Mode = NULL, $Format = NULL, $Unit = NULL)
    {
        if (is_float($Value))
        {
            $Value = round($Value, 10);
        }
        
        return parent::scaleFormat($Value, $Mode, $Format, $Unit);
    }
}
