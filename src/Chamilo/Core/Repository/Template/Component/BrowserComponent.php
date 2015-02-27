<?php
namespace Chamilo\Core\Repository\Template\Component;

use Chamilo\Core\Repository\Filter\FilterData;
use Chamilo\Core\Repository\Filter\Renderer\ConditionFilterRenderer;
use Chamilo\Core\Repository\Form\RepositoryFilterForm;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Template\Manager;
use Chamilo\Core\Repository\Template\Table\Template\TemplateTable;
use Chamilo\Libraries\Format\Structure\ActionBarRenderer;
use Chamilo\Libraries\Format\Structure\ActionBarSearchForm;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * $Id: template_browser.class.php 204 2009-11-13 12:51:30Z kariboe $
 *
 * @package repository.lib.repository_manager.component
 */
class BrowserComponent extends Manager implements TableSupport
{

    private $action_bar;

    private $form;

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $this->action_bar = $this->get_action_bar();
        $this->form = new RepositoryFilterForm($this, $this->get_url());

        $trail = BreadcrumbTrail :: get_instance();

        $output = $this->get_table_html();

        $html = array();

        $html[] = $this->render_header();
        $html[] = $this->action_bar->as_html();
        $html[] = '<br />' . $this->form->toHtml() . '<br />';
        $html[] = $output;
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    public function get_table_html()
    {
        $condition = $this->get_condition();
        $parameters = $this->get_parameters(true);
        $parameters[ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY] = $this->action_bar->get_query();
        $table = new TemplateTable($this);
        return $table->as_html();
    }

    public function get_condition()
    {
        $query = $this->get_action_bar()->get_query();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_STATE),
            new StaticConditionVariable(ContentObject :: STATE_NORMAL));

        $types = \Chamilo\Core\Repository\Storage\DataManager :: get_active_helper_types();

        foreach ($types as $type)
        {
            $conditions[] = new NotCondition(
                new EqualityCondition(
                    new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_TYPE),
                    new StaticConditionVariable($type)));
        }

        $filter_condition_renderer = ConditionFilterRenderer :: factory(
            FilterData :: get_instance(),
            $this->get_user_id(),
            $this->get_allowed_content_object_types());
        $filter_condition = $filter_condition_renderer->render();

        if ($filter_condition instanceof Condition)
        {
            $conditions[] = $filter_condition;
        }

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_OWNER_ID),
            new StaticConditionVariable(0));

        return new AndCondition($conditions);
    }

    public function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $action_bar->set_search_url($this->get_url());
        $action_bar->add_common_action(
            new ToolbarItem(
                Translation :: get('ShowAll', null, Utilities :: COMMON_LIBRARIES),
                Theme :: getInstance()->getCommonImagePath() . 'action_browser.png',
                $this->get_url(),
                ToolbarItem :: DISPLAY_ICON_AND_LABEL));

        return $action_bar;
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(
                    array(
                        \Chamilo\Core\Repository\Manager :: PARAM_ACTION => \Chamilo\Core\Repository\Manager :: ACTION_BROWSE_CONTENT_OBJECTS)),
                Translation :: get('RepositoryManagerBrowserComponent')));
        $breadcrumbtrail->add_help('repository_template_browser');
    }

    public function get_table_condition($table_class_name)
    {
        return $this->get_condition();
    }
}
