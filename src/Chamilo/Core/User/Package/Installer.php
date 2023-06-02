<?php
namespace Chamilo\Core\User\Package;

use Chamilo\Configuration\Storage\DataManager;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\DependencyInjection\Traits\DependencyInjectionContainerTrait;
use Chamilo\Libraries\Hashing\HashingUtilities;
use Chamilo\Libraries\Translation\Translation;

/**
 * @package user.install
 */

/**
 * This installer can be used to create the storage structure for the users application.
 */
class Installer extends \Chamilo\Configuration\Package\Action\Installer
{
    use DependencyInjectionContainerTrait;

    public const CONTEXT = Manager::CONTEXT;

    /**
     * Constructor
     */
    public function __construct($formValues)
    {
        parent::__construct($formValues);
        $this->initializeContainer();
    }

    public function create_admin_account()
    {
        $values = $this->get_form_values();

        $user = new User();
        $user->set_lastname($values['admin_surname']);
        $user->set_firstname($values['admin_firstname']);
        $user->set_username($values['admin_username']);
        $user->set_password($this->getHashingUtilities()->hashString($values['admin_password']));
        $user->set_auth_source('Platform');
        $user->set_email($values['admin_email']);
        $user->set_status(User::STATUS_TEACHER);
        $user->set_platformadmin('1');
        $user->set_official_code('ADMIN');
        $user->set_phone($values['admin_phone']);
        $user->set_disk_quota('209715200');
        $user->set_database_quota('300');
        $user->set_expiration_date(0);

        if (!$user->create())
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    public function create_anonymous_user()
    {
        $values = $this->get_form_values();

        $user = new User();
        $user->set_lastname(Translation::get('Anonymous'));
        $user->set_firstname(Translation::get('Mr'));
        $user->set_username('anonymous');
        $user->set_password($this->getHashingUtilities()->hashString($values['admin_password']));
        $user->set_auth_source('Platform');
        $user->set_email($values['admin_email']);
        $user->set_status(User::STATUS_STUDENT);
        $user->set_platformadmin('0');
        $user->set_official_code('ANONYMOUS');
        $user->set_phone($values['admin_phone']);
        $user->set_disk_quota('0');
        $user->set_database_quota('0');
        $user->set_expiration_date(0);

        if (!$user->create())
        {
            return false;
        }

        return true;
    }

    public function create_test_user_account()
    {
        $user = new User();
        $user->set_lastname('Doe');
        $user->set_firstname('John');
        $user->set_username('JohnDoe');
        $user->set_password($this->getHashingUtilities()->hashString('JohnDoe'));
        $user->set_auth_source('Platform');
        $user->set_email('john.doe@nowhere.org');
        $user->set_status(User::STATUS_STUDENT);
        $user->set_platformadmin('0');
        $user->set_official_code('TEST_USER');
        $user->set_disk_quota('209715200');
        $user->set_database_quota('300');
        $user->set_expiration_date(0);

        if (!$user->create())
        {
            return false;
        }
        else
        {
            $userService = DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(
                UserService::class
            );

            return $userService->createUserSettingForSettingAndUser(
                'Chamilo\Core\Admin', 'platform_language', $user, 'nl'
            );
        }
    }

    /**
     * Runs the install-script.
     */
    public function extra()
    {
        $values = $this->get_form_values();

        $settings[] = [Manager::CONTEXT, 'allow_registration', $values['self_reg']];

        foreach ($settings as $setting)
        {
            $setting_object = DataManager::retrieve_setting_from_variable_name(
                $setting[1], $setting[0]
            );
            $setting_object->set_value($setting[2]);

            if (!$setting_object->update())
            {
                return false;
            }
        }

        if (!$this->create_anonymous_user())
        {
            return false;
        }
        else
        {
            $this->add_message(self::TYPE_NORMAL, Translation::get('AnonymousAccountCreated'));
        }

        if (!$this->create_admin_account())
        {
            return false;
        }
        else
        {
            $this->add_message(self::TYPE_NORMAL, Translation::get('AdminAccountCreated'));
        }

        if (!$this->create_test_user_account())
        {
            return false;
        }
        else
        {
            $this->add_message(self::TYPE_NORMAL, Translation::get('TestUserAccountCreated'));
        }

        return true;
    }

    /**
     * @return \Chamilo\Libraries\Hashing\HashingUtilities
     */
    public function getHashingUtilities()
    {
        return $this->getService(HashingUtilities::class);
    }
}
