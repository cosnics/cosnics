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
        return $this->getRubricAjaxService()->updateChoice(
            $this->getRubricDataId(), $this->getVersion(), $this->getChoiceData()
        );
    }

    /**
     * @return array|string[]
     */
    public function getRequiredPostParameters()
    {
        $parameters = parent::getRequiredPostParameters();
        $parameters[] = self::PARAM_CHOICE_DATA;

        return $parameters;
    }

}
