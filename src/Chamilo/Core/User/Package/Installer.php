<?php
namespace Chamilo\Core\User\Package;

use Chamilo\Configuration\Package\Properties\Dependencies\DependencyVerifier;
use Chamilo\Configuration\Package\Properties\Dependencies\DependencyVerifierRenderer;
use Chamilo\Configuration\Package\Service\PackageBundlesCacheService;
use Chamilo\Configuration\Package\Service\PackageFactory;
use Chamilo\Configuration\Service\ConfigurationService;
use Chamilo\Configuration\Service\RegistrationService;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\SystemPathBuilder;
use Chamilo\Libraries\Hashing\HashingUtilities;
use Chamilo\Libraries\Storage\DataManager\Repository\StorageUnitRepository;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\User\Package
 */
class Installer extends \Chamilo\Configuration\Package\Action\Installer
{
    public const CONTEXT = Manager::CONTEXT;

    protected HashingUtilities $hashingUtilities;

    protected UserService $userService;

    public function __construct(
        ClassnameUtilities $classnameUtilities, ConfigurationService $configurationService,
        StorageUnitRepository $storageUnitRepository, Translator $translator,
        PackageBundlesCacheService $packageBundlesCacheService, PackageFactory $packageFactory,
        RegistrationService $registrationService, SystemPathBuilder $systemPathBuilder,
        DependencyVerifier $dependencyVerifier, DependencyVerifierRenderer $dependencyVerifierRenderer, string $context,
        HashingUtilities $hashingUtilities, UserService $userService
    )
    {
        parent::__construct(
            $classnameUtilities, $configurationService, $storageUnitRepository, $translator,
            $packageBundlesCacheService, $packageFactory, $registrationService, $systemPathBuilder, $dependencyVerifier,
            $dependencyVerifierRenderer, $context
        );

        $this->hashingUtilities = $hashingUtilities;
        $this->userService = $userService;
    }

    public function create_admin_account(array $values): bool
    {
        $user = new User();

        $user->set_lastname($values['admin_surname']);
        $user->set_firstname($values['admin_firstname']);
        $user->set_username($values['admin_username']);
        $user->set_password($this->getHashingUtilities()->hashString($values['admin_password']));
        $user->set_auth_source('Platform');
        $user->set_email($values['admin_email']);
        $user->set_status(User::STATUS_TEACHER);
        $user->set_platformadmin(true);
        $user->set_official_code('ADMIN');
        $user->set_phone($values['admin_phone']);
        $user->set_disk_quota(209715200);
        $user->set_database_quota(300);
        $user->set_expiration_date(0);

        if (!$this->getUserService()->createUser($user))
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    public function create_anonymous_user(array $values): bool
    {
        $translator = $this->getTranslator();
        $user = new User();

        $user->set_lastname($translator->trans('Anonymous', [], Manager::class));
        $user->set_firstname($translator->trans('Mr', [], Manager::class));
        $user->set_username('anonymous');
        $user->set_password($this->getHashingUtilities()->hashString($values['admin_password']));
        $user->set_auth_source('Platform');
        $user->set_email($values['admin_email']);
        $user->set_status(User::STATUS_STUDENT);
        $user->set_platformadmin(false);
        $user->set_official_code('ANONYMOUS');
        $user->set_phone($values['admin_phone']);
        $user->set_disk_quota(0);
        $user->set_database_quota(0);
        $user->set_expiration_date(0);

        if (!$this->getUserService()->createUser($user))
        {
            return false;
        }

        return true;
    }

    public function create_test_user_account(): bool
    {
        $user = new User();

        $user->set_lastname('Doe');
        $user->set_firstname('John');
        $user->set_username('JohnDoe');
        $user->set_password($this->getHashingUtilities()->hashString('JohnDoe'));
        $user->set_auth_source('Platform');
        $user->set_email('john.doe@nowhere.org');
        $user->set_status(User::STATUS_STUDENT);
        $user->set_platformadmin(false);
        $user->set_official_code('TEST_USER');
        $user->set_disk_quota(209715200);
        $user->set_database_quota(300);
        $user->set_expiration_date(0);

        if (!$this->getUserService()->createUser($user))
        {
            return false;
        }

        return true;
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function extra(array $formValues): bool
    {
        $translator = $this->getTranslator();

        $settings[] = [Manager::CONTEXT, 'allow_registration', $formValues['self_reg']];

        foreach ($settings as $setting)
        {
            if (!$this->getConfigurationService()->updateSettingFromParameters($setting[0], $setting[1], $setting[2]))
            {
                return false;
            }
        }

        if (!$this->create_anonymous_user($formValues))
        {
            return false;
        }
        else
        {
            $this->add_message(self::TYPE_NORMAL, $translator->trans('AnonymousAccountCreated'));
        }

        if (!$this->create_admin_account($formValues))
        {
            return false;
        }
        else
        {
            $this->add_message(self::TYPE_NORMAL, $translator->trans('AdminAccountCreated'));
        }

        if (!$this->create_test_user_account())
        {
            return false;
        }
        else
        {
            $this->add_message(self::TYPE_NORMAL, $translator->trans('TestUserAccountCreated'));
        }

        return true;
    }

    public function getHashingUtilities(): HashingUtilities
    {
        return $this->hashingUtilities;
    }

    public function getUserService(): UserService
    {
        return $this->userService;
    }
}
