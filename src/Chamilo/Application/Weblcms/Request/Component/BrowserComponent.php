<?php
namespace Chamilo\Application\Weblcms\Request\Component;

use Chamilo\Application\Weblcms\Request\Manager;
use Chamilo\Application\Weblcms\Request\Rights\Rights;
use Chamilo\Application\Weblcms\Request\Storage\DataClass\Request;
use Chamilo\Application\Weblcms\Request\Storage\DataManager;
use Chamilo\Application\Weblcms\Request\Table\Request\RequestTable;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Format\Tabs\DynamicContentTab;
use Chamilo\Libraries\Format\Tabs\DynamicTabsRenderer;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;

class BrowserComponent extends Manager implements TableSupport, DelegateComponent
{

    private $table_type;

    private $buttonToolbarRenderer;

    function run()
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Request::class, Request::PROPERTY_USER_ID),
            new StaticConditionVariable($this->get_user_id())
        );
        $user_requests = DataManager::count(Request::class, new DataClassCountParameters($condition));

        $tabs = new DynamicTabsRenderer('request');

        if ($user_requests > 0 || Rights::getInstance()->request_is_allowed())
        {

            if ($user_requests > 0)
            {
                $this->table_type = RequestTable::TYPE_PERSONAL;
                $table = new RequestTable($this);
                $tabs->add_tab(
                    new DynamicContentTab(
                        'personal_request', Translation::get('YourRequests'),
                        new FontAwesomeGlyph('inbox', array('fa-lg'), null, 'fas'), $table->as_html()
                    )
                );
            }

            if (Rights::getInstance()->request_is_allowed())
            {
                $target_users = Rights::getInstance()->get_target_users(
                    $this->get_user()
                );

                if (count($target_users) > 0)
                {
                    $target_condition = new InCondition(
                        new PropertyConditionVariable(Request::class, Request::PROPERTY_USER_ID), $target_users
                    );
                }
                else
                {
                    $target_condition = new EqualityCondition(
                        new PropertyConditionVariable(Request::class, Request::PROPERTY_USER_ID),
                        new StaticConditionVariable(- 1)
                    );
                }

                $conditions = array();
                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(Request::class, Request::PROPERTY_DECISION),
                    new StaticConditionVariable(Request::DECISION_PENDING)
                );
                if (!$this->get_user()->is_platform_admin())
                {
                    $conditions[] = $target_condition;
                }
                $condition = new AndCondition($conditions);

                if (DataManager::count(Request::class, new DataClassCountParameters($condition)) > 0)
                {
                    $this->table_type = RequestTable::TYPE_PENDING;
                    $table = new RequestTable($this);
                    $tabs->add_tab(
                        new DynamicContentTab(
                            RequestTable::TYPE_PENDING, Translation::get('PendingRequests'),
                            new FontAwesomeGlyph('pause-circle', array('fa-lg'), null, 'fas'), $table->as_html()
                        )
                    );
                }

                $conditions = array();
                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(Request::class, Request::PROPERTY_DECISION),
                    new StaticConditionVariable(Request::DECISION_GRANTED)
                );
                if (!$this->get_user()->is_platform_admin())
                {
                    $conditions[] = $target_condition;
                }
                $condition = new AndCondition($conditions);

                if (DataManager::count(Request::class, new DataClassCountParameters($condition)) > 0)
                {
                    $this->table_type = RequestTable::TYPE_GRANTED;
                    $table = new RequestTable($this);
                    $tabs->add_tab(
                        new DynamicContentTab(
                            RequestTable::TYPE_GRANTED, Translation::get('GrantedRequests'),
                            new FontAwesomeGlyph('check-circle', array('fa-lg', 'text-success'), null, 'fas'),
                            $table->as_html()
                        )
                    );
                }

                $conditions = array();
                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(Request::class, Request::PROPERTY_DECISION),
                    new StaticConditionVariable(Request::DECISION_DENIED)
                );
                if (!$this->get_user()->is_platform_admin())
                {
                    $conditions[] = $target_condition;
                }
                $condition = new AndCondition($conditions);

                if (DataManager::count(Request::class, new DataClassCountParameters($condition)) > 0)
                {
                    $this->table_type = RequestTable::TYPE_DENIED;
                    $table = new RequestTable($this);
                    $tabs->add_tab(
                        new DynamicContentTab(
                            RequestTable::TYPE_DENIED, Translation::get('DeniedRequests'),
                            new FontAwesomeGlyph('minus-square', array('fa-lg', 'text-danger'), null, 'fas'),
                            $table->as_html()
                        )
                    );
                }
            }
        }

        if ($user_requests > 0 || (Rights::getInstance()->request_is_allowed() && $tabs->size() > 0) ||
            $this->get_user()->is_platform_admin())
        {
            $html = array();

            $this->buttonToolbarRenderer = $this->getButtonToolbarRenderer();
            $html[] = $this->render_header();
            $html[] = $this->buttonToolbarRenderer->render();
            $html[] = $tabs->render();
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
        else
        {
            $this->redirect(
                Translation::get('NoRequestsFormDirectly'), null, array(self::PARAM_ACTION => self::ACTION_CREATE)
            );
        }
    }

    function getButtonToolbarRenderer()
    {
        if (!isset($this->buttonToolbarRenderer))
        {
            $buttonToolbar = new ButtonToolBar();
            $commonActions = new ButtonGroup();
            $toolActions = new ButtonGroup();
            if ($this->request_allowed())
            {
                $commonActions->addButton(
                    new Button(
                        Translation::get('RequestCourse'),
                        new FontAwesomeGlyph('question-circle', array(), null, 'fas'),
                        $this->get_url(array(self::PARAM_ACTION => self::ACTION_CREATE))
                    )
                );
            }

            if ($this->get_user()->is_platform_admin())
            {
                $toolActions->addButton(
                    new Button(
                        Translation::get('ConfigureManagementRights'),
                        new FontAwesomeGlyph('lock', array(), null, 'fas'),
                        $this->get_url(array(self::PARAM_ACTION => self::ACTION_RIGHTS))
                    )
                );
            }

            $buttonToolbar->addButtonGroup($commonActions);
            $buttonToolbar->addButtonGroup($toolActions);

            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }

        return $this->buttonToolbarRenderer;
    }

    /**
     *
     * @see @see common\libraries.NewObjectTableSupport::get_object_table_condition()
     */
    public function get_table_condition($object_table_class_name)
    {
        $conditions = array();

        switch ($this->table_type)
        {
            case RequestTable::TYPE_PENDING :
                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(Request::class, Request::PROPERTY_DECISION),
                    new StaticConditionVariable(Request::DECISION_PENDING)
                );
                break;
            case RequestTable::TYPE_PERSONAL :
                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(Request::class, Request::PROPERTY_USER_ID),
                    new StaticConditionVariable($this->get_user_id())
                );
                break;
            case RequestTable::TYPE_GRANTED :
                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(Request::class, Request::PROPERTY_DECISION),
                    new StaticConditionVariable(Request::DECISION_GRANTED)
                );
                break;
            case RequestTable::TYPE_DENIED :
                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(Request::class, Request::PROPERTY_DECISION),
                    new StaticConditionVariable(Request::DECISION_DENIED)
                );
                break;
        }

        if (!$this->get_user()->is_platform_admin() && Rights::getInstance()->request_is_allowed() &&
            $this->table_type != RequestTable::TYPE_PERSONAL)
        {
            $target_users = Rights::getInstance()->get_target_users(
                $this->get_user()
            );

            if (count($target_users) > 0)
            {
                $conditions[] = new InCondition(
                    new PropertyConditionVariable(Request::class, Request::PROPERTY_USER_ID), $target_users
                );
            }
            else
            {
                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(Request::class, Request::PROPERTY_USER_ID),
                    new StaticConditionVariable(- 1)
                );
            }
        }

        return new AndCondition($conditions);
    }

    function get_table_type()
    {
        return $this->table_type;
    }
}

?>