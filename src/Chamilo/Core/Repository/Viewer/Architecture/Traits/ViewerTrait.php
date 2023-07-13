<?php
namespace Chamilo\Core\Repository\Viewer\Architecture\Traits;

use Chamilo\Core\Repository\Viewer\Manager;
use Chamilo\Libraries\Platform\ChamiloRequest;

/**
 * @package Chamilo\Core\Repository\Viewer\Architecture\Traits
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
trait ViewerTrait
{

    public function getObjectsSelectedInviewer()
    {
        return $this->getRequest()->getFromRequestOrQuery(Manager::PARAM_ID);
    }

    abstract public function getRequest(): ChamiloRequest;

    public function isAnyObjectSelectedInViewer(): bool
    {
        return $this->getRequest()->hasRequestOrQuery(Manager::PARAM_ID);
    }
}