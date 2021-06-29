<?php

namespace Chamilo\Core\Repository\ContentObject\Evaluation\Display\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Ajax\Manager;
use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Service\Entity\EvaluationEntityRetrieveProperties;
use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Service\ImportFromCuriosCSVService;
use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Service\ImportFromCuriosExceptionDisplayService;
use Chamilo\Core\Repository\ContentObject\Evaluation\Domain\Exceptions\CuriosImportException;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Exceptions\UserException;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Storage\FilterParameters\FilterParameters;

/**
 * @package Chamilo\Core\Repository\ContentObject\Evaluation\Display\Ajax\Component
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class ProcessCuriosCSVComponent extends Manager
{
    function run()
    {
        try
        {
            if (!$this->getRequest()->isMethod('POST'))
            {
                throw new NotAllowedException();
            }

            $importService = $this->getService(ImportFromCuriosCSVService::class);
            $properties = $importService->processCSV($_FILES['file']['tmp_name'], $this->getUsers());

            $result = new JsonAjaxResult();
            $result->set_result_code(200);
            $result->set_properties($properties);
            $result->display();
        }
        catch (\Exception $ex)
        {
            $message = $ex->getMessage();
            if ($ex instanceof CuriosImportException)
            {
                $translator = $this->ajaxComponent->getTranslator();
                $displayService = $this->getService(ImportFromCuriosExceptionDisplayService::class);
                $properties = $displayService->translateExceptionProperties($ex, $translator, 'Chamilo\Core\Repository\ContentObject\Evaluation\Display', '</b>, <b>');
                $message = $translator->trans($displayService->getExceptionName($ex), $properties, 'Chamilo\Core\Repository\ContentObject\Evaluation\Display');
            }
            $result = new JsonAjaxResult();
            $result->set_result_code(500);
            $result->set_result_message($message);
            $result->display();
        }
    }
}