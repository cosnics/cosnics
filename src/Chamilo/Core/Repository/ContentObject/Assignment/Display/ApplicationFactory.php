<?php
namespace Chamilo\Core\Repository\ContentObject\Assignment\Display;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ApplicationFactory extends \Chamilo\Libraries\Architecture\Factory\ApplicationFactory
{
    /**
     * @var \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider
     */
    protected $assignmentDataProvider;

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider $assignmentDataProvider
     */
    public function setAssignmentDataProvider(AssignmentDataProvider $assignmentDataProvider)
    {
        $this->assignmentDataProvider = $assignmentDataProvider;
    }

    public function getDefaultAction(string $context): string
    {
        if($this->assignmentDataProvider->canEditAssignment())
        {
            return Manager::ACTION_VIEW;
        }

        return Manager::ACTION_ENTRY;
    }

}