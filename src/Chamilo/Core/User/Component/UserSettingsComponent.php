<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Configuration\Storage\DataClass\Setting;
use Chamilo\Core\Admin\Form\ConfigurationForm;
use Chamilo\Core\User\Manager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTab;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTabsRenderer;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;

/**
 *
 * @package Chamilo\Core\User\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class UserSettingsComponent extends ProfileComponent
{
    const PARAM_CONTEXT = 'context';

    /**
     *
     * @var \Chamilo\Core\User\Form\ConfigurationForm
     */
    private $form;

    /**
     *
     * @var string
     */
    private $context;

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $this->checkAuthorization(Manager::context(), 'ManageAccount');
        $this->context = Request::get(self::PARAM_CONTEXT);

        if (! $this->context)
        {
            $this->context = \Chamilo\Core\Admin\Manager::package();
        }

        if (! $this->getRegistrationConsulter()->isContextRegisteredAndActive($this->context))
        {
            throw new NotAllowedException();
        }

        $this->form = new ConfigurationForm(
            $this->context,
            'config',
            'post',
            $this->get_url(array(self::PARAM_CONTEXT => $this->context)),
            true);

        if ($this->form->validate())
        {
            $success = $this->form->update_user_settings();
            $this->redirect(
                Translation::get($success ? 'ConfigurationUpdated' : 'ConfigurationNotUpdated'),
                ($success ? false : true),
                array(Application::PARAM_ACTION => self::ACTION_USER_SETTINGS, self::PARAM_CONTEXT => $this->context));
        }
        else
        {
            return $this->renderPage();
        }
    }

    /**
     *
     * @return string
     */
    public function getContent()
    {
        $tabs = new DynamicVisualTabsRenderer(
            ClassnameUtilities::getInstance()->getClassNameFromNamespace(__CLASS__, true),
            $this->form->toHtml());

        $setting_contexts = \Chamilo\Configuration\Storage\DataManager::retrieve_setting_contexts(
            new EqualityCondition(
                new PropertyConditionVariable(Setting::class_name(), Setting::PROPERTY_USER_SETTING),
                new StaticConditionVariable(1)));

        foreach ($setting_contexts as $setting_context)
        {
            if ($this->getRegistrationConsulter()->isContextRegisteredAndActive($setting_context))
            {
                $package_url = $this->get_url(
                    array(
                        Application::PARAM_ACTION => self::ACTION_USER_SETTINGS,
                        \Chamilo\Core\Admin\Manager::PARAM_CONTEXT => $setting_context));
                $is_current_tab = ($this->context === $setting_context);
                $tab = new DynamicVisualTab(
                    $setting_context,
                    Translation::get('TypeName', null, $setting_context),
                    Theme::getInstance()->getImagePath($setting_context, 'Logo/22'),
                    $package_url,
                    $is_current_tab);
                $tabs->add_tab($tab);
            }
        }

        $html = array();

        if (! $this->context)
        {
            $html[] = '<div class="normal-message">' . Translation::get('SelectApplicationToConfigure') . '</div><br />';
        }

        $html[] = $tabs->render();

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return \Chamilo\Configuration\Service\RegistrationConsulter
     */
    public function getRegistrationConsulter()
    {
        return $this->getService('chamilo.configuration.service.registration_consulter');
    }
}
