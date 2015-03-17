<?php
namespace Chamilo\Core\User;

use Chamilo\Core\User\Component\UserApproverComponent;
use Chamilo\Core\User\Storage\DataManager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\Page;

/**
 * $Id: user_manager.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
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
    const APPLICATION_NAME = 'user';

    // Parameters
    const PARAM_USER_USER_ID = 'user_id';
    const PARAM_ACTIVE = 'active';
    const PARAM_CHOICE = 'choice';
    const PARAM_FIRSTLETTER = 'firstletter';
    const PARAM_REFER = 'refer';

    // Actions
    const ACTION_CREATE_USER = 'creator';
    const ACTION_BROWSE_USERS = 'admin_user_browser';
    const ACTION_EXPORT_USERS = 'exporter';
    const ACTION_IMPORT_USERS = 'importer';
    const ACTION_UPDATE_USER = 'updater';
    const ACTION_DELETE_USER = 'deleter';
    const ACTION_REGISTER_USER = 'register';
    const ACTION_LOGIN = 'login';
    const ACTION_LOGOUT = 'logout';
    const ACTION_VIEW_ACCOUNT = 'account';
    const ACTION_EMAIL = 'emailer';
    const ACTION_RESET_PASSWORD = 'reset_password';
    const ACTION_CHANGE_USER = 'change_user';
    const ACTION_ADMIN_USER = 'admin_user';
    const ACTION_REPORTING = 'reporting';
    const ACTION_VIEW_QUOTA = 'quota_viewer';
    const ACTION_USER_DETAIL = 'user_detail';
    const ACTION_CHANGE_ACTIVATION = 'active_changer';
    const ACTION_ACTIVATE = 'activator';
    const ACTION_DEACTIVATE = 'deactivator';
    const ACTION_RESET_PASSWORD_MULTI = 'multi_password_resetter';
    const ACTION_BUILD_USER_FIELDS = 'user_fields_builder';
    const ACTION_ADDITIONAL_ACCOUNT_INFORMATION = 'additional_account_information';
    const ACTION_USER_SETTINGS = 'user_settings';
    const ACTION_USER_APPROVAL_BROWSER = 'user_approval_browser';
    const ACTION_USER_APPROVER = 'user_approver';
    const ACTION_APPROVE_USER = 'user_accepter';
    const ACTION_DENY_USER = 'user_denier';
    const ACTION_MANAGE_METADATA = 'metadata_manager';
    const ACTION_VIEW_TERMSCONDITIONS = 'terms_conditions_viewer';
    const ACTION_EDIT_TERMSCONDITIONS = 'terms_conditions_editor';
    const ACTION_QUICK_LANG = 'quick_language';

    // Default action
    const DEFAULT_ACTION = self :: ACTION_BROWSE_USERS;

    // Section
    const SECTION_MY_ACCOUNT = 'my_account';

    /**
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Libraries\Architecture\Application\Application $application
     */
    public function __construct(\Symfony\Component\HttpFoundation\Request $request, $user = null, $application = null)
    {
        parent :: __construct($request, $user, $application);

        Page :: getInstance()->setSection('Chamilo\Core\Admin');
    }

    public function retrieve_user_by_username($username)
    {
        return DataManager :: retrieve_user_by_username($username);
    }

    /*
     * @see RepositoryDataManager::content_object_deletion_allowed()
     */
    public function user_deletion_allowed($user)
    {
        return DataManager :: user_deletion_allowed($user);
    }

    /**
     * gets the user editing url
     *
     * @param return the requested url
     */
    public function get_user_editing_url($user)
    {
        return $this->get_url(
            array(self :: PARAM_ACTION => self :: ACTION_UPDATE_USER, self :: PARAM_USER_USER_ID => $user->get_id()));
    }

    public function get_change_user_url($user)
    {
        return $this->get_url(
            array(self :: PARAM_ACTION => self :: ACTION_CHANGE_USER, self :: PARAM_USER_USER_ID => $user->get_id()));
    }

    /**
     * gets the user delete url
     *
     * @param return the requested url
     */
    public function get_user_delete_url($user)
    {
        return $this->get_url(
            array(self :: PARAM_ACTION => self :: ACTION_DELETE_USER, self :: PARAM_USER_USER_ID => $user->get_id()));
    }

    public function get_reporting_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_REPORTING));
    }

    public function get_user_reporting_url($user_id)
    {
        return $this->get_url(
            array(self :: PARAM_ACTION => self :: ACTION_REPORTING, self :: PARAM_USER_USER_ID => $user_id));
    }

    public function get_user_detail_url($user_id)
    {
        return $this->get_url(
            array(self :: PARAM_ACTION => self :: ACTION_USER_DETAIL, self :: PARAM_USER_USER_ID => $user_id));
    }

    public function get_approve_user_url($user)
    {
        return $this->get_url(
            array(
                self :: PARAM_CONTEXT => self :: context(),
                self :: PARAM_ACTION => self :: ACTION_USER_APPROVER,
                self :: PARAM_USER_USER_ID => $user->get_id(),
                self :: PARAM_CHOICE => UserApproverComponent :: CHOICE_APPROVE));
    }

    public function get_deny_user_url($user)
    {
        return $this->get_url(
            array(
                self :: PARAM_CONTEXT => self :: context(),
                self :: PARAM_ACTION => self :: ACTION_USER_APPROVER,
                self :: PARAM_USER_USER_ID => $user->get_id(),
                self :: PARAM_CHOICE => UserApproverComponent :: CHOICE_DENY));
    }

    public function get_email_user_url($user)
    {
        return $this->get_url(
            array(self :: PARAM_ACTION => self :: ACTION_EMAIL, self :: PARAM_USER_USER_ID => $user->get_id()));
    }

    public function get_edit_metadata_url($user)
    {
        return $this->get_url(
            array(self :: PARAM_ACTION => self :: ACTION_MANAGE_METADATA, self :: PARAM_USER_USER_ID => $user->get_id()));
    }

    /**
     * get the text for the terms and conditions
     *
     * @return <string> terms & conditions
     */
    public static function get_terms_and_conditions()
    {
        return implode(PHP_EOL, file(Path :: getInstance()->getBasePath() . 'files/documentation/license.txt'));
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

        $ConditionsFile = Path :: getInstance()->getBasePath() . 'files/documentation/license.txt';
        $fh = fopen($ConditionsFile, 'w') or die("can't open file");
        $stringData = $text;
        $success &= fwrite($fh, $stringData);

        $platform_setting = \Chamilo\Configuration\Storage\DataManager :: retrieve_setting_from_variable_name(
            'date_terms_and_conditions_update',
            self :: context());

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
        $platform_setting = \Chamilo\Configuration\Storage\DataManager :: retrieve_setting_from_variable_name(
            'date_terms_and_conditions_update',
            self :: context());

        return $platform_setting->get_value();
    }

    /**
     * Returns the admin breadcrumb generator
     *
     * @return \libraries\format\BreadcrumbGeneratorInterface
     */
    public function get_breadcrumb_generator()
    {
        return new \Chamilo\Core\Admin\Core\BreadcrumbGenerator($this, BreadcrumbTrail :: get_instance());
    }
}
