<?php
namespace Chamilo\Core\Install\Format\Structure;

use Chamilo\Libraries\Format\Structure\BaseHeader;

/**
 *
 * @package Chamilo\Core\Install\Format\Structure
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class Header extends BaseHeader
{

    /**
     *
     * @return \Chamilo\Core\Install\Format\Structure\Banner
     */
    protected function getBanner()
    {
        return new Banner($this->getApplication(), $this->getViewMode(), $this->getContainerMode());
    }
}
