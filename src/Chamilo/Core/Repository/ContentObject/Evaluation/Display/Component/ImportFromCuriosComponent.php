<?php

namespace Chamilo\Core\Repository\ContentObject\Evaluation\Display\Component;

use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Ajax\Manager as AjaxManager;
use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;

/**
 * @package Chamilo\Core\Repository\ContentObject\Evaluation\Display\Component
 *
 * @author - Stefan Gabriëls - Hogeschool Gent
 */
class ImportFromCuriosComponent extends Manager
{
    /**
     * @return string
     *
     * @throws NotAllowedException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function run()
    {
        $this->checkAccessRights();

        return $this->getTwig()->render(
            \Chamilo\Core\Repository\ContentObject\Evaluation\Display\Manager::context() . ':CuriosUserImport.html.twig', $this->getTemplateProperties()
        );
    }

    /**
     * @throws NotAllowedException
     */
    protected function checkAccessRights()
    {
        if (!$this->getEvaluationServiceBridge()->canEditEvaluation()) {
            throw new NotAllowedException();
        }
    }

    /**
     * @return string[]
     */
    protected function getTemplateProperties()
    {
        return [
            'HEADER' => $this->render_header(),
            'FOOTER' => $this->render_footer(),
            'PROCESS_CSV_URL' => $this->get_url(
                [
                    self::PARAM_ACTION => self::ACTION_AJAX,
                    AjaxManager::PARAM_ACTION => AjaxManager::ACTION_PROCESS_CURIOS_CSV
                ]
            ),
            'IMPORT_URL' => $this->get_url(
                [
                    self::PARAM_ACTION => self::ACTION_AJAX,
                    AjaxManager::PARAM_ACTION => AjaxManager::ACTION_IMPORT
                ]
            ),
            'EVALUATION_URL' => $this->get_url(
                [
                    self::PARAM_ACTION => self::DEFAULT_ACTION
                ]
            )
        ];
    }
}