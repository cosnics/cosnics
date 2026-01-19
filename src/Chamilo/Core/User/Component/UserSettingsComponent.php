<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\Admin\UserInterface\Form\ConfigurationForm;
use Chamilo\Core\User\Manager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Structure\Glyph\NamespaceIdentGlyph;
use Chamilo\Libraries\Format\Tabs\Link\LinkTab;
use Chamilo\Libraries\Format\Tabs\TabsCollection;
use Symfony\Component\HttpFoundation\Response;

/**
 * @package Chamilo\Core\User\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class UserSettingsComponent extends ProfileComponent
{
    public const PARAM_SELECTED_CONTEXT = 'context';

    private ConfigurationForm $form;

    private string $selectedContext;

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exceptions\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exceptions\StorageNoResultException
     * @throws \QuickformException
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function run(): Response
    {
        $this->checkAuthorization(Manager::CONTEXT, 'ManageAccount');

        $this->form = new ConfigurationForm(
            $this->getSelectedContext(), 'config', FormValidator::FORM_METHOD_POST,
            $this->getUrlGenerator()->fromParameters(
                [self::PARAM_CONTEXT => Manager::CONTEXT, self::PARAM_SELECTED_CONTEXT => $this->getSelectedContext()]
            ), true
        );

        if ($this->form->validate())
        {
            $success = $this->form->update_user_settings();

            return $this->redirectWithMessage(
                $this->getTranslator()->trans($success ? 'ConfigurationUpdated' : 'ConfigurationNotUpdated'), !$success,
                [
                    self::PARAM_CONTEXT => Manager::CONTEXT,
                    self::PARAM_ACTION => self::ACTION_USER_SETTINGS,
                    self::PARAM_SELECTED_CONTEXT => $this->getSelectedContext()
                ]
            );
        }
        else
        {
            return new Response($this->renderPage());
        }
    }

    /**
     * @throws \QuickformException
     */
    public function getContent(): string
    {
        $translator = $this->getTranslator();
        $tabs = new TabsCollection();

        $settingContexts = $this->getUserSettingService()->findUserSettingContexts();

        foreach ($settingContexts as $settingContext)
        {

            $package_url = $this->getUrlGenerator()->fromParameters(
                [
                    self::PARAM_CONTEXT => Manager::CONTEXT,
                    Application::PARAM_ACTION => self::ACTION_USER_SETTINGS,
                    self::PARAM_SELECTED_CONTEXT => $settingContext
                ]
            );

            $is_current_tab = ($this->getSelectedContext() === $settingContext);

            $tab = new LinkTab(
                $settingContext, $translator->trans('TypeName', [], $settingContext), new NamespaceIdentGlyph(
                $settingContext, true
            ), $package_url, $is_current_tab
            );

            $tabs->add($tab);
        }

        $html = [];

        if (!$this->getSelectedContext())
        {
            $html[] = '<div class="normal-message">' .
                $translator->trans('SelectApplicationToConfigure', [], Manager::CONTEXT) . '</div><br />';
        }

        $html[] = $this->getLinkTabsRenderer()->render($tabs, $this->form->render());

        return implode(PHP_EOL, $html);
    }

    public function getSelectedContext(): ?string
    {
        if (!isset($this->selectedContext))
        {
            $this->selectedContext =
                $this->getRequest()->query->get(self::PARAM_SELECTED_CONTEXT, \Chamilo\Core\Admin\Manager::CONTEXT);
        }

        return $this->selectedContext;
    }
}
