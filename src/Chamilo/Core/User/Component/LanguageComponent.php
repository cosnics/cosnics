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
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function run(): Response
    {
        $this->checkAuthorization(Manager::CONTEXT, 'ChangeLanguage');

        if ($this->getContainer()->getParameter('cosnics.application.user.rights.changeLanguage')) {
            $choice = $this->getRequest()->query->get(self::PARAM_LANGUAGE);
            $languages = array_keys($this->getLanguages());

            if ($choice && in_array($choice, $languages)) {
                $this->getUserService()->updateUserSetting(
                    $this->getUser(), 'cosnics.libraries.userInterface.translation.language.default', $choice
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
}
