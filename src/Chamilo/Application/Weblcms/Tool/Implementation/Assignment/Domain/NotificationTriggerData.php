<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Domain;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Domain
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class NotificationTriggerData extends \Chamilo\Core\Notification\Domain\NotificationTriggerData
{
    /**
     * @var int
     */
    protected $entryId;

    /**
     * NotificationTriggerData constructor.
     *
     * @param string $processorClass
     * @param \DateTime $created
     * @param int $entryId
     */
    public function __construct($processorClass, $created, $entryId)
    {
        parent::__construct($processorClass, $created);
        $this->entryId = $entryId;
    }

    /**
     * @return int
     */
    public function getEntryId(): int
    {
        return $this->entryId;
    }
}