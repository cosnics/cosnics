<?php
namespace Chamilo\Core\User;

use Chamilo\Core\Admin\Service\BreadcrumbGenerator;
use Chamilo\Core\User\Component\UserApproverComponent;
use Chamilo\Core\User\Storage\DataManager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Authentication\AuthenticationValidator;
use Chamilo\Libraries\Format\Breadcrumb\BreadcrumbGeneratorInterface;
use Chamilo\Libraries\Mail\Mailer\MailerInterface;

/**
 * @package Chamilo\Core\User
 */
abstract class Manager extends Application
{
    public const ACTION_ACCESS_ANONYMOUSLY = 'AnonymousAccess';
    public const ACTION_ACTIVATE = 'Activator';
    public const ACTION_ADDITIONAL_ACCOUNT_INFORMATION = 'AdditionalAccountInformation';
    public const ACTION_ADMIN_USER = 'AdminUser';
    public const ACTION_APPROVE_USER = 'UserAccepter';
    public const ACTION_BROWSE_USERS = 'AdminUserBrowser';
    public const ACTION_BUILD_USER_FIELDS = 'UserFieldsBuilder';
    public const ACTION_CHANGE_ACTIVATION = 'ActiveChanger';
    public const ACTION_CHANGE_PICTURE = 'Picture';
    public const ACTION_CHANGE_USER = 'ChangeUser';
    public const ACTION_CREATE_USER = 'Creator';
    public const ACTION_DEACTIVATE = 'Deactivator';
    public const ACTION_DELETE_USER = 'Deleter';
    public const ACTION_DENY_USER = 'UserDenier';
    public const ACTION_EMAIL = 'Emailer';
    public const ACTION_EXPORT_USERS = 'Exporter';
    public const ACTION_IMPORT_USERS = 'Importer';
    public const ACTION_LOGOUT = 'Logout';
    public const ACTION_MANAGE_METADATA = 'MetadataManager';
    public const ACTION_QUICK_LANG = 'QuickLanguage';
    public const ACTION_REGISTER_USER = 'Register';
    public const ACTION_REPORTING = 'Reporting';
    public const ACTION_RESET_PASSWORD = 'ResetPassword';
    public const ACTION_RESET_PASSWORD_MULTI = 'MultiPasswordResetter';
    public const ACTION_UPDATE_USER = 'Updater';
    public const ACTION_USER_APPROVAL_BROWSER = 'UserApprovalBrowser';
    public const ACTION_USER_APPROVER = 'UserApprover';
    public const ACTION_USER_DETAIL = 'UserDetail';
    public const ACTION_USER_SETTINGS = 'UserSettings';
    public const ACTION_VIEW_ACCOUNT = 'Account';
    public const ACTION_VIEW_QUOTA = 'QuotaViewer';

    public const CONTEXT = __NAMESPACE__;
    public const DEFAULT_ACTION = self::ACTION_BROWSE_USERS;

    public const PARAM_ACTIVE = 'active';
    public const PARAM_CHOICE = 'choice';
    public const PARAM_FIRSTLETTER = 'firstletter';
    public const PARAM_REFER = 'refer';
    public const PARAM_USER_USER_ID = 'user_id';

    public const SESSION_USER_ID = '_uid';

    protected function getActiveMailer(): MailerInterface
    {
        return $this->getService('Chamilo\Libraries\Mail\Mailer\ActiveMailer');
    }

    /**
     * @return AuthenticationValidator
     */
    public function getAuthenticationValidator()
    {
        return $this->getService(AuthenticationValidator::class);
    }

    public function getBreadcrumbGenerator(): BreadcrumbGeneratorInterface
    {
        return $this->getService(BreadcrumbGenerator::class);
    }

    public function get_approve_user_url($user)
    {
        return $this->get_url(
            [
                self::PARAM_CONTEXT => Manager::CONTEXT,
                self::PARAM_ACTION => self::ACTION_USER_APPROVER,
                self::PARAM_USER_USER_ID => $user->get_id(),
                self::PARAM_CHOICE => UserApproverComponent::CHOICE_APPROVE
            ]
        );
    }

    public function get_change_user_url($user)
    {
        return $this->get_url(
            [self::PARAM_ACTION => self::ACTION_CHANGE_USER, self::PARAM_USER_USER_ID => $user->get_id()]
        );
    }

    /**
     * Returns the last modification date for the terms and conditions
     *
     * @return mixed
     */
    public static function get_date_terms_and_conditions_last_modified()
    {
        $platform_setting = \Chamilo\Configuration\Storage\DataManager::retrieve_setting_from_variable_name(
            'date_terms_and_conditions_update', Manager::CONTEXT
        );

        return $platform_setting->get_value();
    }

    public function get_deny_user_url($user)
    {
        return $this->get_url(
            [
                self::PARAM_CONTEXT => Manager::CONTEXT,
                self::PARAM_ACTION => self::ACTION_USER_APPROVER,
                self::PARAM_USER_USER_ID => $user->get_id(),
                self::PARAM_CHOICE => UserApproverComponent::CHOICE_DENY
            ]
        );
    }

    public function get_edit_metadata_url($user)
    {
        return $this->get_url(
            [self::PARAM_ACTION => self::ACTION_MANAGE_METADATA, self::PARAM_USER_USER_ID => $user->get_id()]
        );
    }

    public function get_email_user_url($user)
    {
        return $this->get_url(
            [self::PARAM_ACTION => self::ACTION_EMAIL, self::PARAM_USER_USER_ID => $user->get_id()]
        );
    }

    public function get_reporting_url()
    {
        return $this->get_url([self::PARAM_ACTION => self::ACTION_REPORTING]);
    }

    /**
     * gets the user delete url
     *
     * @param return the requested url
     */
    public function get_user_delete_url($user)
    {
        return $this->get_url(
            [self::PARAM_ACTION => self::ACTION_DELETE_USER, self::PARAM_USER_USER_ID => $user->get_id()]
        );
    }

    public function get_user_detail_url($user_id)
    {
        return $this->get_url(
            [self::PARAM_ACTION => self::ACTION_USER_DETAIL, self::PARAM_USER_USER_ID => $user_id]
        );
    }

    /**
     * gets the user editing url
     *
     * @param return the requested url
     */
    public function get_user_editing_url($user)
    {
        return $this->get_url(
            [self::PARAM_ACTION => self::ACTION_UPDATE_USER, self::PARAM_USER_USER_ID => $user->get_id()]
        );
    }

    public function get_user_reporting_url($user_id)
    {
        return $this->get_url(
            [self::PARAM_ACTION => self::ACTION_REPORTING, self::PARAM_USER_USER_ID => $user_id]
        );
    }

    public function retrieve_user_by_username($username)
    {
        return DataManager::retrieve_user_by_username($username);
    }

    public function user_deletion_allowed($user)
    {
        return DataManager::user_deletion_allowed($user);
    }
}
