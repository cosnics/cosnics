<?php
namespace Chamilo\Core\Install\Format\Structure;

use Chamilo\Libraries\Format\Structure\BaseFooter;
use Chamilo\Libraries\Format\Structure\Page;

/**
 *
 * @package Chamilo\Libraries\Format\Structure
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Footer extends BaseFooter
{

    /**
     *
     * @see \Chamilo\Libraries\Format\Structure\BaseFooter::render()
     */
    public function render()
    {
        $html = array();

        $html[] = $this->getHeader();

        if ($this->getViewMode() != Page::VIEW_MODE_HEADERLESS)
        {
            $html[] = $this->getContainerHeader();
            $html[] = $this->getContainerFooter();
        }

        $html[] = $this->getFooter();

        return implode(PHP_EOL, $html);
    }
}
