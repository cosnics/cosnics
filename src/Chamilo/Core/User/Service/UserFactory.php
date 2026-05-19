<?php
namespace Chamilo\Core\User\Service;

use Chamilo\Core\User\Storage\Entity\User;
use Chamilo\Libraries\Protocol\Authentication\Service\AuthenticationValidator;
use Chamilo\Libraries\UserInterface\Theme\Service\ThemePathBuilder;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Uid\Uuid;
use Throwable;

/**
 * @package Chamilo\Core\User\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
readonly class UserFactory
{
    public function __construct(
        protected SessionInterface $session, protected UserService $userService,
        protected ThemePathBuilder $themeWebPathBuilder, protected ThemePathBuilder $themeSystemPathBuilder,
        protected Translator $translator, protected UserSettingsService $userSettingsService,
        protected bool $canChangeLanguage, protected bool $canChangeTimezone
    )
    {
    }

    public function getUser(): ?User
    {
        /**
         * @var string|null $userIdentifier
         */
        $userIdentifier = $this->session->get(AuthenticationValidator::SESSION_USER_ID);

        if ($userIdentifier) {
            try {
                $user = $this->userService->findUserByIdentifier(Uuid::fromString($userIdentifier));

                if ($this->canChangeLanguage) {
                    $userLanguage = $this->userSettingsService->findUserSetting(
                        $user, 'cosnics.libraries.userInterface.translation.language.default'
                    );

                    if ($userLanguage) {
                        $this->translator->setLocale($userLanguage);
                    }
                }

                if ($this->canChangeTimezone) {
                    $userTimezone = $this->userSettingsService->findUserSetting(
                        $user, 'cosnics.libraries.calendar.timezone'
                    );

                    if ($userTimezone) {
                        date_default_timezone_set($userTimezone);
                    }
                }

                return $user;
            }
            catch (Throwable) {
                return null;
            }
        }

        return null;
    }
}

