<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Table\Entry;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Service\AssignmentService;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider;
use Chamilo\Libraries\Architecture\Application\Application;

/**
 *
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Table\Entry\User
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class EntryTable extends \Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entry\EntryTable
{
    /**
     * @var AssignmentService
     */
    protected $assignmentService;

    /**
     * @var \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication
     */
    protected $contentObjectPublication;

    /**
     * @var int
     */
    protected $entityType;

    /**
     * EntityTable constructor.
     *
     * @param \Chamilo\Libraries\Architecture\Application\Application $component
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider $assignmentDataProvider
     * @param int $entityId
     * @param int $entityType
     * @param \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Service\AssignmentService $assignmentService
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     */
    public function __construct(
        Application $component, AssignmentDataProvider $assignmentDataProvider, $entityId, $entityType,
        AssignmentService $assignmentService,
        ContentObjectPublication $contentObjectPublication
    )
    {
        parent::__construct($component, $assignmentDataProvider, $entityId);
        $this->entityType = $entityType;
        $this->assignmentService = $assignmentService;
        $this->contentObjectPublication = $contentObjectPublication;
    }

    /**
     * @return \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Service\AssignmentService
     */
    public function getAssignmentService()
    {
        return $this->assignmentService;
    }

    /**
     * @return \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication
     */
    public function getContentObjectPublication()
    {
        return $this->contentObjectPublication;
    }

    /**
     * @return int
     */
    public function getEntityType()
    {
        return $this->entityType;
    }
}