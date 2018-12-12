<?php
namespace Chamilo\Core\Repository\Quota\Service;

use Chamilo\Configuration\Service\ConfigurationConsulter;
use Chamilo\Core\Group\Service\GroupService;
use Chamilo\Core\Repository\Filter\FilterData;
use Chamilo\Core\Repository\Service\ContentObjectService;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\File\ConfigurablePathBuilder;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Form\FormValidator;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Repository\Quota\Service
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class QuotaCalculator
{
    const POLICY_GROUP_HIGHEST = 1;

    const POLICY_GROUP_LOWEST = 2;

    const POLICY_HIGHEST = 3;

    const POLICY_LOWEST = 4;

    const POLICY_USER = 0;

    /**
     * @var \Chamilo\Configuration\Service\ConfigurationConsulter
     */
    private $configurationConsulter;

    /**
     * @var \Symfony\Component\Translation\Translator
     */
    private $translator;

    /**
     * @var \Chamilo\Core\Group\Service\GroupService
     */
    private $groupService;

    /**
     * @var \Chamilo\Core\User\Service\UserService
     */
    private $userService;

    /**
     * @var \Chamilo\Libraries\File\ConfigurablePathBuilder
     */
    private $configurablePathBuilder;

    /**
     * @var integer[]
     */
    private $allowedStorageSpaceForUserCache = array();

    /**
     * @var integer[]
     */
    private $usedStorageSpaceForUserCache = array();

    /**
     * @var integer
     */
    private $usedAggregatedUserStorageSpace;

    /**
     * @var integer
     */
    private $maximumAggregatedUserStorageSpace;

    /**
     * @var \Chamilo\Core\Repository\Service\ContentObjectService
     */
    private $contentObjectService;

    /**
     * @param \Chamilo\Configuration\Service\ConfigurationConsulter $configurationConsulter
     * @param \Symfony\Component\Translation\Translator $translator
     * @param \Chamilo\Core\Group\Service\GroupService $groupService
     * @param \Chamilo\Core\User\Service\UserService $userService
     * @param \Chamilo\Libraries\File\ConfigurablePathBuilder $configurablePathBuilder
     * @param \Chamilo\Core\Repository\Service\ContentObjectService $contentObjectService
     */
    public function __construct(
        ConfigurationConsulter $configurationConsulter, Translator $translator, GroupService $groupService,
        UserService $userService, ConfigurablePathBuilder $configurablePathBuilder,
        ContentObjectService $contentObjectService
    )
    {
        $this->configurationConsulter = $configurationConsulter;
        $this->translator = $translator;
        $this->groupService = $groupService;
        $this->userService = $userService;
        $this->configurablePathBuilder = $configurablePathBuilder;
        $this->contentObjectService = $contentObjectService;
    }

    /**
     * @return \Chamilo\Core\Repository\Service\ContentObjectService
     */
    public function getContentObjectService(): ContentObjectService
    {
        return $this->contentObjectService;
    }

    /**
     * @param \Chamilo\Core\Repository\Service\ContentObjectService $contentObjectService
     */
    public function setContentObjectService(ContentObjectService $contentObjectService): void
    {
        $this->contentObjectService = $contentObjectService;
    }

    /**
     * @param \Chamilo\Libraries\Format\Form\FormValidator $form
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @see Calculator::addUploadWarningToForm()
     * @throws \Exception
     */
    public function addUploadWarningToFormForUser(FormValidator $form, User $user)
    {
        $configurationConsulter = $this->getConfigurationConsulter();
        $translator = $this->getTranslator();

        $postMaxSize = Filesystem::interpret_file_size(ini_get('post_max_size'));
        $uploadMaxFilesize = Filesystem::interpret_file_size(ini_get('upload_max_filesize'));

        $maximumServerSize = $postMaxSize < $uploadMaxFilesize ? $uploadMaxFilesize : $postMaxSize;
        $availableStorageSpaceForUser = $this->getAvailableStorageSpaceForUser($user);

        if ($this->isStorageQuotumEnabled() && $availableStorageSpaceForUser < $maximumServerSize)
        {
            $redirect = new Redirect(
                array(
                    \Chamilo\Libraries\Architecture\Application\Application::PARAM_CONTEXT => \Chamilo\Core\Repository\Manager::context(
                    ), \Chamilo\Core\Repository\Manager::PARAM_ACTION => \Chamilo\Core\Repository\Manager::ACTION_QUOTA,
                    FilterData::FILTER_CATEGORY => null, \Chamilo\Core\Repository\Quota\Manager::PARAM_ACTION => null
                )
            );
            $url = $redirect->getUrl();

            $allowUpgrade = $configurationConsulter->getSetting(array('Chamilo\Core\Repository', 'allow_upgrade'));
            $allowRequest = $configurationConsulter->getSetting(array('Chamilo\Core\Repository', 'allow_request'));

            $translation = ($allowUpgrade || $allowRequest) ? 'MaximumFileSizeUser' : 'MaximumFileSizeUserNoUpgrade';

            $message = $translator->trans(
                $translation, array(
                'SERVER' => Filesystem::format_file_size($maximumServerSize),
                'USER' => Filesystem::format_file_size($availableStorageSpaceForUser), 'URL' => $url
            ), 'Chamilo\Core\Repository\Quota'
            );

            if ($availableStorageSpaceForUser < 5242880)
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
            $message = $translator->trans(
                'MaximumFileSizeServer',
                array('FILESIZE' => Filesystem::format_file_size($maximumSize), 'Chamilo\Core\Repository\Quota')
            );
            $form->add_warning_message('max_size', null, $message);
        }
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param integer $requestedStorageSpace
     *
     * @return boolean
     * @throws \Exception
     * @see Calculator::canUpload()
     */
    public function doesUserHaveRequestedStorageSpace(User $user, int $requestedStorageSpace)
    {
        if (!$this->isStorageQuotumEnabled())
        {
            return true;
        }

        return $this->getAvailableStorageSpaceForUser($user) > $requestedStorageSpace;
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return integer
     * @see Calculator::getUserDiskQuotaPercentage()
     * @throws \Exception
     */
    public function geStorageSpacePercentageForUser(User $user)
    {
        return 100 * $this->getUsedStorageSpaceForUser($user) / $this->getAllowedStorageSpaceForUser($user);
    }

    /**
     * @return integer
     * @see Calculator::getAggregatedUserDiskQuotaPercentage()
     * @throws \Exception
     */
    public function getAggregatedUserStorageSpacePercentage()
    {
        return 100 * $this->getUsedAggregatedUserStorageSpace() / $this->getMaximumAggregatedUserStorageSpace();
    }

    /**
     * @see Calculator::getAllocatedDiskSpacePercentage()
     * @return integer
     * @throws \Exception
     */
    public function getAllocatedStorageSpacePercentage()
    {
        return 100 * $this->getUsedAggregatedUserStorageSpace() / $this->getMaximumAllocatedStorageSpace();
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return integer
     * @throws \Exception
     * @see Calculator::getMaximumUserDiskQuota()
     */
    public function getAllowedStorageSpaceForUser(User $user)
    {
        if (!isset($this->allowedStorageSpaceForUserCache[$user->getId()]))
        {
            $quotumPolicy =
                $this->getConfigurationConsulter()->getSetting(array('Chamilo\Core\Repository', 'quota_policy'));
            $useQuotumFallback = (bool) $this->getConfigurationConsulter()->getSetting(
                array('Chamilo\Core\Repository', 'quota_fallback')
            );
            $isQuotumFallbackUser = (bool) $this->getConfigurationConsulter()->getSetting(
                array('Chamilo\Core\Repository', 'quota_fallback_user')
            );

            switch ($quotumPolicy)
            {
                case self::POLICY_USER :
                    if ($user->get_disk_quota() || !$useQuotumFallback)
                    {
                        $this->allowedStorageSpaceForUserCache[$user->getId()] = $user->get_disk_quota();
                    }
                    else
                    {
                        if ($isQuotumFallbackUser == 0)
                        {
                            $this->allowedStorageSpaceForUserCache[$user->getId()] =
                                $this->getHighestGroupQuotumForUser($user);
                        }
                        else
                        {
                            $this->allowedStorageSpaceForUserCache[$user->getId()] =
                                $this->getLowestGroupQuotumForUser($user);
                        }
                    }
                    break;
                case self::POLICY_GROUP_HIGHEST :
                    $groupQuotum = $this->getHighestGroupQuotumForUser($user);

                    if ($groupQuotum || !$useQuotumFallback)
                    {
                        $this->allowedStorageSpaceForUserCache[$user->getId()] = $groupQuotum;
                    }
                    else
                    {
                        $this->allowedStorageSpaceForUserCache[$user->getId()] = $user->get_disk_quota();
                    }
                    break;
                case self::POLICY_GROUP_LOWEST :
                    $groupQuotum = $this->getLowestGroupQuotumForUser($user);

                    if ($groupQuotum || !$useQuotumFallback)
                    {
                        $this->allowedStorageSpaceForUserCache[$user->getId()] = $groupQuotum;
                    }
                    else
                    {
                        $this->allowedStorageSpaceForUserCache[$user->getId()] = $user->get_disk_quota();
                    }
                    break;
                case self::POLICY_HIGHEST :
                    $groupQuotum = $this->getHighestGroupQuotumForUser($user);

                    if ($groupQuotum > $user->get_disk_quota())
                    {
                        $this->allowedStorageSpaceForUserCache[$user->getId()] = $groupQuotum;
                    }
                    else
                    {
                        $this->allowedStorageSpaceForUserCache[$user->getId()] = $user->get_disk_quota();
                    }
                    break;
                case self::POLICY_LOWEST :
                    $groupQuotum = $this->getLowestGroupQuotumForUser($user);

                    if ($groupQuotum > $user->get_disk_quota() || !$groupQuotum)
                    {
                        $this->allowedStorageSpaceForUserCache[$user->getId()] = $user->get_disk_quota();
                    }
                    else
                    {
                        $this->allowedStorageSpaceForUserCache[$user->getId()] = $groupQuotum;
                    }
                    break;
                default :
                    $this->allowedStorageSpaceForUserCache[$user->getId()] = $user->get_disk_quota();
                    break;
            }
        }

        return $this->allowedStorageSpaceForUserCache[$user->getId()];
    }

    /**
     *
     * @return integer
     * @see Calculator::getAvailableAggregatedUserDiskQuota()
     * @throws \Exception
     */
    public function getAvailableAggregatedUserStorageSpace()
    {
        $availableStorageSpace =
            $this->getMaximumAggregatedUserStorageSpace() - $this->getUsedAggregatedUserStorageSpace();

        return $availableStorageSpace > 0 ? $availableStorageSpace : 0;
    }

    /**
     *
     * @return integer
     * @see Calculator::getAvailableAllocatedDiskSpace()
     * @throws \Exception
     */
    public function getAvailableAllocatedStorageSpace()
    {
        return $this->getMaximumAllocatedStorageSpace() - $this->getUsedAggregatedUserStorageSpace();
    }

    /**
     *
     * @return integer
     * @see Calculator::getAvailableReservedDiskSpace()
     * @throws \Exception
     */
    public function getAvailableReservedStorageSpace()
    {
        $availableStorageSpace =
            $this->getMaximumAllocatedStorageSpace() - $this->getMaximumAggregatedUserStorageSpace();

        return $availableStorageSpace > 0 ? $availableStorageSpace : 0;
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return integer
     * @throws \Exception
     * @see Calculator::getAvailableUserDiskQuota()
     */
    public function getAvailableStorageSpaceForUser(User $user)
    {
        $availableStorageSpace = $this->getAllowedStorageSpaceForUser($user) - $this->getUsedStorageSpaceForUser($user);

        return $availableStorageSpace > 0 ? $availableStorageSpace : 0;
    }

    /**
     * @return \Chamilo\Libraries\File\ConfigurablePathBuilder
     */
    public function getConfigurablePathBuilder(): ConfigurablePathBuilder
    {
        return $this->configurablePathBuilder;
    }

    /**
     * @param \Chamilo\Libraries\File\ConfigurablePathBuilder $configurablePathBuilder
     */
    public function setConfigurablePathBuilder(ConfigurablePathBuilder $configurablePathBuilder): void
    {
        $this->configurablePathBuilder = $configurablePathBuilder;
    }

    /**
     * @return \Chamilo\Configuration\Service\ConfigurationConsulter
     */
    public function getConfigurationConsulter(): ConfigurationConsulter
    {
        return $this->configurationConsulter;
    }

    /**
     * @param \Chamilo\Configuration\Service\ConfigurationConsulter $configurationConsulter
     */
    public function setConfigurationConsulter(ConfigurationConsulter $configurationConsulter): void
    {
        $this->configurationConsulter = $configurationConsulter;
    }

    /**
     * @return \Chamilo\Core\Group\Service\GroupService
     */
    public function getGroupService(): GroupService
    {
        return $this->groupService;
    }

    /**
     * @param \Chamilo\Core\Group\Service\GroupService $groupService
     */
    public function setGroupService(GroupService $groupService): void
    {
        $this->groupService = $groupService;
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return integer
     * @throws \Exception
     */
    public function getHighestGroupQuotumForUser(User $user)
    {
        return $this->getGroupService()->getHighestGroupQuotumForUser($user);
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return integer
     * @throws \Exception
     */
    public function getLowestGroupQuotumForUser(User $user)
    {
        return $this->getGroupService()->getLowestGroupQuotumForUser($user);
    }

    /**
     * @return integer
     * @see Calculator::getMaximumAggregatedUserDiskQuota()
     * @throws \Exception
     */
    public function getMaximumAggregatedUserStorageSpace()
    {
        if (is_null($this->maximumAggregatedUserStorageSpace))
        {
            $users = $this->getUserService()->findUsers();
            $totalQuota = 0;

            foreach ($users as $user)
            {
                $totalQuota += $this->getAllowedStorageSpaceForUser($user);
            }

            $this->maximumAggregatedUserStorageSpace = $totalQuota;
        }

        return $this->maximumAggregatedUserStorageSpace;
    }

    /**
     * @return integer
     * @see Calculator::getMaximumAllocatedStorageSpace()
     */
    public function getMaximumAllocatedStorageSpace()
    {
        return disk_total_space($this->getConfigurablePathBuilder()->getRepositoryPath());
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return float
     * @throws \Exception
     * @see Calculator::getMaximumUploadSize()
     */
    public function getMaximumUploadSizeForUser(User $user)
    {
        $postMaxSize = Filesystem::interpret_file_size(ini_get('post_max_size'));
        $uploadMaxFilesize = Filesystem::interpret_file_size(ini_get('upload_max_filesize'));

        $maximumServerSize = $postMaxSize < $uploadMaxFilesize ? $uploadMaxFilesize : $postMaxSize;
        $availableStorageSpaceForUser = $this->getAvailableStorageSpaceForUser($user);

        if ($this->isStorageQuotumEnabled() && $availableStorageSpaceForUser < $maximumServerSize)
        {
            $maximumUploadSize =
                $availableStorageSpaceForUser > $maximumServerSize ? $maximumServerSize : $availableStorageSpaceForUser;
        }
        else
        {
            $maximumUploadSize = $maximumServerSize;
        }

        return floor($maximumUploadSize / 1024 / 1024);
    }

    /**
     *
     * @return integer
     * @see Calculator::getReservedDiskSpacePercentage()
     * @throws \Exception
     */
    public function getReservedStorageSpacePercentage()
    {
        return 100 * $this->getMaximumAggregatedUserStorageSpace() / $this->getMaximumAllocatedStorageSpace();
    }

    /**
     * @return \Symfony\Component\Translation\Translator
     */
    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    /**
     * @param \Symfony\Component\Translation\Translator $translator
     */
    public function setTranslator(Translator $translator): void
    {
        $this->translator = $translator;
    }

    /**
     * @return integer
     * @see Calculator::getUsedAggregatedUserDiskQuota()
     * @throws \Exception
     */
    public function getUsedAggregatedUserStorageSpace()
    {
        if (!isset($this->usedAggregatedUserStorageSpace))
        {
            $this->usedAggregatedUserStorageSpace = $this->getContentObjectService()->getUsedStorageSpace();
        }

        return $this->usedAggregatedUserStorageSpace;
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return integer
     * @see Calculator::getUsedUserDiskQuota()
     * @throws \Exception
     */
    public function getUsedStorageSpaceForUser(User $user)
    {
        if (!isset($this->usedStorageSpaceForUserCache[$user->getId()]))
        {
            $this->usedStorageSpaceForUserCache[$user->getId()] =
                $this->getContentObjectService()->getUsedStorageSpaceForUser($user);
        }

        return $this->usedStorageSpaceForUserCache[$user->getId()];
    }

    /**
     * @return \Chamilo\Core\User\Service\UserService
     */
    public function getUserService(): UserService
    {
        return $this->userService;
    }

    /**
     * @param \Chamilo\Core\User\Service\UserService $userService
     */
    public function setUserService(UserService $userService): void
    {
        $this->userService = $userService;
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return boolean
     * @throws \Exception
     * @see Calculator::usesUserDiskQuota()
     */
    public function isQuotumDefinedForUser(User $user)
    {
        $quotaPolicy = $this->getConfigurationConsulter()->getSetting(array('Chamilo\Core\Repository', 'quota_policy'));
        $useQuotaFallback =
            (bool) $this->getConfigurationConsulter()->getSetting(array('Chamilo\Core\Repository', 'quota_fallback'));
        $isQuotaFallbackUser = (bool) $this->getConfigurationConsulter()->getSetting(
            array('Chamilo\Core\Repository', 'quota_fallback_user')
        );

        switch ($quotaPolicy)
        {
            case self::POLICY_USER :
                if ($user->get_disk_quota() || !$useQuotaFallback)
                {
                    return true;
                }
                else
                {
                    if ($isQuotaFallbackUser == 0)
                    {
                        $groupQuota = $this->getHighestGroupQuotumForUser($user);

                        return ($groupQuota > $user->get_disk_quota() ? false : true);
                    }
                    else
                    {
                        $groupQuota = $this->getLowestGroupQuotumForUser($user);

                        return $groupQuota || !$useQuotaFallback ? false : true;
                    }
                }
                break;
            case self::POLICY_GROUP_HIGHEST :
                $groupQuota = $this->getHighestGroupQuotumForUser($user);

                return $groupQuota || !$useQuotaFallback ? false : true;
                break;
            case self::POLICY_GROUP_LOWEST :
                $groupQuota = $this->getLowestGroupQuotumForUser($user);

                return $groupQuota || !$useQuotaFallback ? false : true;
                break;
            case self::POLICY_HIGHEST :
                $groupQuota = $this->getHighestGroupQuotumForUser($user);

                return ($groupQuota > $user->get_disk_quota() ? false : true);
                break;
            case self::POLICY_LOWEST :
                $groupQuota = $this->getLowestGroupQuotumForUser($user);

                return ($groupQuota > $user->get_disk_quota() || !$groupQuota ? true : false);
                break;
            default :
                return true;
                break;
        }
    }

    /**
     * @return boolean
     */
    public function isStorageQuotumEnabled()
    {
        return (bool) $this->getConfigurationConsulter()->getSetting(['Chamilo\Core\Repository', 'enable_quota']);
    }
}