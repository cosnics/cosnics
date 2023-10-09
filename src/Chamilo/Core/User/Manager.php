<?php
namespace Chamilo\Core\User;

use Chamilo\Core\Admin\Service\BreadcrumbGenerator;
use Chamilo\Core\User\Service\UserUrlGenerator;
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
    public const ACTION_APPROVE_USER = 'UserApprove';
    public const ACTION_BROWSE_USERS = 'AdminUserBrowser';
    public const ACTION_BUILD_USER_FIELDS = 'UserFieldsBuilder';
    public const ACTION_CHANGE_ACTIVATION = 'ActiveChanger';
    public const ACTION_CHANGE_PICTURE = 'Picture';
    public const ACTION_CHANGE_USER = 'ChangeUser';
    public const ACTION_CREATE_USER = 'Creator';
    public const ACTION_DEACTIVATE = 'Deactivator';
    public const ACTION_DELETE_USER = 'Deleter';
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

    public const CHOICE_APPROVE = 1;
    public const CHOICE_DENY = 0;

    public const CONTEXT = __NAMESPACE__;
    public const DEFAULT_ACTION = self::ACTION_BROWSE_USERS;

    public const PARAM_ACTIVE = 'active';
    public const PARAM_CHOICE = 'choice';
    public const PARAM_EXPORT_TYPE = 'export_type';
    public const PARAM_FIRSTLETTER = 'firstletter';
    public const PARAM_REFER = 'refer';
    public const PARAM_RESET_KEY = 'key';
    public const PARAM_USER_USER_ID = 'user_id';

    public const SESSION_USER_ID = '_uid';

    protected function getActiveMailer(): MailerInterface
    {
        return $this->getService('Chamilo\Libraries\Mail\Mailer\ActiveMailer');
    }

    public function getAuthenticationValidator(): AuthenticationValidator
    {
        return $this->getService(AuthenticationValidator::class);
    }

    public function getBreadcrumbGenerator(): BreadcrumbGeneratorInterface
    {
        return $this->getService(BreadcrumbGenerator::class);
    }

    public function getUserUrlGenerator(): UserUrlGenerator
    {
        return $this->getService(UserUrlGenerator::class);
    }
}
