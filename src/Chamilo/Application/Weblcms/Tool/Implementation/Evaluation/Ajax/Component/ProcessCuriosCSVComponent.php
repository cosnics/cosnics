<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Evaluation\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Service\ImportFromCuriosCSVService;
use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Service\ImportFromCuriosExceptionDisplayService;
use Chamilo\Core\Repository\ContentObject\Evaluation\Domain\Exceptions\CuriosImportException;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Application\Weblcms\Tool\Implementation\Evaluation\Ajax\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Platform\Security\Csrf\CsrfComponentInterface;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Evaluation\Ajax\Component
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class ProcessCuriosCSVComponent extends Manager implements CsrfComponentInterface
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
            $filename = $_FILES['file']['name'];
            $properties = $importService->processCSV($_FILES['file']['tmp_name'], $this->getCourseUsers());
            $properties['needs_title'] = true;
            $properties['title'] = $title = basename($filename,'.' . pathinfo($filename, PATHINFO_EXTENSION));
            $properties['title_exists'] = DataManager::content_object_title_exists($title, 0);

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