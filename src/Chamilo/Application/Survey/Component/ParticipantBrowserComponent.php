<?php
namespace Chamilo\Application\Survey\Component;

use Chamilo\Application\Survey\Manager;
use Chamilo\Application\Survey\Rights\Rights;
use Chamilo\Application\Survey\Storage\DataClass\Participant;
use Chamilo\Application\Survey\Storage\DataClass\Publication;
use Chamilo\Application\Survey\Storage\DataManager;
use Chamilo\Application\Survey\Table\Group\GroupTable;
use Chamilo\Application\Survey\Table\Participant\ParticipantTable;
use Chamilo\Application\Survey\Table\User\UserTable;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Rights\Entity\PlatformGroupEntity;
use Chamilo\Core\Rights\Entity\UserEntity;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Structure\ActionBarRenderer;
use Chamilo\Libraries\Format\Structure\ActionBarSearchForm;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Format\Tabs\DynamicContentTab;
use Chamilo\Libraries\Format\Tabs\DynamicTabsRenderer;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Condition\PatternMatchCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;

class ParticipantBrowserComponent extends Manager implements TableSupport
{
    const TAB_PARTICIPANTS = 1;
    const TAB_USERS = 2;
    const TAB_GROUPS = 3;

    private $action_bar;

    private $pid;

    private $survey_publication;

    private $survey;

    function run()
    {
        $this->pid = Request :: get(self :: PARAM_PUBLICATION_ID);
        
        $this->set_parameter(self :: PARAM_PUBLICATION_ID, $this->pid);

        if (! Rights :: get_instance()->is_right_granted(Rights :: INVITE_RIGHT, $this->pid))
        {
           throw new NotAllowedException();
        }

        $this->survey_publication = DataManager :: retrieve_by_id(Publication :: class_name(), $this->pid);
        $this->survey = $this->survey_publication->get_publication_object();
        $this->action_bar = $this->get_action_bar();

        $html = array();
        
        $html[] = $this->render_header();
        $html[] = $this->action_bar->as_html();
        $html[] = $this->get_tables();
        $html[] = $this->render_footer();
        
        return implode(PHP_EOL, $html);
    }

    function get_tables()
    {
        $tabs = new DynamicTabsRenderer($this->class_name(false));

        $parameters = $this->get_parameters();
        $parameters[ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY] = $this->action_bar->get_query();
        $parameters[self :: PARAM_PUBLICATION_ID] = $this->pid;

        $table = new ParticipantTable($this);
        $tabs->add_tab(
            new DynamicContentTab(
                self :: TAB_PARTICIPANTS,
                Translation :: get('Participants'),
                Theme :: getInstance()->getImagePath('Chamilo\Application\Survey', 'Logo/16'),
                $table->as_html()));

        $table = new UserTable($this);
        $tabs->add_tab(
            new DynamicContentTab(
                self :: TAB_USERS,
                Translation :: get('Users'),
                Theme :: getInstance()->getImagePath('Chamilo\Application\Survey', 'Logo/16'),
                $table->as_html()));

        $table = new GroupTable($this);
        $tabs->add_tab(
            new DynamicContentTab(
                self :: TAB_GROUPS,
                Translation :: get('Groups'),
                Theme :: getInstance()->getImagePath('Chamilo\Application\Survey', 'Logo/16'),
                $table->as_html()));

        $html = array();
        $html[] = $tabs->render();
        return implode(PHP_EOL, $html);
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $parameters = $this->get_parameters();
        $parameters[self :: PARAM_PUBLICATION_ID] = $this->pid;

        $action_bar->set_search_url($this->get_url($parameters));
        $action_bar->add_common_action(
            new ToolbarItem(
                Translation :: get('ShowAll', array(), Utilities :: COMMON_LIBRARIES),
                Theme :: getInstance()->getCommonImagePath('Action/Browser'),
                $this->get_url($parameters),
                ToolbarItem :: DISPLAY_ICON_AND_LABEL));

        if ($this->get_user()->is_platform_admin() ||
             $this->get_user()->get_id() == $this->survey_publication->get_publisher())
        {
            $action_bar->add_tool_action(
                new ToolbarItem(
                    Translation :: get('ManageRights', array(), Utilities :: COMMON_LIBRARIES),
                    Theme :: getInstance()->getCommonImagePath('Action/Rights'),
                    $this->get_publication_rights_url($this->survey_publication),
                    ToolbarItem :: DISPLAY_ICON_AND_LABEL));

            $action_bar->add_tool_action(
                new ToolbarItem(
                    Translation :: get('SubscribeEmails'),
                    Theme :: getInstance()->getCommonImagePath('Export/Excel'),
                    $this->get_subscribe_email_url($this->pid),
                    ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }

        if (Rights :: get_instance()->is_right_granted(Rights :: MAIL_RIGHT, $this->pid))
        {
            $action_bar->add_tool_action(
                new ToolbarItem(
                    Translation :: get('MailManager'),
                    Theme :: getInstance()->getCommonImagePath('Action/InviteUsers'),
                    $this->get_mail_survey_participant_url($this->survey_publication),
                    ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }

        return $action_bar;
    }

    function get_participant_condition()
    {
        $query = $this->action_bar->get_query();
        if (! isset($query))
        {
            $query = Request :: get(ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY);
        }
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Participant :: class_name(), Participant :: PROPERTY_SURVEY_PUBLICATION_ID),
            new StaticConditionVariable($this->pid));

        if (isset($query) && $query != '')
        {
            $user_conditions = array();
            $user_conditions[] = new PatternMatchCondition(
                new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_FIRSTNAME),
                '*' . $query . '*');
            $user_conditions[] = new PatternMatchCondition(
                new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_LASTNAME),
                '*' . $query . '*');
            $user_condition = new OrCondition($user_conditions);
            $users = \Chamilo\Core\User\Storage\DataManager :: retrieve_users($user_condition);
            $user_ids = array();
            while ($user = $users->next_result())
            {
                $user_ids[] = $user->get_id();
            }

            $search_conditions = array();
            $search_conditions[] = new InCondition(
                new PropertyConditionVariable(Participant :: class_name(), Participant :: PROPERTY_USER_ID),
                $user_ids);
            $conditions[] = new OrCondition($search_conditions);
        }

        return new AndCondition($conditions);
    }

    function get_user_condition()
    {
        $publication_id = Request :: get(self :: PARAM_PUBLICATION_ID);

        $target_entities = array();
        $target_entities = Rights :: get_instance()->get_publication_targets_entities(
            Rights :: PARTICIPATE_RIGHT,
            $publication_id);
        $condition = null;

        $invited_users = $target_entities[UserEntity :: ENTITY_TYPE];

        if (count($invited_users) > 0)
        {

            $condition = new InCondition(
                new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_ID),
                $invited_users);
        }
        else
        {
            $condition = new EqualityCondition(
                new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_ID),
                new StaticConditionVariable(0));
        }

        $query = $this->action_bar->get_query();

        if (isset($query) && $query != '')
        {
            $or_conditions[] = new PatternMatchCondition(
                new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_FIRSTNAME),
                '*' . $query . '*');
            $or_conditions[] = new PatternMatchCondition(
                new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_LASTNAME),
                '*' . $query . '*');
            $or_conditions[] = new PatternMatchCondition(
                new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_USERNAME),
                '*' . $query . '*');
            $or_condition = new OrCondition($or_conditions);
        }

        if ($or_condition)
        {
            $conditions = array($condition, $or_condition);
            $condition = new AndCondition($conditions);
        }

        return $condition;
    }

    function get_group_condition()
    {
        $publication_id = Request :: get(self :: PARAM_PUBLICATION_ID);

        $target_entities = array();
        $target_entities = Rights :: get_instance()->get_publication_targets_entities(
            Rights :: PARTICIPATE_RIGHT,
            $publication_id);
        $condition = null;

        $invited_groups = $target_entities[PlatformGroupEntity :: ENTITY_TYPE];

        if (count($invited_groups) > 0)
        {

            $condition = new InCondition(
                new PropertyConditionVariable(Group :: class_name(), Group :: PROPERTY_ID),
                $invited_groups);
        }
        else
        {
            $condition = new EqualityCondition(
                new PropertyConditionVariable(Group :: class_name(), Group :: PROPERTY_ID),
                new StaticConditionVariable(0));
        }

        $query = $this->action_bar->get_query();

        if (isset($query) && $query != '')
        {
            $or_conditions[] = new PatternMatchCondition(
                new PropertyConditionVariable(Group :: class_name(), Group :: PROPERTY_NAME),
                '*' . $query . '*');
            $or_conditions[] = new PatternMatchCondition(
                new PropertyConditionVariable(Group :: class_name(), Group :: PROPERTY_DESCRIPTION),
                '*' . $query . '*');
            $or_condition = new OrCondition($or_conditions);
        }

        if ($or_condition)
        {
            $conditions = array($condition, $or_condition);
            $condition = new AndCondition($conditions);
        }

        return $condition;
    }

    public function get_table_condition($object_table_class_name)
    {
        switch ($object_table_class_name)
        {
            case ParticipantTable :: class_name() :
                return $this->get_participant_condition();
                break;
            case UserTable :: class_name() :
                return $this->get_user_condition();
                break;
            case GroupTable :: class_name() :
                return $this->get_group_condition();
                break;
        }
    }

    public function get_publication_id()
    {
        return $this->pid;
    }
    
    function get_survey_participant_publication_viewer_url($survey_participant_tracker)
    {
        $survey_id = DataManager :: retrieve_by_id(
            Publication :: class_name(),
            $survey_participant_tracker->get_survey_publication_id())->get_content_object_id();
        return $this->get_url(
            array(
                Manager :: PARAM_ACTION => Manager :: ACTION_VIEW,
                Manager :: PARAM_PUBLICATION_ID => $survey_participant_tracker->get_survey_publication_id(),
                Manager :: PARAM_INVITEE_ID => $survey_participant_tracker->get_user_id()));
    }
    
    
    function get_survey_participant_delete_url($user_id)
    {
        return $this->get_url(
            array(
                Manager :: PARAM_ACTION => Manager :: ACTION_DELETE_PARTICIPANT,
                self :: PARAM_PARTICIPANT_ID => $user_id));
    }
}
?>