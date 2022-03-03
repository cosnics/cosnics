<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\Rubric\Ajax\Manager;

/**
 * @package Chamilo\Core\Repository\ContentObject\Rubric\Ajax
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class ResetAbsoluteWeightsComponent extends Manager
{
    /**
     * @return array
     *
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\InvalidChildTypeException
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\RubricHasResultsException
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\RubricStructureException
     * @throws \Doctrine\ORM\ORMException
     */
    function runAjaxComponent()
    {
        return $this->getRubricAjaxService()->resetRubricAbsoluteWeights($this->getRubricDataId(), $this->getVersion());
    }
}