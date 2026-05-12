<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\User\Architecture\Enum\ActionEnum;
use Chamilo\Core\User\Architecture\Interface\UserPictureUpdateProviderInterface;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\Entity\User;
use Chamilo\Core\User\UserInterface\Form\AbstractUserFormType;
use Chamilo\Core\User\UserInterface\Form\UserPictureUpdateFormType;
use Chamilo\Libraries\Architecture\Interface\ApplicationInterface;
use Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\UserException;
use Chamilo\Libraries\UserInterface\Alert\Architecture\Domain\Alert;
use Chamilo\Libraries\UserInterface\Alert\Architecture\Enum\AlertEnum;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * @package Chamilo\Core\User\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class UpdateUserPictureComponent extends ProfileComponent
{
    /**
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\UserException
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function run(?User $currentUser = null): Response
    {
        $this->checkAuthorization(Manager::CONTEXT, $currentUser, 'ManageAccount');
        $translator = $this->getTranslator();

        if ($this->userPictureProvider instanceof UserPictureUpdateProviderInterface) {
            $form = $this->formFactory->create(
                UserPictureUpdateFormType::class, [], [
                    'action' => $this->getUrlGenerator()->fromRequest(),
                    'user' => $currentUser,
                    'executingUser' => $currentUser
                ]
            );
            $form->handleRequest($this->getRequest());

            if ($form->isSubmitted() && $form->isValid()) {
                $submittedData = $form->getData();

                $pictureInformation = $submittedData[User::PROPERTY_PICTURE_URI];

                try {
                    $this->userPictureProvider->updateUserPictureFromParameters(
                        $currentUser, $pictureInformation,
                        (bool) $submittedData[AbstractUserFormType::PROPERTY_PICTURE_REMOVE], $currentUser
                    );

                    $message = 'UserProfileUpdated';
                    $messageType = AlertEnum::SUCCESS;
                }
                catch (Throwable) {
                    if ($pictureInformation instanceof UploadedFile && !$pictureInformation->isValid()) {
                        $message = $pictureInformation->getErrorMessage();
                    }
                    else {
                        $message = 'UserProfileNotUpdated';
                    }

                    $messageType = AlertEnum::DANGER;
                }

                $this->alertsManager->addAlert(
                    new Alert(
                        $this->getTranslator()->trans($message, [], Manager::CONTEXT), $messageType
                    )
                );

                return new RedirectResponse($this->getUrlGenerator()->fromParameters([
                    ApplicationInterface::PARAM_CONTEXT => Manager::CONTEXT,
                    ApplicationInterface::PARAM_ACTION => ActionEnum::UPDATE_USER_PICTURE->value
                ]));
            }
        }
        else {
            throw new UserException(
                $translator->trans(
                    'UserPictureProviderDoesNotSuportUpdates', [], Manager::CONTEXT
                )
            );
        }

        $html = [];

        $html[] = $this->renderHeader($currentUser);
        $html[] = $this->twigEnvironment->render('form.html.twig', [
            'form' => $form->createView(),
        ]);
        $html[] = $this->renderFooter();

        return new Response(implode(PHP_EOL, $html));
    }
}
