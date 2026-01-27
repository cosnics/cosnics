<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\Admin\Service\Consulter\LanguageConsulter;
use Chamilo\Core\User\Manager;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * @package Chamilo\Core\User\Component
 */
class LanguageComponent extends Manager
{
    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     */
    public function run(): Response
    {
        $this->checkAuthorization(Manager::CONTEXT, 'ChangeLanguage');

        if ($this->isAllowedToChangeLanguage())
        {
            $choice = $this->getRequest()->query->get(self::PARAM_LANGUAGE);
            $languages = array_keys($this->getLanguages());

            if ($choice && in_array($choice, $languages))
            {
                $this->getUserSettingService()->saveUserSettingForSettingContextVariableAndUser(
                    'Chamilo\Core\Admin', 'platform_language', $this->getUser(), $choice
                );
            }
        }

        return new RedirectResponse($this->getRequest()->query->get(self::PARAM_REFER));
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
