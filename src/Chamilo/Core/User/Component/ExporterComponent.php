<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Core\User\Form\UserExportForm;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\File\Export\Export;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Configuration\LocalSetting;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package user.lib.user_manager.component
 */
class ExporterComponent extends Manager
{
    const EXPORT_ACTION_ADD = 'A';
    const EXPORT_ACTION_UPDATE = 'U';
    const EXPORT_ACTION_DELETE = 'D';
    const EXPORT_ACTION_DEFAULT = self::EXPORT_ACTION_ADD;

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $this->checkAuthorization(Manager::context(), 'ManageUsers');

        if (! $this->get_user()->is_platform_admin())
        {
            throw new NotAllowedException();
        }

        $form = new UserExportForm(UserExportForm::TYPE_EXPORT, $this->get_url());

        if ($form->validate())
        {
            $export = $form->exportValues();
            $file_type = $export['file_type'];
            $result = \Chamilo\Core\User\Storage\DataManager::retrieves(
                \Chamilo\Core\User\Storage\DataClass\User::class_name(),
                new DataClassRetrievesParameters());
            while ($user = $result->next_result())
            {
                if ($file_type == 'pdf')
                {
                    $user_array = $this->prepare_for_pdf_export($user);
                }
                else
                {
                    $user_array = $this->prepare_for_other_export($user);
                }

                Event::trigger(
                    'Export',
                    Manager::context(),
                    array('target_user_id' => $user->get_id(), 'action_user_id' => $this->get_user()->get_id()));
                $data[] = $user_array;
            }
            $this->export_users($file_type, $data);
        }
        else
        {
            $html = array();

            $html[] = $this->render_header();
            $html[] = $form->toHtml();
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
    }

    public function prepare_for_pdf_export($user)
    {
        $lastname_title = Translation::get(
            (string) StringUtilities::getInstance()->createString(User::PROPERTY_LASTNAME)->upperCamelize());
        $firstname_title = Translation::get(
            (string) StringUtilities::getInstance()->createString(User::PROPERTY_FIRSTNAME)->upperCamelize());
        $username_title = Translation::get(
            (string) StringUtilities::getInstance()->createString(User::PROPERTY_USERNAME)->upperCamelize());
        $email_title = Translation::get(
            (string) StringUtilities::getInstance()->createString(User::PROPERTY_EMAIL)->upperCamelize());
        $language_title = Translation::get(
            (string) StringUtilities::getInstance()->createString('language')->upperCamelize());
        $status_title = Translation::get(
            (string) StringUtilities::getInstance()->createString(User::PROPERTY_STATUS)->upperCamelize());
        $active_title = Translation::get(
            (string) StringUtilities::getInstance()->createString(User::PROPERTY_ACTIVE)->upperCamelize());
        $official_code_title = Translation::get(
            (string) StringUtilities::getInstance()->createString(User::PROPERTY_OFFICIAL_CODE)->upperCamelize());
        $phone_title = Translation::get(
            (string) StringUtilities::getInstance()->createString(User::PROPERTY_PHONE)->upperCamelize());
        $activation_date_title = Translation::get(
            (string) StringUtilities::getInstance()->createString(User::PROPERTY_ACTIVATION_DATE)->upperCamelize());
        $expiration_date_title = Translation::get(
            (string) StringUtilities::getInstance()->createString(User::PROPERTY_EXPIRATION_DATE)->upperCamelize());
        $auth_source_title = Translation::get(
            (string) StringUtilities::getInstance()->createString(User::PROPERTY_AUTH_SOURCE)->uperCamelize());

        $user_array[$lastname_title] = $user->get_lastname();
        $user_array[$firstname_title] = $user->get_firstname();
        $user_array[$username_title] = $user->get_username();
        $user_array[$email_title] = $user->get_email();
        $user_array[$language_title] = LocalSetting::getInstance()->get('platform_language');
        $user_array[$status_title] = $user->get_status();
        $user_array[$active_title] = $user->get_active();
        $user_array[$official_code_title] = $user->get_official_code();
        $user_array[$phone_title] = $user->get_phone();

        $act_date = $user->get_activation_date();

        $user_array[$activation_date_title] = $act_date;

        $exp_date = $user->get_expiration_date();

        $user_array[$expiration_date_title] = $exp_date;

        $user_array[$auth_source_title] = $user->get_auth_source();

        return $user_array;
    }

    public function prepare_for_other_export($user, $action = self :: EXPORT_ACTION_DEFAULT)
    {
        // action => needed for import back into chamilo
        $user_array['action'] = $action;

        // $user_array[User::PROPERTY_USER_ID] = $user->get_id();
        $user_array[User::PROPERTY_LASTNAME] = $user->get_lastname();
        $user_array[User::PROPERTY_FIRSTNAME] = $user->get_firstname();
        $user_array[User::PROPERTY_USERNAME] = $user->get_username();
        $user_array[User::PROPERTY_EMAIL] = $user->get_email();
        $user_array['language'] = LocalSetting::getInstance()->get('platform_language');
        $user_array[User::PROPERTY_STATUS] = $user->get_status();
        $user_array[User::PROPERTY_ACTIVE] = $user->get_active();
        $user_array[User::PROPERTY_OFFICIAL_CODE] = $user->get_official_code();
        $user_array[User::PROPERTY_PHONE] = $user->get_phone();

        $act_date = $user->get_activation_date();

        $user_array[User::PROPERTY_ACTIVATION_DATE] = $act_date;

        $exp_date = $user->get_expiration_date();

        $user_array[User::PROPERTY_EXPIRATION_DATE] = $exp_date;

        $user_array[User::PROPERTY_AUTH_SOURCE] = $user->get_auth_source();

        return $user_array;
    }

    public function export_users($file_type, $data)
    {
        $filename = 'export_users_' . date('Y-m-d_H-i-s');
        if ($file_type == 'pdf')
        {
            $data = array(array('key' => 'users', 'data' => $data));
        }
        $export = Export::factory($file_type, $data);
        $export->set_filename($filename);
        $export->send_to_browser();
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('user_exporter');
    }
}
