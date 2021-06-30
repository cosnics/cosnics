<?php

namespace Chamilo\Core\Repository\ContentObject\Evaluation\Display\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Ajax\Manager;
use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Service\ImportResultsFromCuriosService;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\JsonAjaxResult;

/**
 * @package Chamilo\Core\Repository\ContentObject\Evaluation\Display\Ajax\Component
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class ImportComponent extends Manager
{
    function run()
    {
        try
        {
            if (!$this->getRequest()->isMethod('POST') || $this->getEvaluationServiceBridge()->getCurrentEntityType() !== 0)
            {
                throw new NotAllowedException();
            }
            $results = $this->getRequest()->getFromPost('results');
            $evaluation = $this->get_root_content_object();
            $contextId = $this->getEvaluationServiceBridge()->getContextIdentifier();

            /** @var ImportResultsFromCuriosService $importService */
            $importService = $this->getService(ImportResultsFromCuriosService::class);
            list('importedEntities' => $importedEntities) = $importService->importResults($evaluation->getId(), $this->getUser()->getId(), $contextId, $results);

            $missingUsers = $importService->filterUserFields(
                $importService->findMissingUsers($this->getUsers(), $importedEntities)
            );

            $result = new JsonAjaxResult();
            $result->set_result_code(200);
            $result->set_properties(['missing_users' => $missingUsers]);
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
}