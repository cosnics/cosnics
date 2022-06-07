<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Home\Renderer\Type;

use Chamilo\Application\Weblcms\Renderer\ToolList\Type\PanelToolListRenderer;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Tool\Implementation\Home\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\Home\Renderer\HomeRenderer;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Home\Renderer\Type
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class SidebarHomeRenderer extends HomeRenderer
{

    /**
     *
     * @see \Chamilo\Application\Weblcms\Tool\Implementation\Home\Renderer\HomeRenderer::render()
     */
    public function render()
    {
        $html = [];

        $html[] = '<div class="row course-home">';

        // Menu
        $html[] = '<div class="col-xs-12 col-sm-3 col-lg-2 course-home-sidebar">';
        $html[] = $this->renderMenu();
        $html[] = '</div>';

        // Introduction
        $html[] = '<div class="col-xs-12 col-sm-9 col-lg-10 course-home-content">';
        $html[] = $this->renderIntroduction();
        $html[] = '</div>';

        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return string
     */
    public function renderIntroduction()
    {
        $introduction = $this->getIntroduction();

        $html = [];

        if ($introduction)
        {
            $toolbar = new Toolbar();

            if ($this->getHomeTool()->is_allowed(WeblcmsRights::EDIT_RIGHT))
            {
                $toolbar->add_item(
                    new ToolbarItem(
                        Translation::get('Edit', null, StringUtilities::LIBRARIES), new FontAwesomeGlyph('pencil-alt'),
                        $this->getHomeTool()->get_url(
                            array(
                                Manager::PARAM_ACTION => Manager::ACTION_UPDATE_CONTENT_OBJECT,
                                Manager::PARAM_PUBLICATION_ID => $introduction->get_id()
                            )
                        ), ToolbarItem::DISPLAY_ICON
                    )
                );
            }

            if ($this->getHomeTool()->is_allowed(WeblcmsRights::DELETE_RIGHT))
            {
                $toolbar->add_item(
                    new ToolbarItem(
                        Translation::get('Delete', null, StringUtilities::LIBRARIES), new FontAwesomeGlyph('times'),
                        $this->getHomeTool()->get_url(
                            array(
                                Manager::PARAM_ACTION => Manager::ACTION_DELETE,
                                Manager::PARAM_PUBLICATION_ID => $introduction->get_id()
                            )
                        ), ToolbarItem::DISPLAY_ICON, true
                    )
                );
            }

            $contentObject = $introduction->get_content_object();

            $renditionImplementation = ContentObjectRenditionImplementation::factory(
                $contentObject, ContentObjectRendition::FORMAT_HTML, ContentObjectRendition::VIEW_DESCRIPTION,
                $this->getHomeTool()
            );

            $html[] = '<div class="panel panel-default">';
            $html[] = '<div class="panel-body">';
            $html[] = $renditionImplementation->render();

            if ($toolbar->has_items())
            {
                $html[] = $toolbar->as_html() . '<div class="clearfix"></div>';
            }

            $html[] = '</div>';
            $html[] = '</div>';
        }

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return string
     */
    public function renderMenu()
    {
        $renderer = new PanelToolListRenderer($this->getHomeTool(), $this->getCourseTools());

        return $renderer->toHtml();
    }
}