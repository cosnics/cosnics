<?php
namespace Chamilo\Core\User;

use Chamilo\Core\User\Component\UserApproverComponent;
use Chamilo\Core\User\Service\PasswordSecurity;
use Chamilo\Core\User\Service\UserInviteService;
use Chamilo\Core\User\Storage\DataManager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Authentication\AuthenticationValidator;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;

/**
 *
 * @package user.lib.user_manager
 */

/**
 * A user manager provides some functionalities to the admin to manage his users.
 * For each functionality a component is
 * available.
 */
abstract class Manager extends Application
{
    // Parameters
    const PARAM_USER_USER_ID = 'user_id';
    const PARAM_ACTIVE = 'active';
    const PARAM_CHOICE = 'choice';
    const PARAM_FIRSTLETTER = 'firstletter';
    const PARAM_REFER = 'refer';

    // Actions
    const ACTION_CREATE_USER = 'Creator';
    const ACTION_BROWSE_USERS = 'AdminUserBrowser';
    const ACTION_EXPORT_USERS = 'Exporter';
    const ACTION_IMPORT_USERS = 'Importer';
    const ACTION_UPDATE_USER = 'Updater';
    const ACTION_DELETE_USER = 'Deleter';
    const ACTION_REGISTER_USER = 'Register';
    const ACTION_LOGOUT = 'Logout';
    const ACTION_VIEW_ACCOUNT = 'Account';
    const ACTION_CHANGE_PICTURE = 'Picture';
    const ACTION_EMAIL = 'Emailer';
    const ACTION_RESET_PASSWORD = 'ResetPassword';
    const ACTION_CHANGE_USER = 'ChangeUser';
    const ACTION_ADMIN_USER = 'AdminUser';
    const ACTION_REPORTING = 'Reporting';
    const ACTION_VIEW_QUOTA = 'QuotaViewer';
    const ACTION_USER_DETAIL = 'UserDetail';
    const ACTION_CHANGE_ACTIVATION = 'ActiveChanger';
    const ACTION_ACTIVATE = 'Activator';
    const ACTION_DEACTIVATE = 'Deactivator';
    const ACTION_RESET_PASSWORD_MULTI = 'MultiPasswordResetter';
    const ACTION_BUILD_USER_FIELDS = 'UserFieldsBuilder';
    const ACTION_ADDITIONAL_ACCOUNT_INFORMATION = 'AdditionalAccountInformation';
    const ACTION_USER_SETTINGS = 'UserSettings';
    const ACTION_USER_APPROVAL_BROWSER = 'UserApprovalBrowser';
    const ACTION_USER_APPROVER = 'UserApprover';
    const ACTION_APPROVE_USER = 'UserAccepter';
    const ACTION_DENY_USER = 'UserDenier';
    const ACTION_MANAGE_METADATA = 'MetadataManager';
    const ACTION_QUICK_LANG = 'QuickLanguage';
    const ACTION_ACCESS_ANONYMOUSLY = 'AnonymousAccess';

    const ACTION_INVITE = 'Invite';
    const ACTION_ACCEPT_INVITE = 'AcceptInvite';
    const ACTION_EXTEND_INVITE = 'UserInviteExtender';

    // Default action
    const DEFAULT_ACTION = self::ACTION_BROWSE_USERS;

    public function retrieve_user_by_username($username)
    {
        return DataManager::retrieve_user_by_username($username);
    }

    public function user_deletion_allowed($user)
    {
        return DataManager::user_deletion_allowed($user);
    }

    /**
     * gets the user editing url
     *
     * @param return the requested url
     */
    public function get_user_editing_url($user)
    {
        return $this->get_url(
            array(self::PARAM_ACTION => self::ACTION_UPDATE_USER, self::PARAM_USER_USER_ID => $user->get_id()));
    }

    public function get_change_user_url($user)
    {
        return $this->get_url(
            array(self::PARAM_ACTION => self::ACTION_CHANGE_USER, self::PARAM_USER_USER_ID => $user->get_id()));
    }

    /**
     * gets the user delete url
     *
     * @param return the requested url
     */
    public function get_user_delete_url($user)
    {
        return $this->get_url(
            array(self::PARAM_ACTION => self::ACTION_DELETE_USER, self::PARAM_USER_USER_ID => $user->get_id()));
    }

    public function get_reporting_url()
    {
        return $this->get_url(array(self::PARAM_ACTION => self::ACTION_REPORTING));
    }

    public function get_user_reporting_url($user_id)
    {
        return $this->get_url(
            array(self::PARAM_ACTION => self::ACTION_REPORTING, self::PARAM_USER_USER_ID => $user_id));
    }

    public function get_user_detail_url($user_id)
    {
        return $this->get_url(
            array(self::PARAM_ACTION => self::ACTION_USER_DETAIL, self::PARAM_USER_USER_ID => $user_id));
    }

    public function get_approve_user_url($user)
    {
        return $this->get_url(
            array(
                self::PARAM_CONTEXT => self::context(),
                self::PARAM_ACTION => self::ACTION_USER_APPROVER,
                self::PARAM_USER_USER_ID => $user->get_id(),
                self::PARAM_CHOICE => UserApproverComponent::CHOICE_APPROVE));
    }

    public function get_deny_user_url($user)
    {
        return $this->get_url(
            array(
                self::PARAM_CONTEXT => self::context(),
                self::PARAM_ACTION => self::ACTION_USER_APPROVER,
                self::PARAM_USER_USER_ID => $user->get_id(),
                self::PARAM_CHOICE => UserApproverComponent::CHOICE_DENY));
    }

    public function get_email_user_url($user)
    {
        return $this->get_url(
            array(self::PARAM_ACTION => self::ACTION_EMAIL, self::PARAM_USER_USER_ID => $user->get_id()));
    }

    public function get_edit_metadata_url($user)
    {
        return $this->get_url(
            array(self::PARAM_ACTION => self::ACTION_MANAGE_METADATA, self::PARAM_USER_USER_ID => $user->get_id()));
    }

    /**
     * get the text for the terms and conditions
     *
     * @return <string> terms & conditions
     */
    public static function get_terms_and_conditions()
    {
        return implode(PHP_EOL, file(Path::getInstance()->getBasePath() . 'files/documentation/license.txt'));
    }

    /**
     * Updates the terms and conditions set the text for the terms and conditions
     *
     * @param string $text
     *
     * @return bool
     */
    public static function set_terms_and_conditions($text)
    {
        $success = true;

        $ConditionsFile = Path::getInstance()->getBasePath() . 'files/documentation/license.txt';
        $fh = fopen($ConditionsFile, 'w') or die("can't open file");
        $stringData = $text;
        $success &= fwrite($fh, $stringData);

        $platform_setting = \Chamilo\Configuration\Storage\DataManager::retrieve_setting_from_variable_name(
            'date_terms_and_conditions_update',
            self::context());

        $platform_setting->set_value(time());
        $success &= $platform_setting->update();

        return $success;
    }

    /**
     * Returns the last modification date for the terms and conditions
     *
     * @return mixed
     */
    public static function get_date_terms_and_conditions_last_modified()
    {
        $platform_setting = \Chamilo\Configuration\Storage\DataManager::retrieve_setting_from_variable_name(
            'date_terms_and_conditions_update',
            self::context());

        return $platform_setting->get_value();
    }

    /**
     * Returns the admin breadcrumb generator
     *
     * @return \libraries\format\BreadcrumbGeneratorInterface
     */
    public function get_breadcrumb_generator()
    {
        return new \Chamilo\Core\Admin\Core\BreadcrumbGenerator($this, BreadcrumbTrail::getInstance());
    }

    /**
     * @return AuthenticationValidator
     */
    public function getAuthenticationValidator()
    {
        return $this->getService(AuthenticationValidator::class);
    }

    /**
     * @return PasswordSecurity
     */
    public function getPasswordSecurity()
    {
        return $this->getService(PasswordSecurity::class);
    }


    /**
     * @return bool
     */
    protected function areInvitesAllowed()
    {
         return $this->areInvitesEnabled() && $this->getUser()->is_teacher();
    }

    /**
     * @return bool
     */
    protected function areInvitesEnabled()
    {
        return $this->getConfigurationConsulter()->getSetting(['Chamilo\Core\User', 'allow_invites']) == 1;
    }

    /**
     * @return UserInviteService
     */
    protected function getInviteService()
    {
        return $this->getService(UserInviteService::class);
    }
}
