<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\Rubric\Ajax\Manager;

/**
 * @package Chamilo\Core\Repository\ContentObject\Rubric\Ajax
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class AddLevelComponent extends Manager
{
    /**
     * @return array
     *
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\RubricStructureException
     * @throws \Doctrine\ORM\ORMException
     */
    function runAjaxComponent()
    {
        return $this->getRubricAjaxService()->addLevel(
            $this->getRubricDataId(), $this->getVersion(), $this->getLevelData()
        );
    }

    /**
     * @return array|string[]
     */
    public function getRequiredPostParameters()
    {
        $parameters = parent::getRequiredPostParameters();
        $parameters[] = self::PARAM_LEVEL_DATA;

        return $parameters;
    }

}
