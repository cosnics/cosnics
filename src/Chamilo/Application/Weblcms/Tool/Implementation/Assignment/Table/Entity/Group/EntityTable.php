<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Table\Entity\Group;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Service\AssignmentService;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Table\Entity
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class EntityTable extends \Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entity\EntityTable
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
     * @var int[]
     */
    protected $userIds;

    /**
     * EntityTable constructor.
     *
     * @param \Chamilo\Libraries\Architecture\Application\Application $component
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider $assignmentDataProvider
     * @param \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Service\AssignmentService $assignmentService
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param int[] $userIds
     */
    public function __construct(
        Application $component, AssignmentDataProvider $assignmentDataProvider,
        AssignmentService $assignmentService,
        ContentObjectPublication $contentObjectPublication,
        $userIds = []
    )
    {
        parent::__construct($component, $assignmentDataProvider);
        $this->assignmentService = $assignmentService;
        $this->contentObjectPublication = $contentObjectPublication;
        $this->userIds = $userIds;
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
     * @return int[]
     */
    public function getUserIds()
    {
        return $this->userIds;
    }

}