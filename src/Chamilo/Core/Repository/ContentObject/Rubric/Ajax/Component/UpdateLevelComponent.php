<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Ajax;

/**
 * @package Chamilo\Core\Repository\ContentObject\Rubric\Ajax
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class UpdateLevelComponent extends Manager
{
    /**
     * @return array
     *
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\RubricStructureException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     * @throws \Doctrine\ORM\ORMException
     */
    function runAjaxComponent()
    {
        return $this->getRubricAjaxService()->updateLevel(
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
