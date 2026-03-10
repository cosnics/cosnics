<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\User\Architecture\Enum\ActionEnum;
use Chamilo\Core\User\Architecture\Interface\UserPictureUpdateProviderInterface;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\UserInterface\Form\UserCreationForm;
use Chamilo\Core\User\UserInterface\Form\UserForm;
use Chamilo\Libraries\Architecture\Interface\ApplicationInterface;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException;
use Chamilo\Libraries\UserInterface\Alert\Architecture\Domain\Alert;
use Chamilo\Libraries\UserInterface\Alert\Architecture\Enum\AlertEnum;
use Exception;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * @package Chamilo\Core\User\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class CreateComponent extends Manager
{
    /**
     * @throws \QuickformException
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException
     */
    public function run(?User $currentUser = null): Response
    {
        $this->checkAuthorization(Manager::CONTEXT, $currentUser, 'ManageUsers');

        $translator = $this->getTranslator();

        if (!$currentUser->isPlatformAdministrator()) {
            throw new NotAllowedException();
        }

        $form = new UserCreationForm($this->getUrlGenerator()->fromRequest());

        if ($form->validate()) {
            try {
                $formValues = $form->exportValues();

                $createdUser = $this->getUserService()->createUserFromParameters(
                    $formValues[User::PROPERTY_GIVEN_NAME], $formValues[User::PROPERTY_SURNAME],
                    $formValues[User::PROPERTY_USERNAME], $formValues[User::PROPERTY_OFFICIAL_CODE],
                    $formValues[User::PROPERTY_EMAIL], (bool) $formValues[UserForm::PROPERTY_GENERATE_PASSWORD],
                    $formValues[User::PROPERTY_PASSWORD], 'Chamilo\Libraries\Authentication\Platform',
                    (bool) $formValues[User::PROPERTY_PLATFORM_ADMINISTRATOR],
                    (bool) $formValues[User::PROPERTY_ACTIVE], (bool) $formValues[UserForm::PROPERTY_SEND_MAIL]
                );

                $userPictureProvider = $this->getUserPictureProvider();

                if ($userPictureProvider instanceof UserPictureUpdateProviderInterface) {
                    $pictureInformation = $this->getRequest()->files->get(User::PROPERTY_PICTURE_URI);

                    if ($pictureInformation instanceof UploadedFile && $pictureInformation->isValid()) {
                        if (!$userPictureProvider->updateUserPictureFromParameters(
                            $createdUser, $currentUser, $pictureInformation
                        )) {
                            $this->getNotificationMessageManager()->addAlert(
                                new Alert(
                                    $translator->trans('UserPictureNotUpdated', [], Manager::CONTEXT),
                                    AlertEnum::WARNING
                                )
                            );
                        }
                    }
                }

                $this->getNotificationMessageManager()->addAlert(
                    new Alert(
                        $translator->trans('UserCreated', [], Manager::CONTEXT), AlertEnum::SUCCESS
                    )
                );

                return new RedirectResponse(
                    $this->getUrlGenerator()->fromParameters(
                        [
                            ApplicationInterface::PARAM_CONTEXT => Manager::CONTEXT,
                            ApplicationInterface::PARAM_ACTION => ActionEnum::BROWSE->value
                        ]
                    )
                );
            }
            catch (Exception $exception) {
                $this->getNotificationMessageManager()->addAlert(
                    new Alert($exception->getMessage(), AlertEnum::DANGER)
                );
            }
        }

        $html = [];

        $html[] = $this->renderHeader($currentUser);
        $html[] = $form->render();
        $html[] = $this->renderFooter();

        return new Response(implode(PHP_EOL, $html));
    }

    public function getUserPictureProvider(): ?UserPictureUpdateProviderInterface
    {
        $service = $this->getService('Chamilo\Core\User\Service\UserPictureProvider');

        return $service instanceof UserPictureUpdateProviderInterface ? $service : null;
    }
}
