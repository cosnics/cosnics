<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Configuration\Form\Storage\DataClass\Instance;
use Chamilo\Configuration\Storage\DataClass\Setting;
use Chamilo\Core\Admin\Form\ConfigurationForm;
use Chamilo\Core\User\Manager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Structure\BreadcrumbGenerator;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\Header;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTab;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTabsRenderer;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * $Id: user_settings.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 *
 * @package user.lib.user_manager.component
 */
class UserSettingsComponent extends Manager
{
    const PARAM_CONTEXT = 'context';

    private $tabs;

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        Header :: get_instance()->set_section('my_account');

        $context = Request :: get(self :: PARAM_CONTEXT);

        if (! $context)
        {
            $context = \Chamilo\Core\Admin\Manager :: context();
        }

        $form = new ConfigurationForm(
            $context,
            'config',
            'post',
            $this->get_url(array(self :: PARAM_CONTEXT => $context)),
            true);

        if ($form->validate())
        {
            $success = $form->update_user_settings();
            $this->redirect(
                Translation :: get($success ? 'ConfigurationUpdated' : 'ConfigurationNotUpdated'),
                ($success ? false : true),
                array(Application :: PARAM_ACTION => self :: ACTION_USER_SETTINGS, self :: PARAM_CONTEXT => $context));
        }
        else
        {
            $tabs = new DynamicVisualTabsRenderer(
                ClassnameUtilities :: getInstance()->getClassNameFromNamespace(__NAMESPACE__, true),
                $form->toHtml());
            $setting_contexts = \Chamilo\Configuration\Storage\DataManager :: retrieve_setting_contexts(
                new EqualityCondition(
                    new PropertyConditionVariable(Setting :: class_name(), Setting :: PROPERTY_USER_SETTING),
                    new StaticConditionVariable(1)));

            foreach ($setting_contexts as $setting_context)
            {
                $package_url = $this->get_url(
                    array( // Application :: PARAM_ACTION => AdminManager ::
                           // ACTION_CONFIGURE_PLATFORM,
                        Application :: PARAM_ACTION => self :: ACTION_USER_SETTINGS,
                        \Chamilo\Core\Admin\Manager :: PARAM_CONTEXT => $setting_context));
                $is_current_tab = ($context === $setting_context);
                $tab = new DynamicVisualTab(
                    $setting_context,
                    Translation :: get('TypeName', null, $setting_context),
                    Theme :: getInstance()->getImagePath($setting_context) . 'Logo/22.png',
                    $package_url,
                    $is_current_tab);
                $tabs->add_tab($tab);
            }

            $html = array();

            $html[] = $this->render_header();

            if (! $context)
            {
                $html[] = '<div class="normal-message">' . Translation :: get('SelectApplicationToConfigure') .
                     '</div><br />';
            }

            $html[] = $tabs->render();
            $html[] = $this->render_footer();

            return implode("\n", $html);
        }
    }

    public function render_header()
    {
        $html = array();

        $html[] = parent :: render_header();

        $actions[] = self :: ACTION_VIEW_ACCOUNT;
        $actions[] = self :: ACTION_USER_SETTINGS;

        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Instance :: class_name(), Instance :: PROPERTY_APPLICATION),
            new StaticConditionVariable(self :: context()));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Instance :: class_name(), Instance :: PROPERTY_NAME),
            new StaticConditionVariable('account_fields'));
        $condition = new AndCondition($conditions);

        $form = \Chamilo\Configuration\Form\Storage\DataManager :: retrieve(
            Instance :: class_name(),
            new DataClassRetrieveParameters($condition));

        if ($form instanceof \Chamilo\Configuration\Form\Storage\DataClass\Instance && count($form->get_elements()) > 0)
        {
            $actions[] = self :: ACTION_ADDITIONAL_ACCOUNT_INFORMATION;
        }

        $this->tabs = new DynamicVisualTabsRenderer('account');

        foreach ($actions as $action)
        {
            $selected = ($action == self :: ACTION_USER_SETTINGS ? true : false);

            $label = htmlentities(
                Translation :: get(
                    (string) StringUtilities :: getInstance()->createString($action)->upperCamelize() . 'Title'));
            $link = $this->get_url(array(self :: PARAM_ACTION => $action));

            $this->tabs->add_tab(
                new DynamicVisualTab(
                    $action,
                    $label,
                    Theme :: getInstance()->getImagePath('Chamilo\Core\User\\') . 'place_' . $action . '.png',
                    $link,
                    $selected));
        }

        $html[] = $this->tabs->header();
        $html[] = $this->tabs->body_header();

        return implode("\n", $html);
    }

    public function render_footer()
    {
        $html = array();

        $html[] = '<script type="text/javascript">';
        $html[] = '$(document).ready(function() {';
        $html[] = '$(\':checkbox\').iphoneStyle({ checkedLabel: \'' .
             Translation :: get('ConfirmOn', null, Utilities :: COMMON_LIBRARIES) . '\', uncheckedLabel: \'' .
             Translation :: get('ConfirmOff', null, Utilities :: COMMON_LIBRARIES) . '\'});';
        $html[] = '});';
        $html[] = '</script>';
        $html[] = $this->tabs->body_footer();
        $html[] = $this->tabs->footer();

        $html[] = parent :: render_footer();

        return implode("\n", $html);
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('user_settings');
    }

    /**
     * Returns the admin breadcrumb generator
     *
     * @return \libraries\format\BreadcrumbGeneratorInterface
     */
    public function get_breadcrumb_generator()
    {
        return new BreadcrumbGenerator($this, BreadcrumbTrail :: get_instance());
    }
}
