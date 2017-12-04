<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Renderer;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Renderer
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class EntityRenderer
{

    /**
     *
     * @var \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider
     */
    private $assignmentDataProvider;

    /**
     *
     * @var integer
     */
    private $entityId;

    /**
     *
     * @var \Chamilo\Libraries\Storage\DataClass\DataClass
     */
    private $entity;

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider $assignmentDataProvider
     * @param integer $entityId
     */
    public function __construct(AssignmentDataProvider $assignmentDataProvider, $entityId)
    {
        $this->assignmentDataProvider = $assignmentDataProvider;
        $this->entityId = $entityId;
    }

    /**
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider
     */
    public function getAssignmentDataProvider()
    {
        return $this->assignmentDataProvider;
    }

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider $assignmentDataProvider
     */
    public function setAssignmentDataProvider(AssignmentDataProvider $assignmentDataProvider)
    {
        $this->assignmentDataProvider = $assignmentDataProvider;
    }

    /**
     *
     * @return integer
     */
    public function getEntityId()
    {
        return $this->entityId;
    }

    /**
     *
     * @param integer $entityId
     */
    public function setEntityId($entityId)
    {
        $this->entityId = $entityId;
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function getEntity()
    {
        if (!isset($this->entity))
        {
            $this->entity = $this->findEntity();
        }

        return $this->entity;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass $entity
     */
    public function setEntity(DataClass $entity)
    {
        $this->entity = $entity;
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\DataClass\DataClass
     */
    abstract public function findEntity();

    /**
     *
     * @return string[]
     */
    public function getProperties()
    {
        return $this->renderProperties($this->getEntity());
    }

    /**
     *
     * @return string[]
     */
    abstract public function renderProperties(DataClass $entity);

    /**
     *
     * @return string
     */
    abstract function getEntityName();
}