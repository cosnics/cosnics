<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Evaluation\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\Evaluation\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\Evaluation\Ajax\Manager as AjaxManager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Translation\Translation;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Evaluation\Component
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class ImportFromCuriosComponent extends Manager implements DelegateComponent
{
    public function run()
    {
        $breadcrumbTrail = BreadcrumbTrail::getInstance();
        $breadcrumbTrail->add(new Breadcrumb($this->get_url(), Translation::get('Import', null, Manager::context())));

        return $this->getTwig()->render(
            \Chamilo\Core\Repository\ContentObject\Evaluation\Display\Manager::context() . ':CuriosUserImport.html.twig', $this->getTemplateProperties()
        );
    }

    /**
     * @return string[]
     * @throws NotAllowedException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ClassNotExistException
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
                    self::PARAM_ACTION => self::ACTION_DISPLAY,
                    self::PARAM_PUBLICATION_ID => '__PUBLICATION_ID__'
                ]
            )
        ];
    }
}