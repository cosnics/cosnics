<?php
namespace Chamilo\Core\Repository\Quota;

use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Platform\Configuration\PlatformSetting;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Condition\InequalityCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\File\Cache\PhpFileCache;
use Chamilo\Libraries\Storage\Query\Variable\FunctionConditionVariable;
use Chamilo\Libraries\Storage\Parameters\RecordRetrieveParameters;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\DataManager\DataManager;

/**
 * This class provides some functionality to manage user disk quota
 *
 * @package repository.quota
 * @author Bart Mollet
 * @author Dieter De Neef
 * @author Hans De Bisschop
 */
class Calculator
{
    const POLICY_USER = 0;
    const POLICY_GROUP_HIGHEST = 1;
    const POLICY_GROUP_LOWEST = 2;
    const POLICY_HIGHEST = 3;
    const POLICY_LOWEST = 4;

    /**
     * The user
     *
     * @var \core\user\User
     */
    private $user;

    /**
     * Create a new Calculator
     *
     * @param $user \core\user\User
     */
    public function __construct(User $user, $reset = false)
    {
        $this->user = $user;
        if ($reset)
        {
            $this->reset_cache();
        }
    }

    /**
     * USER DISK QUOTA
     */
    public function get_used_user_disk_quota()
    {
        if (is_null($this->used_user_disk_quota))
        {
            $this->used_user_disk_quota = \Chamilo\Core\Repository\Storage\DataManager :: get_used_disk_space(
                $this->user->get_id());
        }
        return $this->used_user_disk_quota;
    }

    public function get_maximum_user_disk_quota()
    {
        if (is_null($this->maximum_user_disk_quota))
        {
            $policy = PlatformSetting :: get('quota_policy', __NAMESPACE__);
            $fallback = PlatformSetting :: get('quota_fallback', __NAMESPACE__);
            $fallback_user = PlatformSetting :: get('quota_fallback_user', __NAMESPACE__);

            switch ($policy)
            {
                case self :: POLICY_USER :
                    if ($this->user->get_disk_quota() || ! $fallback)
                    {
                        $this->maximum_user_disk_quota = $this->user->get_disk_quota();
                    }
                    else
                    {
                        $this->maximum_user_disk_quota = ($fallback_user == 0 ? $this->get_group_highest() : $this->get_group_lowest());
                    }
                    break;
                case self :: POLICY_GROUP_HIGHEST :
                    $group = $this->get_group_highest();
                    $this->maximum_user_disk_quota = $group || ! $fallback ? $group : $this->user->get_disk_quota();
                    break;
                case self :: POLICY_GROUP_LOWEST :
                    $group = $this->get_group_lowest();
                    $this->maximum_user_disk_quota = $group || ! $fallback ? $group : $this->user->get_disk_quota();
                    break;
                case self :: POLICY_HIGHEST :
                    $group = $this->get_group_highest();
                    $this->maximum_user_disk_quota = ($group > $this->user->get_disk_quota() ? $group : $this->user->get_disk_quota());
                    break;
                case self :: POLICY_LOWEST :
                    $group = $this->get_group_lowest();
                    $this->maximum_user_disk_quota = ($group > $this->user->get_disk_quota() || ! $group ? $this->user->get_disk_quota() : $group);
                    break;
                default :
                    $this->maximum_user_disk_quota = $this->user->get_disk_quota();
                    break;
            }
        }
        return $this->maximum_user_disk_quota;
    }

    public function get_group_lowest()
    {
        $user_group_ids = $this->user->get_groups(true);

        $conditions = array();
        $conditions[] = new InCondition(
            new PropertyConditionVariable(Group :: class_name(), Group :: PROPERTY_ID),
            $user_group_ids);
        $conditions[] = new InequalityCondition(
            new PropertyConditionVariable(Group :: class_name(), Group :: PROPERTY_DISK_QUOTA),
            InequalityCondition :: GREATER_THAN,
            new StaticConditionVariable(0));
        $condition = new AndCondition($conditions);

        $group = \Chamilo\Core\Group\Storage\DataManager :: retrieve(
            \Chamilo\Core\Group\Storage\DataClass\Group :: class_name(),
            new DataClassRetrieveParameters(
                $condition,
                array(
                    new OrderBy(
                        new PropertyConditionVariable(Group :: class_name(), Group :: PROPERTY_DISK_QUOTA),
                        SORT_ASC))));

        return $group instanceof Group ? $group->get_disk_quota() : 0;
    }

    public function get_group_highest()
    {
        $user_group_ids = $this->user->get_groups(true);

        $conditions = array();
        $conditions[] = new InCondition(
            new PropertyConditionVariable(Group :: class_name(), Group :: PROPERTY_ID),
            $user_group_ids);
        $conditions[] = new InequalityCondition(
            new PropertyConditionVariable(Group :: class_name(), Group :: PROPERTY_DISK_QUOTA),
            InequalityCondition :: GREATER_THAN,
            new StaticConditionVariable(0));
        $condition = new AndCondition($conditions);

        $group = \Chamilo\Core\Group\Storage\DataManager :: retrieve(
            \Chamilo\Core\Group\Storage\DataClass\Group :: class_name(),
            new DataClassRetrieveParameters(
                $condition,
                array(new OrderBy(new PropertyConditionVariable(Group :: class_name(), Group :: PROPERTY_DISK_QUOTA)))));

        return $group instanceof Group ? $group->get_disk_quota() : 0;
    }

    public function get_available_user_disk_quota()
    {
        $quota = $this->get_maximum_user_disk_quota() - $this->get_used_user_disk_quota();
        if ($quota > 0)
        {
            return $quota;
        }
        else
        {
            return 0;
        }
    }

    public function get_user_disk_quota_percentage()
    {
        return 100 * $this->get_used_user_disk_quota() / $this->get_maximum_user_disk_quota();
    }

    /**
     * AGGREGATED USER DISK QUOTA
     */
    public function get_used_aggregated_user_disk_quota()
    {
        if (is_null($this->used_aggregated_user_disk_quota))
        {
            $this->used_aggregated_user_disk_quota = \Chamilo\Core\Repository\Storage\DataManager :: get_used_disk_space();
        }
        return $this->used_aggregated_user_disk_quota;
    }

    public function get_maximum_aggregated_user_disk_quota()
    {
        if (is_null($this->maximum_aggregated_user_disk_quota))
        {
            $this->maximum_aggregated_user_disk_quota = $this->get_total_user_disk_quota();
        }
        return $this->maximum_aggregated_user_disk_quota;
    }

    public function get_available_aggregated_user_disk_quota()
    {
        $quota = $this->get_maximum_aggregated_user_disk_quota() - $this->get_used_aggregated_user_disk_quota();
        if ($quota > 0)
        {
            return $quota;
        }
        else
        {
            return 0;
        }
    }

    public function get_aggregated_user_disk_quota_percentage()
    {
        return 100 * $this->get_used_aggregated_user_disk_quota() / $this->get_maximum_aggregated_user_disk_quota();
    }

    /**
     * RESERVED DISK SPACE
     */
    public function get_used_reserved_disk_space()
    {
        return $this->get_maximum_aggregated_user_disk_quota();
    }

    public function get_maximum_reserved_disk_space()
    {
        return disk_total_space(Path :: getInstance()->getStoragePath());
    }

    public function get_available_reserved_disk_space()
    {
        $quota = $this->get_maximum_reserved_disk_space() - $this->get_used_reserved_disk_space();
        if ($quota > 0)
        {
            return $quota;
        }
        else
        {
            return 0;
        }
    }

    public function get_reserved_disk_space_percentage()
    {
        return 100 * $this->get_used_reserved_disk_space() / $this->get_maximum_reserved_disk_space();
    }

    /**
     * ALLOCATED DISK SPACE
     */
    public function get_used_allocated_disk_space()
    {
        return $this->get_used_aggregated_user_disk_quota();
    }

    public function get_maximum_allocated_disk_space()
    {
        return disk_total_space(Path :: getInstance()->getStoragePath());
    }

    public function get_available_allocated_disk_space()
    {
        $quota = $this->get_maximum_allocated_disk_space() - $this->get_used_allocated_disk_space();
        if ($quota > 0)
        {
            return $quota;
        }
        else
        {
            return 0;
        }
    }

    public function get_allocated_disk_space_percentage()
    {
        return 100 * $this->get_used_allocated_disk_space() / $this->get_maximum_allocated_disk_space();
    }

    /**
     * DATABASE SPACE
     */
    public function get_used_database_quota()
    {
        if (is_null($this->used_database_quota))
        {
            $condition = new AndCondition(
                new EqualityCondition(
                    new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_OWNER_ID),
                    new StaticConditionVariable($this->user->get_id())),
                new NotCondition(
                    new InCondition(
                        new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_TYPE),
                        \Chamilo\Core\Repository\Storage\DataManager :: get_active_helper_types())));
            $this->used_database_quota = \Chamilo\Core\Repository\Storage\DataManager :: count_active_content_objects(
                ContentObject :: class_name(),
                $condition);
        }
        return $this->used_database_quota;
    }

    public function get_maximum_database_quota()
    {
        if (is_null($this->maximum_database_quota))
        {
            $this->maximum_database_quota = $this->user->get_database_quota();
        }
        return $this->maximum_database_quota;
    }

    public function get_available_database_quota()
    {
        $quota = $this->get_maximum_database_quota() - $this->get_used_database_quota();
        if ($quota > 0)
        {
            return $quota;
        }
        else
        {
            return 0;
        }
    }

    public function get_user_database_percentage()
    {
        return 100 * $this->get_used_database_quota() / $this->get_maximum_database_quota();
    }

    public function upgrade_allowed()
    {
        $quota_step = (int) PlatformSetting :: get('step', __NAMESPACE__);
        $allow_upgrade = (boolean) PlatformSetting :: get('allow_upgrade', __NAMESPACE__);
        $maximum_user_disk_space = (int) PlatformSetting :: get('maximum_user', __NAMESPACE__);
        $policy = PlatformSetting :: get('quota_policy', __NAMESPACE__);

        if (! $this->uses_user_disk_quota())
        {
            return false;
        }

        if (\Chamilo\Core\Repository\Quota\Rights\Rights :: get_instance()->quota_is_allowed() &&
             $this->get_available_allocated_disk_space() > $quota_step)
        {
            return true;
        }

        if ($allow_upgrade)
        {
            if ($maximum_user_disk_space == 0)
            {
                if ($this->get_available_allocated_disk_space() > $quota_step)
                {
                    return true;
                }
            }
            else
            {
                if ($this->user->get_disk_quota() < $maximum_user_disk_space)
                {
                    if ($this->get_available_allocated_disk_space() > $quota_step)
                    {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    public function request_allowed()
    {
        $quota_step = (int) PlatformSetting :: get('step', __NAMESPACE__);
        $allow_request = PlatformSetting :: get('allow_request', __NAMESPACE__);
        $policy = PlatformSetting :: get('quota_policy', __NAMESPACE__);

        if (! $this->uses_user_disk_quota())
        {
            return false;
        }

        if (\Chamilo\Core\Repository\Quota\Rights\Rights :: get_instance()->quota_is_allowed() &&
             $this->get_available_allocated_disk_space() > $quota_step)
        {
            return true;
        }

        if ($allow_request)
        {
            if ($this->get_available_allocated_disk_space() > $quota_step)
            {
                return true;
            }
        }

        return false;
    }

    public function uses_user_disk_quota()
    {
        $policy = PlatformSetting :: get('quota_policy', __NAMESPACE__);
        $fallback = PlatformSetting :: get('quota_fallback', __NAMESPACE__);
        $fallback_user = PlatformSetting :: get('quota_fallback_user', __NAMESPACE__);

        switch ($policy)
        {
            case self :: POLICY_USER :
                return ($this->user->get_disk_quota() || ! $fallback ? true : false);
                break;
            case self :: POLICY_GROUP_HIGHEST :
                $group = $this->get_group_highest();
                return $group || ! $fallback ? false : true;
                break;
            case self :: POLICY_GROUP_LOWEST :
                $group = $this->get_group_lowest();
                return $group || ! $fallback ? false : true;
                break;
            case self :: POLICY_HIGHEST :
                $group = $this->get_group_highest();
                return ($group > $this->user->get_disk_quota() ? false : true);
                break;
            case self :: POLICY_LOWEST :
                $group = $this->get_group_lowest();
                return ($group > $this->user->get_disk_quota() || ! $group ? true : false);
                break;
            default :
                return true;
                break;
        }
    }

    /**
     * Build a bar-view of the used quota.
     *
     * @param $percent float The percentage of the bar that is in use
     * @param $status string A status message which will be displayed below the bar.
     * @return string HTML representation of the requested bar.
     */
    public static function get_bar($percent, $status)
    {
        $html = array();

        $html[] = '<div class="usage_information">';
        $html[] = '<div class="usage_bar">';

        for ($i = 0; $i < 100; $i ++)
        {
            if ($percent > $i)
            {
                if ($i >= 90)
                {
                    $class = 'very_critical';
                }
                elseif ($i >= 80)
                {
                    $class = 'critical';
                }
                else
                {
                    $class = 'used';
                }
            }
            else
            {
                $class = '';
            }
            $html[] = '<div class="' . $class . '"></div>';
        }

        $html[] = '</div>';
        $html[] = '<div class="usage_status">' . $status . ' &ndash; ' . round($percent, 2) . ' %</div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    public function reset_cache()
    {
        $cache = new PhpFileCache(Path :: getInstance()->getCachePath(__NAMESPACE__));
        $cache->delete('total_user_disk_quota');
    }

    public function get_total_user_disk_quota($reset = false)
    {
        $cache = new PhpFileCache(Path :: getInstance()->getCachePath(__NAMESPACE__));

        if ($reset)
        {
            $cache->delete('total_user_disk_quota');
        }

        if ($cache->contains('total_user_disk_quota'))
        {
            $total_quota = $cache->fetch('total_user_disk_quota');
        }
        else
        {
            $policy = PlatformSetting :: get('quota_policy', __NAMESPACE__);
            $fallback = PlatformSetting :: get('quota_fallback', __NAMESPACE__);

            if ($policy == Calculator :: POLICY_USER && ! $fallback)
            {
                $property = new FunctionConditionVariable(
                    FunctionConditionVariable :: SUM,
                    new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_DISK_QUOTA),
                    'disk_quota');

                $parameters = new RecordRetrieveParameters(new DataClassProperties($property));

                $record = DataManager :: record(User :: class_name(), $parameters);
                $total_quota = $record['disk_quota'];
            }
            else
            {
                $users = DataManager :: retrieves(User :: class_name());

                $total_quota = 0;

                while ($user = $users->next_result())
                {
                    $calculator = new Calculator($user);
                    $total_quota += $calculator->get_maximum_user_disk_quota();
                }

                $total_quota;
            }

            $cache->save('total_user_disk_quota', $total_quota);
        }

        return $total_quota;
    }
}
