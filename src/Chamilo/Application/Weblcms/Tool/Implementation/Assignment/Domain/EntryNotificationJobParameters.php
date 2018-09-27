<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Domain;

use Chamilo\Core\Queue\Domain\JobParametersInterface;

use JMS\Serializer\Annotation\Type;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Domain
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class EntryNotificationJobParameters implements JobParametersInterface
{
    /**
     * @var int
     *
     * @Type("int")
     */
    protected $entryId;

    /**
     * NotificationTriggerData constructor.
     *
     * @param int $entryId
     */
    public function __construct($entryId)
    {
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