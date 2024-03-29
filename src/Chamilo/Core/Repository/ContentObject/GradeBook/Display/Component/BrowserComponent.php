<?php

namespace Chamilo\Core\Repository\ContentObject\GradeBook\Display\Component;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Core\Repository\ContentObject\GradeBook\Display\Manager;
use Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax\Manager as AjaxManager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\GradeBook\Display\Component
 * @author Stefan Gabriëls <stefan.gabriels@hogent.be>
 */
class BrowserComponent extends Manager
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
        BreadcrumbTrail::getInstance()->remove(count(BreadcrumbTrail::getInstance()->getBreadcrumbs()) - 1);

        return $this->getTwig()->render(
            Manager::context() . ':Browser.html.twig', $this->getTemplateProperties()
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
        $gradebook = $this->getGradeBook();

        return [
            'HEADER' => $this->render_header(),
            'FOOTER' => $this->render_footer(),
            'LANGUAGE' => $this->getTranslator()->getLocale(),
            'CONTENT_OBJECT_TITLE' => $gradebook->get_title(),
            'CONTENT_OBJECT_RENDITION' => $this->renderContentObject(),
            'GRADEBOOK_ROOT_URL' => $this->get_url(
                [
                    self::PARAM_ACTION => null
                ]
            ),
            'GRADEBOOK_IMPORT_CSV_URL' => $this->get_url(
                [
                    self::PARAM_ACTION => self::ACTION_IMPORT_CSV
                ]
            ),
            'GRADEBOOK_EXPORT_URL' => $this->get_url(
                [
                    self::PARAM_ACTION => self::ACTION_EXPORT
                ]
            ),
            'LOAD_GRADEBOOK_DATA_URL' => $this->get_url(
                [
                    self::PARAM_ACTION => self::ACTION_AJAX,
                    AjaxManager::PARAM_ACTION => AjaxManager::ACTION_LOAD_GRADEBOOK_DATA
                ]
            ),
            'ADD_CATEGORY_URL' => $this->get_url(
                [
                    self::PARAM_ACTION => self::ACTION_AJAX,
                    AjaxManager::PARAM_ACTION => AjaxManager::ACTION_ADD_CATEGORY
                ]
            ),
            'UPDATE_CATEGORY_URL' => $this->get_url(
                [
                    self::PARAM_ACTION => self::ACTION_AJAX,
                    AjaxManager::PARAM_ACTION => AjaxManager::ACTION_UPDATE_CATEGORY
                ]
            ),
            'MOVE_CATEGORY_URL' => $this->get_url(
                [
                    self::PARAM_ACTION => self::ACTION_AJAX,
                    AjaxManager::PARAM_ACTION => AjaxManager::ACTION_MOVE_CATEGORY
                ]
            ),
            'REMOVE_CATEGORY_URL' => $this->get_url(
                [
                    self::PARAM_ACTION => self::ACTION_AJAX,
                    AjaxManager::PARAM_ACTION => AjaxManager::ACTION_REMOVE_CATEGORY
                ]
            ),
            'ADD_COLUMN_URL' => $this->get_url(
                [
                    self::PARAM_ACTION => self::ACTION_AJAX,
                    AjaxManager::PARAM_ACTION => AjaxManager::ACTION_ADD_COLUMN
                ]
            ),
            'UPDATE_COLUMN_URL' => $this->get_url(
                [
                    self::PARAM_ACTION => self::ACTION_AJAX,
                    AjaxManager::PARAM_ACTION => AjaxManager::ACTION_UPDATE_COLUMN
                ]
            ),
            'UPDATE_COLUMN_CATEGORY_URL' => $this->get_url(
                [
                    self::PARAM_ACTION => self::ACTION_AJAX,
                    AjaxManager::PARAM_ACTION => AjaxManager::ACTION_UPDATE_COLUMN_CATEGORY
                ]
            ),
            'MOVE_COLUMN_URL' => $this->get_url(
                [
                    self::PARAM_ACTION => self::ACTION_AJAX,
                    AjaxManager::PARAM_ACTION => AjaxManager::ACTION_MOVE_COLUMN
                ]
            ),
            'ADD_COLUMN_SUBITEM_URL' => $this->get_url(
                [
                    self::PARAM_ACTION => self::ACTION_AJAX,
                    AjaxManager::PARAM_ACTION => AjaxManager::ACTION_ADD_COLUMN_SUBITEM
                ]
            ),
            'REMOVE_COLUMN_SUBITEM_URL' => $this->get_url(
                [
                    self::PARAM_ACTION => self::ACTION_AJAX,
                    AjaxManager::PARAM_ACTION => AjaxManager::ACTION_REMOVE_COLUMN_SUBITEM
                ]
            ),
            'REMOVE_COLUMN_URL' => $this->get_url(
                [
                    self::PARAM_ACTION => self::ACTION_AJAX,
                    AjaxManager::PARAM_ACTION => AjaxManager::ACTION_REMOVE_COLUMN
                ]
            ),
            'SYNCHRONIZE_GRADEBOOK_URL' => $this->get_url(
                [
                    self::PARAM_ACTION => self::ACTION_AJAX,
                    AjaxManager::PARAM_ACTION => AjaxManager::ACTION_SYNCHRONIZE_GRADEBOOK
                ]
            ),
            'OVERWRITE_SCORE_URL' => $this->get_url(
                [
                    self::PARAM_ACTION => self::ACTION_AJAX,
                    AjaxManager::PARAM_ACTION => AjaxManager::ACTION_OVERWRITE_SCORE
                ]
            ),
            'REVERT_OVERWRITTEN_SCORE_URL' => $this->get_url(
                [
                    self::PARAM_ACTION => self::ACTION_AJAX,
                    AjaxManager::PARAM_ACTION => AjaxManager::ACTION_REVERT_OVERWRITTEN_SCORE
                ]
            ),
            'UPDATE_SCORE_COMMENT_URL' => $this->get_url(
                [
                    self::PARAM_ACTION => self::ACTION_AJAX,
                    AjaxManager::PARAM_ACTION => AjaxManager::ACTION_UPDATE_SCORE_COMMENT
                ]
            ),
            'CALCULATE_TOTAL_SCORES_URL' => $this->get_url(
                [
                    self::PARAM_ACTION => self::ACTION_AJAX,
                    AjaxManager::PARAM_ACTION => AjaxManager::ACTION_CALCULATE_TOTAL_SCORES
                ]
            ),
            'UPDATE_DISPLAY_TOTAL_URL' => $this->get_url(
                [
                    self::PARAM_ACTION => self::ACTION_AJAX,
                    AjaxManager::PARAM_ACTION => AjaxManager::ACTION_UPDATE_DISPLAY_TOTAL
                ]
            )
        ];
    }

    /**
     *
     * @return string
     */
    protected function renderContentObject()
    {
        $display = ContentObjectRenditionImplementation::factory(
            $this->get_root_content_object(),
            ContentObjectRendition::FORMAT_HTML,
            ContentObjectRendition::VIEW_DESCRIPTION,
            $this
        );

        return $display->render();
    }

    public function render_header($pageTitle = '')
    {
        $html = [];
        $html[] = parent::render_header('');

        return implode(PHP_EOL, $html);
    }

}
