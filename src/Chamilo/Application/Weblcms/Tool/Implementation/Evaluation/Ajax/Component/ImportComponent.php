<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Evaluation\Ajax\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\Evaluation\Ajax\Manager;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Service\ImportResultsFromCuriosService;
use Chamilo\Core\Repository\ContentObject\Evaluation\Storage\DataClass\Evaluation;
use Chamilo\Application\Weblcms\Tool\Implementation\Evaluation\Storage\DataClass\Publication as EvaluationPublication;
use Chamilo\Libraries\Architecture\ContextIdentifier;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\JsonAjaxResult;


/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Evaluation\Ajax\Component
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class ImportComponent extends Manager
{
    function run()
    {
        try
        {
            if (!$this->getRequest()->isMethod('POST'))
            {
                throw new NotAllowedException();
            }

            /** @var ImportResultsFromCuriosService $importService */
            $importService = $this->getService(ImportResultsFromCuriosService::class);

            $title = $this->getRequest()->getFromPost('title');
            $results = $this->getRequest()->getFromPost('results');
            $evaluatorId = $this->getUser()->getId();

            list('evaluation' => $evaluation, 'contentObjectPublication' => $contentObjectPublication, 'contextId' => $contextId)
                = $this->createEvaluationAndPublication($title, $evaluatorId);
            list('importedEntities' => $importedEntities) = $importService->importResults($evaluation->getId(), $evaluatorId, $contextId, $results);

            $missingUsers = $importService->filterUserFields(
                $importService->findMissingUsers($this->getCourseUsers(), $importedEntities)
            );

            $result = new JsonAjaxResult();
            $result->set_result_code(200);
            $result->set_properties(['missing_users' => $missingUsers, 'publicationId' => $contentObjectPublication->getId()]);
            $result->display();
        }
        catch (\Exception $ex)
        {
            $result = new JsonAjaxResult();
            $result->set_result_code(500);
            $result->set_result_message($ex->getMessage());
            $result->display();
        }
    }

    /**
     * @param string $title
     * @param int $ownerId
     * @return array
     */
    protected function createEvaluationAndPublication(string $title, int $ownerId): array
    {
        // TODO: Move this to a new service class
        $evaluation = new Evaluation();
        $evaluation->set_owner_id($ownerId);
        $evaluation->set_title($title);
        $evaluation->set_description($title);
        $evaluation->create();

        $contentObjectPublication = new ContentObjectPublication();
        $contentObjectPublication->set_content_object_id($evaluation->getId());
        $contentObjectPublication->set_course_id($this->ajaxComponent->get_course_id());
        $contentObjectPublication->set_tool($this->ajaxComponent->get_tool_id());
        $contentObjectPublication->set_publisher_id($this->getUser()->getId());
        $contentObjectPublication->set_publication_date(time());
        $contentObjectPublication->set_modified_date(time());
        $contentObjectPublication->set_hidden(true);
        $contentObjectPublication->set_allow_collaboration(1);

        $contentObjectPublication->create();

        $evaluationPublication = new EvaluationPublication();
        $evaluationPublication->setPublicationId($contentObjectPublication->getId());
        $evaluationPublication->setEntityType(0);
        $evaluationPublication->setReleaseScores(false);
        $evaluationPublication->create();

        $contextId = new ContextIdentifier(get_class($evaluationPublication), $contentObjectPublication->getId());

        return ['evaluation' => $evaluation, 'contentObjectPublication' => $contentObjectPublication, 'evaluationPublication' => $evaluationPublication, 'contextId' => $contextId];
    }
}