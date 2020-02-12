<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Ajax;

/**
 * @package Chamilo\Core\Repository\ContentObject\Rubric\Ajax
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class MoveTreeNodeComponent extends Manager
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
        return $this->getRubricAjaxService()->moveTreeNode(
            $this->getRubricDataId(), $this->getVersion(), $this->getTreeNodeData(),
            $this->getRequest()->getFromPost(self::PARAM_NEW_PARENT_ID),
            $this->getRequest()->getFromPost(self::PARAM_NEW_SORT)
        );
    }

    /**
     * @return array|string[]
     */
    public function getRequiredPostParameters()
    {
        $parameters = parent::getRequiredPostParameters();
        $parameters[] = self::PARAM_TREE_NODE_DATA;
        $parameters[] = self::PARAM_NEW_PARENT_ID;
        $parameters[] = self::PARAM_NEW_SORT;

        return $parameters;
    }

}
