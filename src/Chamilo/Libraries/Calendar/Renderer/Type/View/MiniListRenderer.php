<?php
namespace Chamilo\Libraries\Calendar\Renderer\Type\View;

/**
 *
 * @package Chamilo\Libraries\Calendar\Renderer\Type\View
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class MiniListRenderer extends ListRenderer
{

    /**
     *
     * @return integer
     */
    protected function getEndTime()
    {
        return strtotime('+3 Days', $this->getStartTime());
    }
}
