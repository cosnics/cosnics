<?php
namespace Chamilo\Core\Repository\Component;

use Chamilo\Core\Repository\Common\Renderer\ContentObjectRenderer;
use Chamilo\Core\Repository\Filter\FilterData;
use Chamilo\Core\Repository\Filter\Renderer\ConditionFilterRenderer;
use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Selector\TypeSelector;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Core\Repository\Workspace\Service\RightsService;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Utilities\Utilities;
use Chamilo\Libraries\Format\Structure\ActionBar\DropdownButton;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButton;

/**
 * $Id: browser.class.php 204 2009-11-13 12:51:30Z kariboe $
 *
 * @package repository.lib.repository_manager.component
 */
/**
 * Default repository manager component which allows the user to browse through the different categories and content
 * objects in the repository.
 */
class BrowserComponent extends Manager implements DelegateComponent
{

    private $form;

    /**
     *
     * @var ButtonToolBarRenderer
     */
    private $buttonToolbarRenderer;

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $this->buttonToolbarRenderer = $this->getButtonToolbarRenderer();

        if (! RightsService :: getInstance()->canViewContentObjects($this->get_user(), $this->getWorkspace()))
        {
            throw new NotAllowedException();
        }

        $trail = BreadcrumbTrail :: get_instance();

        $output = $this->get_content_objects_html();

        $query = $this->buttonToolbarRenderer->getSearchForm()->getQuery();
        if (isset($query) && $query != '')
        {
            $trail->add(
                new Breadcrumb(
                    $this->get_url(),
                    Translation :: get('SearchResultsFor', null, Utilities :: COMMON_LIBRARIES) . ' ' . $query));
        }

        $html = array();

        $html[] = $this->render_header();
        $html[] = $this->buttonToolbarRenderer->render();
        $html[] = $output;
        $html[] = ResourceManager :: get_instance()->get_resource_html(
            Path :: getInstance()->getJavascriptPath('Chamilo\Core\Repository', true) . 'Faq.js');
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     * Gets the table which shows the learning objects in the currently active category
     */
    private function get_content_objects_html()
    {
        $renderer = ContentObjectRenderer :: factory($this->get_renderer(), $this);
        return $renderer->as_html();
    }

    public function get_renderer()
    {
        $renderer = Request :: get(self :: PARAM_RENDERER);

        if ($renderer && in_array($renderer, $this->get_available_renderers()))
        {
            return $renderer;
        }
        else
        {
            $renderers = $this->get_available_renderers();
            return $renderers[0];
        }
    }

    public function get_available_renderers()
    {
        return array(
            ContentObjectRenderer :: TYPE_TABLE,
            ContentObjectRenderer :: TYPE_GALLERY,
            ContentObjectRenderer :: TYPE_SLIDESHOW);
    }

    public function getButtonToolbarRenderer()
    {
        if (! isset($this->buttonToolbarRenderer))
        {
            $buttonToolbar = new ButtonToolBar();

            if ($this->has_filter_type())
            {
                $filter_type = $this->get_filter_type();
                $template_registration = \Chamilo\Core\Repository\Configuration :: registration_by_id(
                    (int) $filter_type);

                $buttonToolbar->addItem(
                    new Button(
                        Translation :: get(
                            'CreateObjectType',
                            array('TYPE' => $template_registration->get_template()->translate('TypeName'))),
                        Theme :: getInstance()->getCommonImagePath('Action/Create'),
                        $this->get_url(
                            array(
                                Application :: PARAM_ACTION => self :: ACTION_CREATE_CONTENT_OBJECTS,
                                TypeSelector :: PARAM_SELECTION => $filter_type)),
                        ToolbarItem :: DISPLAY_ICON_AND_LABEL,
                        false,
                        'btn-primary'));
            }

            $buttonToolbar->addItem(
                new Button(
                    Translation :: get('ManageCategories'),
                    Theme :: getInstance()->getCommonImagePath('Action/Category'),
                    $this->get_url(array(Application :: PARAM_ACTION => self :: ACTION_MANAGE_CATEGORIES)),
                    ToolbarItem :: DISPLAY_ICON_AND_LABEL));

            $buttonToolbar->addItem(
                new Button(
                    Translation :: get('ExportCategory'),
                    Theme :: getInstance()->getCommonImagePath('Action/Backup'),
                    $this->get_url(
                        array(
                            Application :: PARAM_ACTION => self :: ACTION_EXPORT_CONTENT_OBJECTS,
                            FilterData :: FILTER_CATEGORY => FilterData :: get_instance($this->getWorkspace())->get_filter_property(
                                FilterData :: FILTER_CATEGORY))),
                    ToolbarItem :: DISPLAY_ICON_AND_LABEL));

            $renderers = $this->get_available_renderers();

            if (count($renderers) > 1)
            {
                $viewActions = new DropdownButton(
                    Translation :: get($this->get_renderer() . 'View', null, Utilities :: COMMON_LIBRARIES),
                    Theme :: getInstance()->getCommonImagePath('View/' . $this->get_renderer()));
                $buttonToolbar->addItem($viewActions);

                foreach ($renderers as $renderer)
                {
                    if ($this->get_renderer() != $renderer)
                    {
                        $action = $this->get_url(
                            array(\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_BROWSER_TYPE => $renderer));
                        $classes = '';
                    }
                    else
                    {
                        $action = '';
                        $classes = 'selected';
                    }

                    $viewActions->addSubButton(
                        new SubButton(
                            Translation :: get(
                                (string) StringUtilities :: getInstance()->createString($renderer)->upperCamelize() .
                                     'View',
                                    null,
                                    Utilities :: COMMON_LIBRARIES),
                            Theme :: getInstance()->getImagePath(
                                'Chamilo\Core\Repository',
                                'View/' . StringUtilities :: getInstance()->createString($renderer)->upperCamelize()),
                            $action,
                            Button :: DISPLAY_LABEL,
                            false,
                            $classes));
                }
            }

            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }

        return $this->buttonToolbarRenderer;
    }

    public function get_condition()
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_STATE),
            new StaticConditionVariable(ContentObject :: STATE_NORMAL));

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_OWNER_ID),
            new StaticConditionVariable($this->get_user_id()));

        $types = DataManager :: get_active_helper_types();

        foreach ($types as $type)
        {
            $conditions[] = new NotCondition(
                new EqualityCondition(
                    new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_TYPE),
                    new StaticConditionVariable($type)));
        }

        $filter_condition_renderer = ConditionFilterRenderer :: factory(
            FilterData :: get_instance($this->getWorkspace()),
            $this->getWorkspace());
        $filter_condition = $filter_condition_renderer->render();

        if ($filter_condition instanceof Condition)
        {
            $conditions[] = $filter_condition;
        }

        return new AndCondition($conditions);
    }

    private function get_parent_id()
    {
        return FilterData :: get_instance($this->getWorkspace())->get_filter_property(FilterData :: FILTER_CATEGORY);
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('repository_browser');
    }

    public function get_additional_parameters()
    {
        return parent :: get_additional_parameters(
            array(
                self :: PARAM_RENDERER,
                ContentObject :: PROPERTY_PARENT_ID,
                \Chamilo\Configuration\Category\Manager :: PARAM_CATEGORY_ID));
    }

    /**
     *
     * @return int
     */
    public function get_filter_type()
    {
        return TypeSelector :: get_selection();
    }

    /**
     *
     * @return boolean
     */
    public function has_filter_type()
    {
        $filter_type = $this->get_filter_type();
        return isset($filter_type);
    }
}
