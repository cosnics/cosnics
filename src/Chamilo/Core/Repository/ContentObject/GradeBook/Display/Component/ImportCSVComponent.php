<?php

namespace Chamilo\Core\Repository\ContentObject\GradeBook\Display\Component;

use Chamilo\Core\Repository\ContentObject\GradeBook\Display\Manager;
use Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax\Manager as AjaxManager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\GradeBook\Display\Component
 * @author Stefan Gabriëls <stefan.gabriels@hogent.be>
 */
class ImportCSVComponent extends Manager
{
    /**
     * @return string
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function run()
    {
        $this->checkAccessRights();

        return $this->getTwig()->render(
            Manager::context() . ':ImportCSV.html.twig', $this->getTemplateProperties()
        );
    }

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     */
    protected function checkAccessRights()
    {
        if (!$this->getGradeBookServiceBridge()->canEditGradeBook())
        {
            throw new NotAllowedException();
        }
    }

    /**
     * @return array
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ClassNotExistException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     */
    protected function getTemplateProperties(): array
    {
        $gradeBookData = $this->getGradeBookService()->getGradeBookData($this->getGradeBook());

        return [
            'HEADER' => $this->render_header(),
            'FOOTER' => $this->render_footer(),
            'LANGUAGE' => $this->getTranslator()->getLocale(),
            'GRADEBOOK_DATA_ID' => $gradeBookData->getId(),
            'GRADEBOOK_DATA_CURRENT_VERSION' => $gradeBookData->getVersion(),
            'GRADEBOOK_ROOT_URL' => $this->get_url(
                [
                    self::PARAM_ACTION => null
                ]
            ),
            'PROCESS_CSV_URL' => $this->get_url(
                [
                    self::PARAM_ACTION => self::ACTION_AJAX,
                    AjaxManager::PARAM_ACTION => AjaxManager::ACTION_PROCESS_CSV
                ]
            ),
            'IMPORT_URL' => $this->get_url(
                [
                    self::PARAM_ACTION => self::ACTION_AJAX,
                    AjaxManager::PARAM_ACTION => AjaxManager::ACTION_IMPORT
                ]
            )
        ];
    }
}