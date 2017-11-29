<?php
namespace Chamilo\Libraries\Calendar\Renderer\Type;

/**
 *
 * @package Chamilo\Libraries\Calendar\Renderer\Type
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
