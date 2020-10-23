<?php
namespace Chamilo\Application\ExamAssignment;

use Chamilo\Application\ExamAssignment\Service\ExamAssignmentService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface;

/**
 * @package Chamilo\Application\ExamAssignment
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class Manager extends Application
{
    const ACTION_LIST = 'List';
    const ACTION_VIEW_ASSIGNMENT = 'ViewAssignment';
    const ACTION_RESULT = 'Result';
    const ACTION_ENTRY = 'Entry';

    const PARAM_CONTENT_OBJECT_PUBLICATION_ID = 'publicationId';
    const PARAM_CODE = 'code';

    const DEFAULT_ACTION = self::ACTION_LIST;

    /**
     * Manager constructor.
     *
     * @param \Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface $applicationConfiguration
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     */
    public function __construct(ApplicationConfigurationInterface $applicationConfiguration)
    {
        parent::__construct($applicationConfiguration);

        if ($this->getUser() instanceof User)
        {
            $this->checkAuthorization(Manager::context());
        }
    }

    /**
     * @return ExamAssignmentService
     */
    protected function getExamAssignmentService()
    {
        return $this->getService(ExamAssignmentService::class);
    }
}