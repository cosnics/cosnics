<?php
namespace Chamilo\Application\ExamAssignment\Ajax;

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
    const ACTION_UPLOAD_EXAM = 'UploadExam';

    const PARAM_CONTENT_OBJECT_PUBLICATION_ID = 'publicationId';
    const PARAM_SECURITY_CODE = 'securityCode';

    const DEFAULT_ACTION = self::ACTION_UPLOAD_EXAM;

    /**
     * @return ExamAssignmentService
     */
    protected function getExamAssignmentService()
    {
        return $this->getService(ExamAssignmentService::class);
    }
}
