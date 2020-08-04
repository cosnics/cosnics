<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\AssignmentServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry;
use Chamilo\Core\Repository\ContentObject\Rubric\Display\Bridge\RubricBridgeInterface;
use Chamilo\Libraries\Architecture\ContextIdentifier;

/**
 * Class RubricBridge
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge
 */
class RubricBridge implements RubricBridgeInterface
{
    /**
     * @var AssignmentServiceBridgeInterface
     */
    protected $assignmentServiceBridge;

    /**
     * @var Entry
     */
    protected $entry;

    /**
     * RubricBridge constructor.
     *
     * @param AssignmentServiceBridgeInterface $assignmentServiceBridge
     */
    public function __construct(AssignmentServiceBridgeInterface $assignmentServiceBridge)
    {
        $this->assignmentServiceBridge = $assignmentServiceBridge;
    }

    /**
     * @param Entry $entry
     */
    public function setEntry(Entry $entry)
    {
        $this->entry = $entry;
    }

    /**
     * @return ContextIdentifier
     */
    public function getContextIdentifier()
    {
        return new ContextIdentifier(get_class($this->entry), $this->entry->getId());
    }

    /**
     * @return string|void
     */
    public function getEntityName()
    {
        return $this->assignmentServiceBridge->renderEntityNameByEntityTypeAndEntityId(
            $this->entry->getEntityType(), $this->entry->getEntityId()
        );
    }

    /**
     * @return \Chamilo\Core\User\Storage\DataClass\User[]
     */
    public function getTargetUsers()
    {
        return $this->assignmentServiceBridge->getUsersForEntity(
            $this->entry->getEntityType(), $this->entry->getEntityId()
        );
    }
}
