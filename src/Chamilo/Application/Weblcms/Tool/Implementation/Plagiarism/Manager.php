<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Plagiarism;

use Chamilo\Application\Plagiarism\Service\ContentObjectPlagiarismChecker;
use Chamilo\Application\Plagiarism\Service\ContentObjectPlagiarismResultService;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Plagiarism
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class Manager extends \Chamilo\Application\Weblcms\Tool\Manager
{
    const ACTION_BROWSE = 'Browser';
    const ACTION_REFRESH = 'Refresh';
    const ACTION_CHECK_PLAGIARISM = 'CheckPlagiarism';
    const ACTION_VIEW_REPORT = 'ViewReport';

    const PARAM_CONTENT_OBJECT_PLAGIARISM_RESULT_ID = 'PlagiarismResultId';

    const DEFAULT_ACTION = self::ACTION_BROWSE;

    /**
     * @return ContentObjectPlagiarismResultService
     */
    protected function getContentObjectPlagiarismResultService()
    {
        return $this->getService(ContentObjectPlagiarismResultService::class);
    }

    /**
     * @return ContentObjectPlagiarismChecker
     */
    protected function getContentObjectPlagiarismChecker()
    {
        return $this->getService(ContentObjectPlagiarismChecker::class);
    }
}
