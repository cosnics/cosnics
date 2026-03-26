<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\User\Architecture\Interface\UserPictureProviderInterface;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Service\UserUrlGenerator;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Domain\ChamiloRequest;
use Chamilo\Libraries\Protocol\Authentication\Service\AuthenticationValidator;
use Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchParameterException;
use Chamilo\Libraries\Protocol\Mail\Architecture\Interface\MailerInterface;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\Storage\Architecture\Exception\NoSuchObjectException;
use Chamilo\Libraries\UserInterface\Alert\Service\AlertsManager;
use Chamilo\Libraries\UserInterface\Layout\Service\ApplicationHeaderRenderer;
use Chamilo\Libraries\UserInterface\Layout\Service\DefaultFooterRenderer;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\User\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class DownloadUserPictureComponent extends Manager
{
    public function __construct(
        ChamiloRequest $request, ApplicationHeaderRenderer $applicationHeaderRenderer,
        DefaultFooterRenderer $defaultFooterRenderer, Translator $translator, UrlGenerator $urlGenerator,
        AuthenticationValidator $authenticationValidator, UserUrlGenerator $userUrlGenerator,
        MailerInterface $activeMailer, AlertsManager $alertsManager, UserService $userService,
        protected readonly UserPictureProviderInterface $userPictureProvider
    )
    {
        parent::__construct(
            $request, $applicationHeaderRenderer, $defaultFooterRenderer, $translator, $urlGenerator,
            $authenticationValidator, $userUrlGenerator, $activeMailer, $alertsManager, $userService
        );
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchParameterException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\NoSuchObjectException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function run(?User $currentUser = null): Response
    {
        return $this->userPictureProvider->downloadUserPicture($this->getUserFromRequest());
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\NoSuchObjectException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchParameterException
     */
    protected function getUserFromRequest(): User
    {
        $translator = $this->getTranslator();
        $userIdentifier = $this->getRequest()->query->get(Manager::PARAM_USER_ID);

        if (empty($userIdentifier)) {
            throw new NoSuchParameterException(Manager::PARAM_USER_ID);
        }

        $user = $this->userService->findUserByIdentifier($userIdentifier);

        if (empty($user)) {
            throw new NoSuchObjectException(
                $translator->trans('User', [], Manager::CONTEXT), $userIdentifier
            );
        }

        return $user;
    }
}
