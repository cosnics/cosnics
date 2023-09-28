<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\Admin\Form\ConfigurationForm;
use Chamilo\Core\User\Manager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Structure\Glyph\NamespaceIdentGlyph;
use Chamilo\Libraries\Format\Tabs\Link\LinkTab;
use Chamilo\Libraries\Format\Tabs\TabsCollection;

/**
 * @package Chamilo\Core\User\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class UserSettingsComponent extends ProfileComponent
{
    public const PARAM_CONTEXT = 'context';

    private string $context;

    private ConfigurationForm $form;

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \QuickformException
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function run()
    {
        $this->checkAuthorization(Manager::CONTEXT, 'ManageAccount');
        $this->context = $this->getRequest()->query->get(self::PARAM_CONTEXT);

        if (!$this->context)
        {
            $this->context = \Chamilo\Core\Admin\Manager::CONTEXT;
        }

        if (!$this->getRegistrationConsulter()->isContextRegisteredAndActive($this->context))
        {
            throw new NotAllowedException();
        }

        $this->form = new ConfigurationForm(
            $this->context, 'config', FormValidator::FORM_METHOD_POST,
            $this->get_url([self::PARAM_CONTEXT => $this->context]), true
        );

        if ($this->form->validate())
        {
            $success = $this->form->update_user_settings();
            $this->redirectWithMessage(
                $this->getTranslator()->trans($success ? 'ConfigurationUpdated' : 'ConfigurationNotUpdated'), !$success,
                [Application::PARAM_ACTION => self::ACTION_USER_SETTINGS, self::PARAM_CONTEXT => $this->context]
            );
        }
        else
        {
            return $this->renderPage();
        }
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     * @throws \QuickformException
     */
    public function getContent(): string
    {
        $translator = $this->getTranslator();
        $tabs = new TabsCollection();

        $settingContexts = $this->getUserSettingService()->findUserSettingContexts();

        foreach ($settingContexts as $settingContext)
        {
            if ($this->getRegistrationConsulter()->isContextRegisteredAndActive($settingContext))
            {
                $package_url = $this->get_url(
                    [
                        Application::PARAM_ACTION => self::ACTION_USER_SETTINGS,
                        \Chamilo\Core\Admin\Manager::PARAM_CONTEXT => $settingContext
                    ]
                );

                $is_current_tab = ($this->context === $settingContext);

                $tab = new LinkTab(
                    $settingContext, $translator->trans('TypeName', [], $settingContext), new NamespaceIdentGlyph(
                    $settingContext, true
                ), $package_url, $is_current_tab
                );

                $tabs->add($tab);
            }
        }

        $html = [];

        if (!$this->context)
        {
            $html[] = '<div class="normal-message">' .
                $translator->trans('SelectApplicationToConfigure', [], Manager::CONTEXT) . '</div><br />';
        }

        $html[] = $this->getLinkTabsRenderer()->render($tabs, $this->form->render());

        return implode(PHP_EOL, $html);
    }
}
