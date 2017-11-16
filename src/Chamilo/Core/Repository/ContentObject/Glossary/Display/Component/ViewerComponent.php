<?php
namespace Chamilo\Core\Repository\ContentObject\Glossary\Display\Component;

use Chamilo\Core\Repository\ContentObject\Glossary\Display\Component\Renderer\GlossaryRendererFactory;
use Chamilo\Core\Repository\ContentObject\Glossary\Display\Manager;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package repository.lib.complex_display.glossary.component
 */

/**
 * Represents the view component for the assessment tool.
 */
class ViewerComponent extends Manager implements DelegateComponent
{
    const PARAM_VIEW = 'view';

    /**
     *
     * @var ButtonToolBarRenderer
     */
    private $buttonToolbarRenderer;

    /**
     * Runs this component and shows it's output
     */
    public function run()
    {
        BreadcrumbTrail::getInstance()->add(new Breadcrumb(null, $this->get_root_content_object()->get_title()));
        $html = array();

        $html[] = $this->render_header();
        $html[] = $this->to_html();
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     * Returns the component as html string
     *
     * @return string
     */
    public function to_html()
    {
        $this->buttonToolbarRenderer = $this->getButtonToolbarRenderer();

        $query = $this->buttonToolbarRenderer->getSearchForm()->getQuery();

        $object = $this->get_parent()->get_root_content_object($this);
        $trail = BreadcrumbTrail::getInstance();

        if (! is_array($object))
        {
            $trail->add(new Breadcrumb($this->get_url(), $object->get_title()));
        }

        $html = array();

        $html[] = $this->buttonToolbarRenderer->render();
        $html[] = GlossaryRendererFactory::launch($this->get_view(), $this, $object, $query);

        return implode(PHP_EOL, $html);
    }

    /**
     * Returns the view type
     *
     * @return string
     */
    public function get_view()
    {
        $view = Request::get(self::PARAM_VIEW);

        if (! $view)
        {
            $view = GlossaryRendererFactory::TYPE_TABLE;
        }

        return $view;
    }

    /**
     * Builds and returns the actionbar
     *
     * @return ButtonToolBarRenderer
     */
    public function getButtonToolbarRenderer()
    {
        if (! isset($this->buttonToolbarRenderer))
        {
            $buttonToolbar = new ButtonToolBar($this->get_url());
            $commonActions = new ButtonGroup();

            if ($this->get_parent()->is_allowed_to_add_child())
            {
                $commonActions->addButton(
                    new Button(
                        Translation::get('CreateItem'),
                        Theme::getInstance()->getCommonImagePath('Action/Create'),
                        $this->get_url(
                            array(
                                self::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->get_complex_content_object_item_id(),
                                self::PARAM_ACTION => self::ACTION_CREATE_COMPLEX_CONTENT_OBJECT_ITEM)),
                        ToolbarItem::DISPLAY_ICON_AND_LABEL));
            }

            $commonActions->addButton(
                new Button(
                    Translation::get('TableView', null, Utilities::COMMON_LIBRARIES),
                    Theme::getInstance()->getCommonImagePath('Action/Browser'),
                    $this->get_url(array(self::PARAM_VIEW => GlossaryRendererFactory::TYPE_TABLE)),
                    ToolbarItem::DISPLAY_ICON_AND_LABEL));
            $commonActions->addButton(
                new Button(
                    Translation::get('ListView', null, Utilities::COMMON_LIBRARIES),
                    Theme::getInstance()->getCommonImagePath('Action/Browser'),
                    $this->get_url(array(self::PARAM_VIEW => GlossaryRendererFactory::TYPE_LIST)),
                    ToolbarItem::DISPLAY_ICON_AND_LABEL));

            $buttonToolbar->addItem($commonActions);

            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }

        return $this->buttonToolbarRenderer;
    }

    /**
     * Returns the additional parameters for registration
     *
     * @return array
     */
    public function get_additional_parameters()
    {
        return array(self::PARAM_VIEW);
    }

    /**
     * Checks whether or not the content object can be edited
     *
     * @return boolean
     */
    public function is_allowed_to_edit_content_object()
    {
        return $this->get_parent()->is_allowed_to_edit_content_object();
    }

    /**
     * Checks whether or not a child can be deleted
     *
     * @return boolean
     */
    public function is_allowed_to_delete_child()
    {
        return $this->get_parent()->is_allowed_to_delete_child();
    }
}
