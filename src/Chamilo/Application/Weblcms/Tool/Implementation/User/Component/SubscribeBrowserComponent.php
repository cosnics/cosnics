<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User\Component;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Tool\Implementation\User\Component\Unsubscribed\UnsubscribedUserTable;
use Chamilo\Application\Weblcms\Tool\Implementation\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\ActionBar\ActionBarSearchForm;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package application.lib.weblcms.tool.user.component
 */
class SubscribeBrowserComponent extends Manager implements TableSupport
{

    /**
     *
     * @var ButtonToolBarRenderer
     */
    private $buttonToolbarRenderer;

    public function run()
    {
        if (!$this->is_allowed(WeblcmsRights::EDIT_RIGHT))
        {
            throw new NotAllowedException();
        }

        $this->buttonToolbarRenderer = $this->getButtonToolbarRenderer();
        $this->set_parameter(
            ActionBarSearchForm::PARAM_SIMPLE_SEARCH_QUERY, $this->buttonToolbarRenderer->getSearchForm()->getQuery()
        );

        $html = array();

        $html[] = $this->render_header();
        $html[] = $this->buttonToolbarRenderer->render();
        $html[] = $this->get_user_subscribe_html();
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('weblcms_user_subscribe_browser');

        $this->addBrowserBreadcrumb($breadcrumbtrail);
    }

    public function getButtonToolbarRenderer()
    {
        if (!isset($this->buttonToolbarRenderer))
        {
            $buttonToolbar = new ButtonToolBar($this->get_url());

            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }

        return $this->buttonToolbarRenderer;
    }

    public function get_additional_parameters()
    {
        return array(self::PARAM_TAB, \Chamilo\Application\Weblcms\Manager::PARAM_GROUP);
    }

    public function get_condition()
    {
        $conditions = array();

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(User::class_name(), User::PROPERTY_ACTIVE), new StaticConditionVariable(1)
        );

        $query = $this->buttonToolbarRenderer->getSearchForm()->getQuery();
        if (isset($query) && $query != '')
        {
            $conditions[] = $this->buttonToolbarRenderer->getConditions(
                array(
                    new PropertyConditionVariable(User::class_name(), User::PROPERTY_OFFICIAL_CODE),
                    new PropertyConditionVariable(User::class_name(), User::PROPERTY_LASTNAME),
                    new PropertyConditionVariable(User::class_name(), User::PROPERTY_FIRSTNAME),
                    new PropertyConditionVariable(User::class_name(), User::PROPERTY_USERNAME)
                )
            );
        }

        return new AndCondition($conditions);
    }

    public function get_table_condition($object_table_class_name)
    {
        return $this->get_condition();
    }

    public function get_user_subscribe_html()
    {
        $table = new UnsubscribedUserTable($this);

        $html = array();
        $html[] = $table->as_html();

        return implode(PHP_EOL, $html);
    }
}
