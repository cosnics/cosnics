<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\Rubric\Ajax\Manager;

/**
 * @package Chamilo\Core\Repository\ContentObject\Rubric\Ajax
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class AddTreeNodeComponent extends Manager
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
        return $this->getRubricAjaxService()->addTreeNode(
            $this->getRubricDataId(), $this->getVersion(), $this->getTreeNodeData(),
            (int) $this->getRequest()->getFromPost(self::PARAM_NEW_PARENT_ID)
        );
    }

    /**
     * @return array|string[]
     */
    public function getRequiredPostParameters()
    {
        $parameters = parent::getRequiredPostParameters();
        $parameters[] = self::PARAM_TREE_NODE_DATA;

        return $parameters;
    }


}
