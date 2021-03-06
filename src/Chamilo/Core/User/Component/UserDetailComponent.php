<?php

namespace Chamilo\Core\User\Component;

use Chamilo\Configuration\Configuration;
use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Service\UserPropertiesExtension\UserPropertiesExtensionManager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Utilities\Utilities;
use HTML_Table;

/**
 *
 * @package user.lib.user_manager.component
 */
class UserDetailComponent extends Manager
{

    /**
     *
     * @var ButtonToolBarRenderer
     */
    private $buttonToolbarRenderer;

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $this->checkAuthorization(Manager::context(), 'ManageUsers');

        if (!$this->get_user()->is_platform_admin())
        {
            throw new NotAllowedException();
        }

        $id = Request::get(self::PARAM_USER_USER_ID);
        $this->set_parameter(self::PARAM_USER_USER_ID, $id);
        if ($id)
        {
            $user = \Chamilo\Core\User\Storage\DataManager::retrieve_by_id(
                \Chamilo\Core\User\Storage\DataClass\User::class_name(),
                (int) $id
            );

            $this->buttonToolbarRenderer = $this->getButtonToolbarRenderer($user);
            $html = array();

            $html[] = $this->render_header();
            $html[] = $this->buttonToolbarRenderer->render() . '<br />';
            $html[] = $this->display_user_info($user);
            $html[] = '<br />';
            $html[] = $this->display_groups($user);
            $html[] = '<br />';

            $registrations = Configuration::getInstance()->getIntegrationRegistrations(self::package());

            foreach ($registrations as $registration)
            {
                $manager_class = $registration[Registration::PROPERTY_CONTEXT] . '\Manager';

                $info = $manager_class::get_additional_user_information($user);

                if ($info)
                {
                    $html[] = $info;
                    $html[] = '<br />';
                }
            }

            $html[] = $this->display_additional_information($id);
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
        else
        {
            return $this->display_error_page(
                htmlentities(
                    Translation::get(
                        'NoObjectSelected',
                        array('OBJECT' => Translation::get('User')),
                        Utilities::COMMON_LIBRARIES
                    )
                )
            );
        }
    }

    /**
     * Displays the user information
     *
     * @param User $user
     *
     * @return String
     */
    public function display_user_info($user)
    {
        $table = new HTML_Table(array('class' => 'table table-striped table-bordered table-hover table-responsive'));

        $table->setHeaderContents(0, 0, Translation::get('UserInformation'));
        $table->setCellAttributes(0, 0, array('colspan' => 3, 'style' => 'text-align: center;'));

        $profilePhotoUrl = new Redirect(
            array(
                Application::PARAM_CONTEXT => \Chamilo\Core\User\Ajax\Manager::context(),
                Application::PARAM_ACTION => \Chamilo\Core\User\Ajax\Manager::ACTION_USER_PICTURE,
                \Chamilo\Core\User\Manager::PARAM_USER_USER_ID => $user->get_id()
            )
        );

        $table->setCellContents(1, 2, '<img src="' . $profilePhotoUrl->getUrl() . '" />');
        $table->setCellAttributes(1, 2, array('rowspan' => 4, 'style' => 'width: 120px; text-align: center;'));

        $attributes = array(
            'username',
            'firstname',
            'lastname',
            'official_code',
            'email',
            'auth_source',
            'phone',
            'language',
            'active',
            'activation_date',
            'expiration_date',
            'registration_date',
            'disk_quota',
            'database_quota',
            'version_quota'
        );

        foreach ($attributes as $i => $attribute)
        {
            $table->setCellContents(
                ($i + 1),
                0,
                Translation::get((string) StringUtilities::getInstance()->createString($attribute)->upperCamelize())
            );
            $table->setCellAttributes(($i + 1), 0, array('style' => 'width: 150px;'));

            $value = $user->get_default_property($attribute);
            $value = $this->format_property($attribute, $value);

            $table->setCellContents(($i + 1), 1, $value);

            if ($i >= 4)
            {
                $table->setCellAttributes(($i + 1), 1, array('colspan' => 2));
            }
        }

        $extendedProperties = $this->getUserPropertiesExtensionManager()->getExtendedUserProperties($user);
        foreach($extendedProperties as $key => $value)
        {
            $table->setCellContents(($i + 1), 0, $key);
            $table->setCellAttributes(($i + 1), 0, array('style' => 'width: 150px;'));

            $value = $this->format_property($attribute, $value);
            $table->setCellContents(($i + 1), 1, $value);
            $table->setCellAttributes(($i + 1), 1, array('colspan' => 2));
            $i++;
        }

        $table->altRowAttributes(0, array('class' => 'row_odd'), array('class' => 'row_even'), true);

        return $table->toHtml();
    }

    public function format_property($attribute, $value)
    {
        switch ($attribute)
        {
            case 'active' :
                return $value ? Translation::get('ConfirmTrue', null, Utilities::COMMON_LIBRARIES) : Translation::get(
                    'ConfirmFalse',
                    null,
                    Utilities::COMMON_LIBRARIES
                );
            case 'activation_date' :
                return $value == 0 ? Translation::get('Forever') : DatetimeUtilities::format_locale_date(null, $value);
            case 'expiration_date' :
                return $value == 0 ? Translation::get('Forever') : DatetimeUtilities::format_locale_date(null, $value);
            case 'registration_date' :
                return DatetimeUtilities::format_locale_date(null, $value);
            default :
                return $value;
        }
    }

    /**
     * Displays the user groups
     *
     * @param User $user
     *
     * @return String
     */
    public function display_groups($user)
    {
        $table = new HTML_Table(array('class' => 'table table-striped table-bordered table-hover table-responsive'));

        $table->setHeaderContents(0, 0, Translation::get('Groups', null, 'group'));
        $table->setCellAttributes(0, 0, array('colspan' => 2, 'style' => 'text-align: center;'));

        $table->setHeaderContents(1, 0, Translation::get('GroupCode'));
        $table->setCellAttributes(1, 0, array('style' => 'width: 150px;'));
        $table->setHeaderContents(1, 1, Translation::get('GroupName'));

        $groups = $user->get_groups();

        if (!$groups || $groups->size() == 0)
        {
            $table->setCellContents(2, 0, Translation::get('NoGroups'));
            $table->setCellAttributes(2, 0, array('colspan' => 2, 'style' => 'text-align: center;'));
        }
        else
        {
            $i = 2;

            while ($group = $groups->next_result())
            {
                $redirect = new Redirect(
                    array(
                        Application::PARAM_CONTEXT => \Chamilo\Core\Group\Manager::package(),
                        \Chamilo\Core\Group\Manager::PARAM_ACTION => \Chamilo\Core\Group\Manager::ACTION_VIEW_GROUP,
                        \Chamilo\Core\Group\Manager::PARAM_GROUP_ID => $group->get_id()
                    )
                );

                $url = '<a href="' . $redirect->getUrl() . '">';

                $table->setCellContents($i, 0, $url . $group->get_code() . '</a>');
                $table->setCellAttributes($i, 0, array('style' => 'width: 150px;'));
                $table->setCellContents($i, 1, $url . $group->get_name() . '</a>');
                $i ++;
            }
        }

        $table->altRowAttributes(1, array('class' => 'row_odd'), array('class' => 'row_even'), true);

        return $table->toHtml();
    }

    public function getButtonToolbarRenderer($user)
    {
        if (!isset($this->buttonToolbarRenderer))
        {
            $buttonToolbar = new ButtonToolBar();
            $commonActions = new ButtonGroup();
            $toolActions = new ButtonGroup();

            $commonActions->addButton(
                new Button(
                    Translation::get('Edit', null, Utilities::COMMON_LIBRARIES),
                    Theme::getInstance()->getCommonImagePath('Action/Edit'),
                    $this->get_user_editing_url($user),
                    ToolbarItem::DISPLAY_ICON_AND_LABEL
                )
            );

            $commonActions->addButton(
                new Button(
                    Translation::get('Delete', null, Utilities::COMMON_LIBRARIES),
                    Theme::getInstance()->getCommonImagePath('Action/Delete'),
                    $this->get_user_delete_url($user),
                    ToolbarItem::DISPLAY_ICON_AND_LABEL
                )
            );

            $commonActions->addButton(
                new Button(
                    Translation::get('ViewQuota'),
                    Theme::getInstance()->getCommonImagePath('Action/Browser'),
                    $this->get_url(
                        array(self::PARAM_ACTION => self::ACTION_VIEW_QUOTA, 'user_id' => $user->get_id())
                    ),
                    ToolbarItem::DISPLAY_ICON_AND_LABEL
                )
            );

            $toolActions->addButton(
                new Button(
                    Translation::get('LoginAsUser'),
                    Theme::getInstance()->getCommonImagePath('Action/Login'),
                    $this->get_change_user_url($user),
                    ToolbarItem::DISPLAY_ICON_AND_LABEL
                )
            );

            $buttonToolbar->addButtonGroup($commonActions);
            $buttonToolbar->addButtonGroup($toolActions);

            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }

        return $this->buttonToolbarRenderer;
    }

    public function display_additional_information($user_id)
    {
        $form_viewer = new \Chamilo\Configuration\Form\Viewer(
            self::package(),
            'account_fields',
            $user_id,
            Translation::get('AdditionalUserInformation')
        );

        return $form_viewer->render();
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(array(self::PARAM_ACTION => self::ACTION_BROWSE_USERS)),
                Translation::get('AdminUserBrowserComponent')
            )
        );
        $breadcrumbtrail->add_help('user_detail');
    }

    /**
     * @return UserPropertiesExtensionManager
     */
    protected function getUserPropertiesExtensionManager()
    {
        return $this->getService(UserPropertiesExtensionManager::class);
    }
}
