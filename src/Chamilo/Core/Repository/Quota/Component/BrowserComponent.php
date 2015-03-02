<?php
namespace Chamilo\Core\Repository\Quota\Component;

use Chamilo\Core\Repository\Quota\Calculator;
use Chamilo\Core\Repository\Quota\DataClass\Request;
use Chamilo\Core\Repository\Quota\DataManager;
use Chamilo\Core\Repository\Quota\Manager;
use Chamilo\Core\Repository\Quota\Table\Request\RequestTable;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\Format\Structure\ActionBarRenderer;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Format\Table\PropertiesTable;
use Chamilo\Libraries\Format\Tabs\DynamicContentTab;
use Chamilo\Libraries\Format\Tabs\DynamicTabsRenderer;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Configuration\PlatformSetting;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\DatetimeUtilities;

class BrowserComponent extends Manager implements TableSupport
{

    private $table_type;

    private $calculator;

    public function run()
    {
        $reset_cache = (bool) \Chamilo\Libraries\Platform\Session\Request :: get(self :: PARAM_RESET_CACHE);
        $this->calculator = new Calculator($this->get_user(), $reset_cache);

        $html = array();

        $html[] = $this->render_header();

        $html[] = $this->get_action_bar()->as_html();

        $user_quota = array();
        $user_quota[] = '<h3>' . htmlentities(Translation :: get('UsedDiskSpace')) . '</h3>';
        $user_quota[] = Calculator :: get_bar(
            $this->calculator->get_user_disk_quota_percentage(),
            Filesystem :: format_file_size($this->calculator->get_used_user_disk_quota()) . ' / ' . Filesystem :: format_file_size(
                $this->calculator->get_maximum_user_disk_quota()));
        $user_quota[] = '<div style="clear: both;">&nbsp;</div>';

        $user_quota[] = $this->get_statistics();
        $user_quota[] = '<div style="clear: both;">&nbsp;</div>';

        $condition = new EqualityCondition(
            new PropertyConditionVariable(Request :: class_name(), Request :: PROPERTY_USER_ID),
            new StaticConditionVariable($this->get_user_id()));
        $user_requests = DataManager :: count(Request :: class_name(), $condition);

        if ($user_requests > 0 || \Chamilo\Core\Repository\Quota\Rights\Rights :: get_instance()->quota_is_allowed())
        {
            $tabs = new DynamicTabsRenderer('quota');
            $tabs->add_tab(
                new DynamicContentTab(
                    'personal',
                    Translation :: get('Personal'),
                    Theme :: getInstance()->getImagesPath() . 'tab/personal.png',
                    implode(PHP_EOL, $user_quota)));

            if ($user_requests > 0)
            {
                $this->table_type = RequestTable :: TYPE_PERSONAL;
                $table = new RequestTable($this);
                $tabs->add_tab(
                    new DynamicContentTab(
                        'personal_request',
                        Translation :: get('YourRequests'),
                        Theme :: getInstance()->getImagesPath() . 'tab/personal_request.png',
                        $table->as_html()));
            }

            if (\Chamilo\Core\Repository\Quota\Rights\Rights :: get_instance()->quota_is_allowed())
            {
                $platform_quota = array();
                $platform_quota[] = '<h3>' . htmlentities(Translation :: get('AggregatedUserDiskQuotas')) . '</h3>';
                $platform_quota[] = Calculator :: get_bar(
                    $this->calculator->get_aggregated_user_disk_quota_percentage(),
                    Filesystem :: format_file_size($this->calculator->get_used_aggregated_user_disk_quota()) . ' / ' . Filesystem :: format_file_size(
                        $this->calculator->get_maximum_aggregated_user_disk_quota()));
                $platform_quota[] = '<div style="clear: both;">&nbsp;</div>';

                $platform_quota[] = '<h3>' . htmlentities(Translation :: get('ReservedDiskSpace')) . '</h3>';
                $platform_quota[] = Calculator :: get_bar(
                    $this->calculator->get_reserved_disk_space_percentage(),
                    Filesystem :: format_file_size($this->calculator->get_used_reserved_disk_space()) . ' / ' . Filesystem :: format_file_size(
                        $this->calculator->get_maximum_reserved_disk_space()));
                $platform_quota[] = '<div style="clear: both;">&nbsp;</div>';

                $platform_quota[] = '<h3>' . htmlentities(Translation :: get('AllocatedDiskSpace')) . '</h3>';
                $platform_quota[] = Calculator :: get_bar(
                    $this->calculator->get_allocated_disk_space_percentage(),
                    Filesystem :: format_file_size($this->calculator->get_used_allocated_disk_space()) . ' / ' . Filesystem :: format_file_size(
                        $this->calculator->get_maximum_allocated_disk_space()));
                $platform_quota[] = '<div style="clear: both;">&nbsp;</div>';

                $tabs->add_tab(
                    new DynamicContentTab(
                        'platform',
                        Translation :: get('Platform'),
                        Theme :: getInstance()->getImagesPath() . 'tab/platform.png',
                        implode(PHP_EOL, $platform_quota)));

                $target_users = \Chamilo\Core\Repository\Quota\Rights\Rights :: get_instance()->get_target_users(
                    $this->get_user());

                if (count($target_users) > 0)
                {
                    $target_condition = new InCondition(
                        new PropertyConditionVariable(Request :: class_name(), Request :: PROPERTY_USER_ID),
                        $target_users);
                }
                else
                {
                    $target_condition = new EqualityCondition(
                        new PropertyConditionVariable(Request :: class_name(), Request :: PROPERTY_USER_ID),
                        new StaticConditionVariable(- 1));
                }

                $conditions = array();
                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(Request :: class_name(), Request :: PROPERTY_DECISION),
                    new StaticConditionVariable(Request :: DECISION_PENDING));
                if (! $this->get_user()->is_platform_admin())
                {
                    $conditions[] = $target_condition;
                }
                $condition = new AndCondition($conditions);

                if (DataManager :: count(Request :: class_name(), $condition) > 0)
                {
                    $this->table_type = RequestTable :: TYPE_PENDING;
                    $table = new RequestTable($this);
                    $tabs->add_tab(
                        new DynamicContentTab(
                            RequestTable :: TYPE_PENDING,
                            Translation :: get('PendingRequests'),
                            Theme :: getInstance()->getImagesPath() . 'decision/22/' . Request :: DECISION_PENDING .
                                 '.png',
                                $table->as_html()));
                }

                $conditions = array();
                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(Request :: class_name(), Request :: PROPERTY_DECISION),
                    new StaticConditionVariable(Request :: DECISION_GRANTED));
                if (! $this->get_user()->is_platform_admin())
                {
                    $conditions[] = $target_condition;
                }
                $condition = new AndCondition($conditions);
                if (DataManager :: count(Request :: class_name(), $condition) > 0)
                {
                    $this->table_type = RequestTable :: TYPE_GRANTED;
                    $table = new RequestTable($this);
                    $tabs->add_tab(
                        new DynamicContentTab(
                            RequestTable :: TYPE_GRANTED,
                            Translation :: get('GrantedRequests'),
                            Theme :: getInstance()->getImagesPath() . 'decision/22/' . Request :: DECISION_GRANTED .
                                 '.png',
                                $table->as_html()));
                }

                $conditions = array();
                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(Request :: class_name(), Request :: PROPERTY_DECISION),
                    new StaticConditionVariable(Request :: DECISION_DENIED));
                if (! $this->get_user()->is_platform_admin())
                {
                    $conditions[] = $target_condition;
                }
                $condition = new AndCondition($conditions);

                if (DataManager :: count(Request :: class_name(), $condition) > 0)
                {
                    $this->table_type = RequestTable :: TYPE_DENIED;
                    $table = new RequestTable($this);
                    $tabs->add_tab(
                        new DynamicContentTab(
                            RequestTable :: TYPE_DENIED,
                            Translation :: get('DeniedRequests'),
                            Theme :: getInstance()->getImagesPath() . 'decision/22/' . Request :: DECISION_DENIED . '.png',
                            $table->as_html()));
                }
            }
        }

        if ($user_requests > 0 || \Chamilo\Core\Repository\Quota\Rights\Rights :: get_instance()->quota_is_allowed())
        {
            $html[] = $tabs->render();
        }
        else
        {
            $html[] = implode(PHP_EOL, $user_quota);
        }

        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    public function get_statistics()
    {
        $html = array();
        $html[] = '<h3>' . htmlentities(Translation :: get('RepositoryStatistics')) . '</h3>';
        /**
         * Disabled right now since database quota are not enforced anywhere, this is just a visual reference $html[] =
         * Calculator :: get_bar($this->calculator->get_user_database_percentage(),
         * $this->calculator->get_used_database_quota() .
         * ' / ' . $this->calculator->get_maximum_database_quota());
         */

        $properties = array();
        $properties[Translation :: get('NumberOfContentObjects')] = $this->calculator->get_used_database_quota();

        $type_counts = array();
        $condition = new EqualityCondition(
            new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_OWNER_ID),
            new StaticConditionVariable($this->get_user_id()));
        $most_used = null;

        foreach (\Chamilo\Core\Repository\Storage\DataManager :: get_registered_types() as $type)
        {
            $type_counts[$type] = \Chamilo\Core\Repository\Storage\DataManager :: count_active_content_objects(
                $type,
                new DataClassCountParameters($condition));
            if ($type_counts[$type] > $type_counts[$most_used])
            {
                $most_used = $type;
            }
        }

        arsort($type_counts);

        $properties[Translation :: get('MostUsedContentObjectType')] = Translation :: get(
            'TypeName',
            null,
            ClassnameUtilities :: getInstance()->getNamespaceFromClassname($most_used)) . ' (' . $type_counts[$most_used] .
             ')';

        $reference_count = $type_counts[$most_used] / 2;

        unset($type_counts[$most_used]);

        $frequent = array();

        foreach ($type_counts as $type => $count)
        {
            if ($count >= $reference_count)
            {
                $frequent[] = Translation :: get(
                    'TypeName',
                    null,
                    ClassnameUtilities :: getInstance()->getNamespaceFromClassname($type)) . ' (' . $count . ')';
            }
        }

        $properties[Translation :: get('OtherFrequentlyUsedContentObjectTypes')] = implode('<br />', $frequent);

        $properties[Translation :: get('AvailableDiskSpace')] = Filesystem :: format_file_size(
            $this->calculator->get_available_user_disk_quota());

        $condition = new EqualityCondition(
            new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_OWNER_ID),
            new StaticConditionVariable($this->get_user_id()));
        $oldest_object = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_active_content_objects(
            ContentObject :: class_name(),
            new DataClassRetrievesParameters($condition))->next_result();

        if ($oldest_object instanceof ContentObject)
        {
            $properties[Translation :: get('OldestContentObject')] = '<a href="' .
                 $this->get_parent()->get_content_object_viewing_url($oldest_object) . '">' . $oldest_object->get_title() .
                 '</a> - ' . DatetimeUtilities :: format_locale_date(null, $oldest_object->get_creation_date());
        }

        $table = new PropertiesTable($properties);
        $html[] = '<div class="quota_statistics">';
        $html[] = $table->toHTML();
        $html[] = '</div>';
        return implode(PHP_EOL, $html);
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
            case RequestTable :: TYPE_PENDING :
                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(Request :: class_name(), Request :: PROPERTY_DECISION),
                    new StaticConditionVariable(Request :: DECISION_PENDING));
                break;
            case RequestTable :: TYPE_PERSONAL :
                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(Request :: class_name(), Request :: PROPERTY_USER_ID),
                    new StaticConditionVariable($this->get_user_id()));
                break;
            case RequestTable :: TYPE_GRANTED :
                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(Request :: class_name(), Request :: PROPERTY_DECISION),
                    new StaticConditionVariable(Request :: DECISION_GRANTED));
                break;
            case RequestTable :: TYPE_DENIED :
                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(Request :: class_name(), Request :: PROPERTY_DECISION),
                    new StaticConditionVariable(Request :: DECISION_DENIED));
                break;
        }

        if (! $this->get_user()->is_platform_admin() &&
             \Chamilo\Core\Repository\Quota\Rights\Rights :: get_instance()->quota_is_allowed() &&
             $this->table_type != RequestTable :: TYPE_PERSONAL)
        {
            $target_users = \Chamilo\Core\Repository\Quota\Rights\Rights :: get_instance()->get_target_users(
                $this->get_user());

            if (count($target_users) > 0)
            {
                $conditions[] = new InCondition(
                    new PropertyConditionVariable(Request :: class_name(), Request :: PROPERTY_USER_ID),
                    $target_users);
            }
            else
            {
                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(Request :: class_name(), Request :: PROPERTY_USER_ID),
                    new StaticConditionVariable(- 1));
            }
        }

        return new AndCondition($conditions);
    }

    public function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $allow_upgrade = PlatformSetting :: get('allow_upgrade', __NAMESPACE__);
        $maximum_user_disk_space = PlatformSetting :: get('maximum_user', __NAMESPACE__);

        if ($this->calculator->upgrade_allowed())
        {
            $action_bar->add_common_action(
                new ToolbarItem(
                    Translation :: get('UpgradeQuota'),
                    Theme :: getInstance()->getImagesPath() . 'action/upgrade.png',
                    $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_UPGRADE))));
        }

        if ($this->calculator->request_allowed())
        {
            $action_bar->add_common_action(
                new ToolbarItem(
                    Translation :: get('RequestUpgrade'),
                    Theme :: getInstance()->getImagesPath() . 'action/request.png',
                    $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE))));
        }

        if ($this->get_user()->is_platform_admin())
        {
            $action_bar->add_tool_action(
                new ToolbarItem(
                    Translation :: get('ConfigureManagementRights'),
                    Theme :: getInstance()->getImagesPath() . 'action/rights.png',
                    $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_RIGHTS))));

            $action_bar->add_tool_action(
                new ToolbarItem(
                    Translation :: get('ResetTotal'),
                    Theme :: getInstance()->getImagesPath() . 'action/reset.png',
                    $this->get_url(array(self :: PARAM_RESET_CACHE => 1))));
        }

        return $action_bar;
    }

    public function get_table_type()
    {
        return $this->table_type;
    }
}
