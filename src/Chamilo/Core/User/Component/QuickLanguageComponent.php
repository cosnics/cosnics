<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Configuration\Service\Consulter\LanguageConsulter;
use Chamilo\Core\User\Manager;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * @package user.lib.user_manager.component
 */
class QuickLanguageComponent extends Manager
{
    /**
     * Runs this component and redirect to the original location afterwards.
     * If the allow_user_quick_lang platform
     * setting is set, overwrite the language setting (depending on the request parameters).
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function run()
    {
        $this->checkAuthorization(Manager::CONTEXT, 'ChangeLanguage');

        if ($this->isAllowedToChangeLanguage())
        {
            $choice = $this->getRequest()->query->get(self::PARAM_CHOICE);
            $languages = array_keys($this->getLanguages());

            if ($choice && in_array($choice, $languages))
            {
                $this->getUserSettingService()->saveUserSettingForSettingContextVariableAndUser(
                    'Chamilo\Core\Admin', 'platform_language', $this->getUser(), $choice
                );
            }
        }

        $response = new RedirectResponse($this->getRequest()->query->get(self::PARAM_REFER));
        $response->send();
    }

    private function getLanguageConsulter(): LanguageConsulter
    {
        return $this->getService(LanguageConsulter::class);
    }

    /**
     * @return string[]
     */
    private function getLanguages(): array
    {
        return $this->getLanguageConsulter()->getLanguages();
    }

    private function isAllowedToChangeLanguage(): bool
    {
        return $this->getConfigurationConsulter()->getSetting(
                ['Chamilo\Core\User', 'allow_user_change_platform_language']
            ) == 1 && $this->getConfigurationConsulter()->getSetting(
                ['Chamilo\Core\User', 'allow_user_quick_change_platform_language']
            ) == 1;
    }
}
