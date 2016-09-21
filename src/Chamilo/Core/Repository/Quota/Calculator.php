<?php
namespace Chamilo\Core\Repository\Quota;

use Chamilo\Configuration\Configuration;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Repository\Filter\FilterData;
use Chamilo\Core\Repository\Quota\Service\CalculatorCacheService;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Condition\InequalityCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package Chamilo\Core\Repository\Quota
 * @author Bart Mollet
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Dieter De Neef
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Calculator
{
    const POLICY_USER = 0;
    const POLICY_GROUP_HIGHEST = 1;
    const POLICY_GROUP_LOWEST = 2;
    const POLICY_HIGHEST = 3;
    const POLICY_LOWEST = 4;

    /**
     *
     * @var \Chamilo\Core\User\Storage\DataClass\User
     */
    private $user;

    /**
     *
     * @var integer
     */
    private $usedUserDiskQuota;

    /**
     *
     * @var integer
     */
    private $maximumUserDiskQuota;

    /**
     *
     * @var integer
     */
    private $usedAggregatedUserDiskQuota;

    /**
     *
     * @var integer
     */
    private $usedDatabaseQuota;

    /**
     *
     * @var integer
     */
    private $maximumDatabaseQuota;

    /**
     *
     * @var \Chamilo\Core\Repository\Quota\Service\CalculatorCacheService
     */
    private $calculatorCacheService;

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param boolean $reset
     */
    public function __construct(User $user, $reset = false)
    {
        $this->user = $user;

        if ($reset)
        {
            $this->resetCache();
        }
    }

    /**
     * USER DISK QUOTA
     */
    /**
     *
     * @return integer
     */
    public function getUsedUserDiskQuota()
    {
        if (is_null($this->usedUserDiskQuota))
        {
            $this->usedUserDiskQuota = \Chamilo\Core\Repository\Storage\DataManager :: get_used_disk_space(
                $this->user->getId());
        }

        return $this->usedUserDiskQuota;
    }

    /**
     *
     * @return integer
     */
    public function getMaximumUserDiskQuota()
    {
        if (is_null($this->maximumUserDiskQuota))
        {
            $policy = Configuration :: get_instance()->get_setting(array('Chamilo\Core\Repository', 'quota_policy'));
            $fallback = Configuration :: get_instance()->get_setting(array('Chamilo\Core\Repository', 'quota_fallback'));
            $fallbackUser = Configuration :: get_instance()->get_setting(array('Chamilo\Core\Repository', 'quota_fallback_user'));

            switch ($policy)
            {
                case self :: POLICY_USER :
                    if ($this->user->get_disk_quota() || ! $fallback)
                    {
                        $this->maximumUserDiskQuota = $this->user->get_disk_quota();
                    }
                    else
                    {
                        $this->maximumUserDiskQuota = ($fallbackUser == 0 ? $this->getGroupHighest() : $this->getGroupLowest());
                    }
                    break;
                case self :: POLICY_GROUP_HIGHEST :
                    $group = $this->getGroupHighest();
                    $this->maximumUserDiskQuota = $group || ! $fallback ? $group : $this->user->get_disk_quota();
                    break;
                case self :: POLICY_GROUP_LOWEST :
                    $group = $this->getGroupLowest();
                    $this->maximumUserDiskQuota = $group || ! $fallback ? $group : $this->user->get_disk_quota();
                    break;
                case self :: POLICY_HIGHEST :
                    $group = $this->getGroupHighest();
                    $this->maximumUserDiskQuota = ($group > $this->user->get_disk_quota() ? $group : $this->user->get_disk_quota());
                    break;
                case self :: POLICY_LOWEST :
                    $group = $this->getGroupLowest();
                    $this->maximumUserDiskQuota = ($group > $this->user->get_disk_quota() || ! $group ? $this->user->get_disk_quota() : $group);
                    break;
                default :
                    $this->maximumUserDiskQuota = $this->user->get_disk_quota();
                    break;
            }
        }

        return $this->maximumUserDiskQuota;
    }

    /**
     *
     * @return integer
     */
    public function getGroupLowest()
    {
        $userGroupIds = $this->user->get_groups(true);

        $conditions = array();
        $conditions[] = new InCondition(
            new PropertyConditionVariable(Group :: class_name(), Group :: PROPERTY_ID),
            $userGroupIds);
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

    /**
     *
     * @return integer
     */
    public function getGroupHighest()
    {
        $userGroupIds = $this->user->get_groups(true);

        $conditions = array();
        $conditions[] = new InCondition(
            new PropertyConditionVariable(Group :: class_name(), Group :: PROPERTY_ID),
            $userGroupIds);
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

    /**
     *
     * @return integer
     */
    public function getAvailableUserDiskQuota()
    {
        $quota = $this->getMaximumUserDiskQuota() - $this->getUsedUserDiskQuota();

        return $quota > 0 ? $quota : 0;
    }

    /**
     *
     * @param integer $requestedStorageSize
     * @return boolean
     */
    public function canUpload($requestedStorageSize)
    {
        if (! $this->isEnabled())
        {
            return true;
        }

        return $this->getAvailableUserDiskQuota() > $requestedStorageSize;
    }

    public function isEnabled()
    {
        return (boolean) Configuration :: get_instance()->get_setting(array('Chamilo\Core\Repository', 'enable_quota'));
    }

    /**
     *
     * @return integer
     */
    public function getUserDiskQuotaPercentage()
    {
        return 100 * $this->getUsedUserDiskQuota() / $this->getMaximumUserDiskQuota();
    }

    /**
     * AGGREGATED USER DISK QUOTA
     */
    /**
     *
     * @return integer
     */
    public function getUsedAggregatedUserDiskQuota()
    {
        if (is_null($this->usedAggregatedUserDiskQuota))
        {
            $this->usedAggregatedUserDiskQuota = \Chamilo\Core\Repository\Storage\DataManager :: get_used_disk_space();
        }

        return $this->usedAggregatedUserDiskQuota;
    }

    /**
     *
     * @return integer
     */
    public function getMaximumAggregatedUserDiskQuota()
    {
        if (is_null($this->maximumAggregatedUserDiskQuota))
        {
            $this->maximumAggregatedUserDiskQuota = $this->getTotalUserDiskQuota();
        }

        return $this->maximumAggregatedUserDiskQuota;
    }

    /**
     *
     * @return integer
     */
    public function getAvailableAggregatedUserDiskQuota()
    {
        $quota = $this->getMaximumAggregatedUserDiskQuota() - $this->getUsedAggregatedUserDiskQuota();

        return $quota > 0 ? $quota : 0;
    }

    /**
     *
     * @return integer
     */
    public function getAggregatedUserDiskQuotaPercentage()
    {
        return 100 * $this->getUsedAggregatedUserDiskQuota() / $this->getMaximumAggregatedUserDiskQuota();
    }

    /**
     * RESERVED DISK SPACE
     */
    /**
     *
     * @return integer
     */
    public function getUsedReservedDiskSpace()
    {
        return $this->getMaximumAggregatedUserDiskQuota();
    }

    /**
     *
     * @return integer
     */
    public function getMaximumReservedDiskSpace()
    {
        return disk_total_space(Path :: getInstance()->getRepositoryPath());
    }

    /**
     *
     * @return integer
     */
    public function getAvailableReservedDiskSpace()
    {
        $quota = $this->getMaximumReservedDiskSpace() - $this->getUsedReservedDiskSpace();

        return $quota > 0 ? $quota : 0;
    }

    /**
     *
     * @return integer
     */
    public function getReservedDiskSpacePercentage()
    {
        return 100 * $this->getUsedReservedDiskSpace() / $this->getMaximumReservedDiskSpace();
    }

    /**
     * ALLOCATED DISK SPACE
     */
    /**
     *
     * @return integer
     */
    public function getUsedAllocatedDiskSpace()
    {
        return $this->getUsedAggregatedUserDiskQuota();
    }

    /**
     *
     * @return integer
     */
    public function getMaximumAllocatedDiskSpace()
    {
        return disk_total_space(Path :: getInstance()->getRepositoryPath());
    }

    /**
     *
     * @return integer
     */
    public function getAvailableAllocatedDiskSpace()
    {
        $quota = $this->getMaximumAllocatedDiskSpace() - $this->getUsedAllocatedDiskSpace();
    }

    /**
     *
     * @return integer
     */
    public function getAllocatedDiskSpacePercentage()
    {
        return 100 * $this->getUsedAllocatedDiskSpace() / $this->getMaximumAllocatedDiskSpace();
    }

    /**
     * DATABASE SPACE
     */
    /**
     *
     * @return integer
     */
    public function getUsedDatabaseQuota()
    {
        if (is_null($this->usedDatabaseQuota))
        {
            $condition = new AndCondition(
                new EqualityCondition(
                    new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_OWNER_ID),
                    new StaticConditionVariable($this->user->get_id())),
                new NotCondition(
                    new InCondition(
                        new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_TYPE),
                        \Chamilo\Core\Repository\Storage\DataManager :: get_active_helper_types())));

            $this->usedDatabaseQuota = \Chamilo\Core\Repository\Storage\DataManager :: count_active_content_objects(
                ContentObject :: class_name(),
                $condition);
        }

        return $this->usedDatabaseQuota;
    }

    /**
     *
     * @return integer
     */
    public function getMaximumDatabaseQuota()
    {
        if (is_null($this->maximumDatabaseQuota))
        {
            $this->maximumDatabaseQuota = $this->user->get_database_quota();
        }
        return $this->maximumDatabaseQuota;
    }

    /**
     *
     * @return integer
     */
    public function getAvailableDatabaseQuota()
    {
        $quota = $this->getMaximumDatabaseQuota() - $this->getUsedDatabaseQuota();

        return $quota > 0 ? $quota : 0;
    }

    /**
     *
     * @return integer
     */
    public function getUserDatabasePercentage()
    {
        return 100 * $this->getUsedDatabaseQuota() / $this->getMaximumDatabaseQuota();
    }

    /**
     *
     * @return boolean
     */
    public function upgradeAllowed()
    {
        if (! $this->isEnabled())
        {
            return false;
        }

        $quotaStep = (int) Configuration :: get_instance()->get_setting(array('Chamilo\Core\Repository', 'step'));
        $allowUpgrade = (boolean) Configuration :: get_instance()->get_setting(array('Chamilo\Core\Repository', 'allow_upgrade'));
        $maximumUserDiskSpace = (int) Configuration :: get_instance()->get_setting(array('Chamilo\Core\Repository', 'maximum_user'));

        if (! $this->usesUserDiskQuota())
        {
            return false;
        }

        if (\Chamilo\Core\Repository\Quota\Rights\Rights :: get_instance()->quota_is_allowed() &&
             $this->getAvailableAllocatedDiskSpace() > $quotaStep)
        {
            return true;
        }

        if ($allowUpgrade)
        {
            if ($maximumUserDiskSpace == 0)
            {
                if ($this->getAvailableAllocatedDiskSpace() > $quotaStep)
                {
                    return true;
                }
            }
            else
            {
                if ($this->user->get_disk_quota() < $maximumUserDiskSpace)
                {
                    if ($this->getAvailableAllocatedDiskSpace() > $quotaStep)
                    {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     *
     * @return boolean
     */
    public function requestAllowed()
    {
        if (! $this->isEnabled())
        {
            return false;
        }

        $quotaStep = (int) Configuration::get_instance()->get_setting(array('Chamilo\Core\Repository', 'step'));
        $allowRequest = Configuration::get_instance()->get_setting(array('Chamilo\Core\Repository', 'allow_request'));

        if (! $this->usesUserDiskQuota())
        {
            return false;
        }

        if (\Chamilo\Core\Repository\Quota\Rights\Rights :: get_instance()->quota_is_allowed() &&
             $this->getAvailableAllocatedDiskSpace() > $quotaStep)
        {
            return true;
        }

        if ($allowRequest)
        {
            if ($this->getAvailableAllocatedDiskSpace() > $quotaStep)
            {
                return true;
            }
        }

        return false;
    }

    /**
     *
     * @return boolean
     */
    public function usesUserDiskQuota()
    {
        $policy = Configuration::get_instance()->get_setting(array('Chamilo\Core\Repository', 'quota_policy'));
        $fallback = Configuration::get_instance()->get_setting(array('Chamilo\Core\Repository', 'quota_fallback'));
        $fallbackUser = Configuration::get_instance()->get_setting(array('Chamilo\Core\Repository', 'quota_fallback_user'));

        switch ($policy)
        {
            case self :: POLICY_USER :
                if ($this->user->get_disk_quota() || ! $fallback)
                {
                    return true;
                }
                else
                {
                    if ($fallbackUser == 0)
                    {
                        $group = $this->getGroupHighest();
                        return ($group > $this->user->get_disk_quota() ? false : true);
                    }
                    else
                    {
                        $group = $this->getGroupLowest();
                        return $group || ! $fallback ? false : true;
                    }
                }
                break;
            case self :: POLICY_GROUP_HIGHEST :
                $group = $this->getGroupHighest();
                return $group || ! $fallback ? false : true;
                break;
            case self :: POLICY_GROUP_LOWEST :
                $group = $this->getGroupLowest();
                return $group || ! $fallback ? false : true;
                break;
            case self :: POLICY_HIGHEST :
                $group = $this->getGroupHighest();
                return ($group > $this->user->get_disk_quota() ? false : true);
                break;
            case self :: POLICY_LOWEST :
                $group = $this->getGroupLowest();
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
    public static function getBar($percent, $status)
    {
        $html = array();

        if ($percent >= 100)
        {
            $percent = 100;
        }

        if ($percent >= 90)
        {
            $class = 'progress-bar-danger';
        }
        elseif ($percent >= 80)
        {
            $class = 'progress-bar-warning';
        }
        else
        {
            $class = 'progress-bar-success';
        }

        $displayPercent = round($percent);

        $html[] = '<div class="progress">';
        $html[] = '<div class="progress-bar progress-bar-striped ' . $class . '" role="progressbar" aria-valuenow="' .
             $displayPercent . '" aria-valuemin="0" aria-valuemax="100" style="width: ' . $displayPercent .
             '%; min-width: 2em;">';
        $html[] = $status . ' &ndash; ' . $displayPercent . '%';
        $html[] = '</div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    private function getCalculatorCacheService()
    {
        if (! isset($this->calculatorCacheService))
        {
            $this->calculatorCacheService = new CalculatorCacheService();
        }

        return $this->calculatorCacheService;
    }

    public function resetCache()
    {
        $this->getCalculatorCacheService()->clearForIdentifiers(
            array(CalculatorCacheService :: IDENTIFIER_TOTAL_USER_DISK_QUOTA));
    }

    public function getTotalUserDiskQuota($reset = false)
    {
        if ($reset)
        {
            $this->getCalculatorCacheService()->clearForIdentifiers(
                array(CalculatorCacheService :: IDENTIFIER_TOTAL_USER_DISK_QUOTA));
        }

        return $this->getCalculatorCacheService()->getTotalUserDiskQuota();
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Form\FormValidator $form
     */
    public function addUploadWarningToForm(FormValidator $form)
    {
        $enableQuota = (boolean) Configuration :: get_instance()->get_setting(array('Chamilo\Core\Repository', 'enable_quota'));

        $postMaxSize = Filesystem :: interpret_file_size(ini_get('post_max_size'));
        $uploadMaxFilesize = Filesystem :: interpret_file_size(ini_get('upload_max_filesize'));

        $maximumServerSize = $postMaxSize < $uploadMaxFilesize ? $uploadMaxFilesize : $postMaxSize;

        if ($enableQuota && $this->getAvailableUserDiskQuota() < $maximumServerSize)
        {
            $maximumSize = $this->getAvailableUserDiskQuota();

            $redirect = new Redirect(
                array(
                    \Chamilo\Libraries\Architecture\Application\Application :: PARAM_CONTEXT => \Chamilo\Core\Repository\Manager :: context(),
                    \Chamilo\Core\Repository\Manager :: PARAM_ACTION => \Chamilo\Core\Repository\Manager :: ACTION_QUOTA,
                    FilterData :: FILTER_CATEGORY => null,
                    \Chamilo\Core\Repository\Quota\Manager :: PARAM_ACTION => null));
            $url = $redirect->getUrl();

            $allowUpgrade = Configuration :: get_instance()->get_setting(array('Chamilo\Core\Repository', 'allow_upgrade'));
            $allowRequest = Configuration :: get_instance()->get_setting(array('Chamilo\Core\Repository', 'allow_request'));

            $translation = ($allowUpgrade || $allowRequest) ? 'MaximumFileSizeUser' : 'MaximumFileSizeUserNoUpgrade';

            $message = Translation :: get(
                $translation,
                array(
                    'SERVER' => Filesystem :: format_file_size($maximumServerSize),
                    'USER' => Filesystem :: format_file_size($maximumSize),
                    'URL' => $url));

            if ($maximumSize < 5242880)
            {
                $form->add_error_message('max_size', null, $message);
            }
            else
            {
                $form->add_warning_message('max_size', null, $message);
            }
        }
        else
        {
            $maximumSize = $maximumServerSize;
            $message = Translation :: get(
                'MaximumFileSizeServer',
                array('FILESIZE' => Filesystem :: format_file_size($maximumSize)));
            $form->add_warning_message('max_size', null, $message);
        }
    }

    /**
     *
     * @return integer
     */
    public function getMaximumUploadSize()
    {
        $enableQuota = (boolean) Configuration :: get_instance()->get_setting(array('Chamilo\Core\Repository', 'enable_quota'));

        $postMaxSize = Filesystem :: interpret_file_size(ini_get('post_max_size'));
        $uploadMaxFilesize = Filesystem :: interpret_file_size(ini_get('upload_max_filesize'));

        $maximumServerSize = $postMaxSize < $uploadMaxFilesize ? $uploadMaxFilesize : $postMaxSize;

        if ($enableQuota && $this->getAvailableUserDiskQuota() < $maximumServerSize)
        {
            $maximumSize = $this->getAvailableUserDiskQuota();
            $maximumUploadSize = $maximumSize > $maximumServerSize ? $maximumServerSize : $maximumSize;
        }
        else
        {
            $maximumUploadSize = $maximumServerSize;
        }

        return floor($maximumUploadSize / 1024 / 1024);
    }
}
