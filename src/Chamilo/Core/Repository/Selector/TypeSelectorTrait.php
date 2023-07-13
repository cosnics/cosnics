<?php
namespace Chamilo\Core\Repository\Selector;

use Chamilo\Libraries\Platform\ChamiloRequest;

/**
 * @package Chamilo\Core\Repository\Selector
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
trait TypeSelectorTrait
{
    abstract public function getRequest(): ChamiloRequest;

    public function getSelectedTypes()
    {
        return $this->getRequest()->getFromRequestOrQuery(TypeSelector::PARAM_SELECTION);
    }
}