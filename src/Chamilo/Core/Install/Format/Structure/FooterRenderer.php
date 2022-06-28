<?php
namespace Chamilo\Core\Install\Format\Structure;

use Chamilo\Libraries\Format\Structure\AbstractFooterRenderer;
use Chamilo\Libraries\Format\Structure\PageConfiguration;

/**
 *
 * @package Chamilo\Libraries\Format\Structure
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class FooterRenderer extends AbstractFooterRenderer
{
    public function render(): string
    {
        $html = [];

        $html[] = $this->getHeader();

        if ($this->getPageConfiguration()->getViewMode() != PageConfiguration::VIEW_MODE_HEADERLESS)
        {
            $html[] = $this->getContainerHeader();
            $html[] = $this->getContainerFooter();
        }

        $html[] = $this->getFooter();

        return implode(PHP_EOL, $html);
    }
}
