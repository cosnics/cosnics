<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\User\Architecture\Enum\ActionEnum;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Service\UserUrlGenerator;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Domain\ChamiloRequest;
use Chamilo\Libraries\Architecture\Interface\ApplicationInterface;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException;
use Chamilo\Libraries\Protocol\Authentication\Service\AuthenticationValidator;
use Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchParameterException;
use Chamilo\Libraries\Protocol\Mail\Architecture\Interface\MailerInterface;
use Chamilo\Libraries\Protocol\Security\Service\HashingAlgorithm;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\UserInterface\Alert\Architecture\Domain\Alert;
use Chamilo\Libraries\UserInterface\Alert\Architecture\Enum\AlertEnum;
use Chamilo\Libraries\UserInterface\Alert\Service\AlertsManager;
use Chamilo\Libraries\UserInterface\Layout\Service\ApplicationHeaderRenderer;
use Chamilo\Libraries\UserInterface\Layout\Service\DefaultFooterRenderer;
use Hackzilla\PasswordGenerator\Generator\PasswordGeneratorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\User\Component
 */
class MultiPasswordResetComponent extends Manager
{
    protected HashingAlgorithm $hashingAlgorithm;

    protected PasswordGeneratorInterface $passwordGenerator;

    public function __construct(
        ChamiloRequest $request, ApplicationHeaderRenderer $applicationHeaderRenderer,
        DefaultFooterRenderer $defaultFooterRenderer, Translator $translator,
        AuthenticationValidator $authenticationValidator, UserUrlGenerator $userUrlGenerator,
        MailerInterface $activeMailer, AlertsManager $alertsManager, UserService $userService,
        UrlGenerator $urlGenerator, HashingAlgorithm $hashingAlgorithm, PasswordGeneratorInterface $passwordGenerator
    )
    {
        parent::__construct(
            $request, $applicationHeaderRenderer, $defaultFooterRenderer, $translator, $authenticationValidator,
            $userUrlGenerator, $activeMailer, $alertsManager, $userService, $urlGenerator
        );

        $this->hashingAlgorithm = $hashingAlgorithm;
        $this->passwordGenerator = $passwordGenerator;
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchParameterException
     */
    public function run(?User $currentUser = null): Response
    {
        $userIdentifiers = (array) $this->getRequest()->getFromRequestOrQuery(self::PARAM_USER_ID, []);
        $translator = $this->getTranslator();

        if (!$currentUser->isPlatformAdministrator()) {
            throw new NotAllowedException();
        }

        if (count($userIdentifiers) > 0) {
            $userService = $this->getUserService();

            $failures = 0;

            foreach ($userIdentifiers as $userIdentifier) {
                $userToReset = $userService->findUserByIdentifier($userIdentifier);

                if (!$userService->createNewPasswordForUser($userToReset)) {
                    $failures ++;
                }
            }

            if ($failures) {
                if (count($userIdentifiers) == 1) {
                    $message = $translator->trans(
                        'UserPasswordNotResetted', [], Manager::CONTEXT
                    );
                }
                else {
                    $message = $translator->trans(
                        'UserPasswordsNotResetted', [], Manager::CONTEXT
                    );
                }
            }
            elseif (count($userIdentifiers) == 1) {
                $message = $translator->trans(
                    'UserPasswordResetted', [], Manager::CONTEXT
                );
            }
            else {
                $message = $translator->trans(
                    'UserPasswordResetted', [], Manager::CONTEXT
                );
            }

            $this->getAlertsManager()->addAlert(
                new Alert(
                    $message, $failures ? AlertEnum::DANGER : AlertEnum::SUCCESS
                )
            );

            return new RedirectResponse($this->getUrlGenerator()->fromParameters([
                ApplicationInterface::PARAM_CONTEXT => Manager::CONTEXT,
                ApplicationInterface::PARAM_ACTION => ActionEnum::BROWSE->value
            ]));
        }
        else {
            throw new NoSuchParameterException(self::PARAM_USER_ID);
        }
    }

    public function getHashingUtilities(): HashingAlgorithm
    {
        return $this->hashingAlgorithm;
    }

    public function getPasswordGenerator(): PasswordGeneratorInterface
    {
        return $this->passwordGenerator;
    }
}
