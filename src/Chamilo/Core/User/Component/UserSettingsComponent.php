<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Configuration\Service\RegistrationConsulter;
use Chamilo\Configuration\Storage\DataClass\Setting;
use Chamilo\Configuration\Storage\DataManager;
use Chamilo\Core\Admin\Form\ConfigurationForm;
use Chamilo\Core\User\Manager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Structure\Glyph\IdentGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\NamespaceIdentGlyph;
use Chamilo\Libraries\Format\Tabs\Link\LinkTab;
use Chamilo\Libraries\Format\Tabs\Link\LinkTabsRenderer;
use Chamilo\Libraries\Format\Tabs\TabsCollection;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package Chamilo\Core\User\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class UserSettingsComponent extends ProfileComponent
{
    public const PARAM_CONTEXT = 'context';

    /**
     *
     * @var string
     */
    private $context;

    /**
     *
     * @var \Chamilo\Core\User\Form\ConfigurationForm
     */
    private $form;

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $this->checkAuthorization(Manager::context(), 'ManageAccount');
        $this->context = Request::get(self::PARAM_CONTEXT);

        if (!$this->context)
        {
            $this->context = \Chamilo\Core\Admin\Manager::package();
        }

        if (!$this->getRegistrationConsulter()->isContextRegisteredAndActive($this->context))
        {
            throw new NotAllowedException();
        }

        $this->form = new ConfigurationForm(
            $this->context, 'config', FormValidator::FORM_METHOD_POST,
            $this->get_url(array(self::PARAM_CONTEXT => $this->context)), true
        );

        if ($this->form->validate())
        {
            $success = $this->form->update_user_settings();
            $this->redirect(
                Translation::get($success ? 'ConfigurationUpdated' : 'ConfigurationNotUpdated'), !$success,
                array(Application::PARAM_ACTION => self::ACTION_USER_SETTINGS, self::PARAM_CONTEXT => $this->context)
            );
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
        $tabs = new TabsCollection();

        $setting_contexts = DataManager::retrieve_setting_contexts(
            new EqualityCondition(
                new PropertyConditionVariable(Setting::class, Setting::PROPERTY_USER_SETTING),
                new StaticConditionVariable(1)
            )
        );

        foreach ($setting_contexts as $setting_context)
        {
            if ($this->getRegistrationConsulter()->isContextRegisteredAndActive($setting_context))
            {
                $package_url = $this->get_url(
                    array(
                        Application::PARAM_ACTION => self::ACTION_USER_SETTINGS,
                        \Chamilo\Core\Admin\Manager::PARAM_CONTEXT => $setting_context
                    )
                );
                $is_current_tab = ($this->context === $setting_context);
                $tab = new LinkTab(
                    $setting_context, Translation::get('TypeName', null, $setting_context), new NamespaceIdentGlyph(
                    $setting_context, true, false, false, IdentGlyph::SIZE_SMALL, []
                ), $package_url, $is_current_tab
                );
                $tabs->add($tab);
            }
        }

        $html = [];

        if (!$this->context)
        {
            $html[] =
                '<div class="normal-message">' . Translation::get('SelectApplicationToConfigure') . '</div><br />';
        }

        $html[] = $this->getLinkTabsRenderer()->render($tabs, $this->form->render());

        return implode(PHP_EOL, $html);
    }

    public function getLinkTabsRenderer(): LinkTabsRenderer
    {
        return $this->getService(LinkTabsRenderer::class);
    }

    /**
     *
     * @return \Chamilo\Configuration\Service\RegistrationConsulter
     */
    public function getRegistrationConsulter(): RegistrationConsulter
    {
        return $this->getService(RegistrationConsulter::class);
    }
}
