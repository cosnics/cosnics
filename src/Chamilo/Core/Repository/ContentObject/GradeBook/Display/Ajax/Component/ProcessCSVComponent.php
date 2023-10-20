<?php

namespace Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax\Manager;
use Chamilo\Core\Repository\ContentObject\GradeBook\Service\ImportFromGradeBookExceptionDisplayService;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Core\Repository\ContentObject\GradeBook\Domain\Exceptions\GradeBookImportException;
use Chamilo\Libraries\Platform\Security\Csrf\CsrfComponentInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Chamilo\Libraries\Architecture\JsonAjaxResult;

/**
 * @package Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class ProcessCSVComponent extends Manager implements CsrfComponentInterface
{

    function run()
    {
        try
        {
            $result = $this->runAjaxComponent();
            return new JsonResponse($this->serialize($result), 200, [], true);
        }
        catch (GradeBookImportException | \Exception $ex)
        {
            $this->getExceptionLogger()->logException($ex);
            $message = $ex->getMessage();
            if ($ex instanceof GradeBookImportException)
            {
                $translator = $this->ajaxComponent->getTranslator();
                $displayService = $this->getService(ImportFromGradeBookExceptionDisplayService::class);
                $properties = $displayService->translateExceptionProperties($ex, $translator, 'Chamilo\Core\Repository\ContentObject\GradeBook\Display', '</b>, <b>');
                $message = $translator->trans($displayService->getExceptionName($ex), $properties, 'Chamilo\Core\Repository\ContentObject\GradeBook\Display');
            }
            $result = new JsonAjaxResult();
            $result->set_result_code(500);
            $result->set_result_message($message);
            $result->display();
        }
    }

    /**
     * @return array|array[]
     *
     * @throws NotAllowedException
     * @throws GradeBookImportException
     */
    function runAjaxComponent(): array
    {
        if (!$this->getRequest()->isMethod('POST'))
        {
            throw new NotAllowedException();
        }
        $targetUsers = $this->getGradeBookServiceBridge()->getTargetUsers();
        return $this->getImportFromCSVService()->processCSV($_FILES['file']['tmp_name'], $this->getImportType(), $targetUsers);
    }
}