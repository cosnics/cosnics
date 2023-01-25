<?php
namespace Chamilo\Core\User\Service;

use Chamilo\Core\User\Picture\UserPictureProviderInterface;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\Translation\Translator;

class UserDetailsRenderer
{
    protected Translator $translator;

    protected UserPictureProviderInterface $userPictureProvider;

    protected UserService $userService;

    /**
     * @param \Chamilo\Core\User\Service\UserService $userService
     * @param \Symfony\Component\Translation\Translator $translator
     * @param \Chamilo\Core\User\Picture\UserPictureProviderInterface $userPictureProvider
     */
    public function __construct(
        UserService $userService, Translator $translator, UserPictureProviderInterface $userPictureProvider
    )
    {
        $this->userService = $userService;
        $this->translator = $translator;
        $this->userPictureProvider = $userPictureProvider;
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    public function getUserPictureProvider(): UserPictureProviderInterface
    {
        return $this->userPictureProvider;
    }

    public function getUserService(): UserService
    {
        return $this->userService;
    }

    public function renderUserDetails(User $user, User $requestingUser): string
    {
        $html = [];

        $html[] = '<div class="panel panel-default">';

        $html[] = '<div class="panel-heading">';
        $html[] = '<h3 class="panel-title">';

        $glyph = new FontAwesomeGlyph('user-circle', [], null, 'fas');
        $html[] = $glyph->render() . '&nbsp;' . $user->get_fullname();

        $html[] = '</h3>';
        $html[] = '</div>';

        $userPicture = $this->getUserPictureProvider()->getUserPictureAsBase64String($user, $requestingUser);

        $html[] = '<div class="panel-body">';

        $html[] = '<img class="img-thumbnail pull-right" src="' . $userPicture . '" alt="' . $user->get_fullname() .
            '" style="max-height: 150px;"/>';

        $translator = $this->getTranslator();

        $html[] = $translator->trans('Email', [], 'Chamilo\Core\User') . ': ' .
            StringUtilities::getInstance()->encryptMailLink($user->get_email());
        $html[] = '<br />' . $translator->trans('Username', [], 'Chamilo\Core\User') . ': ' . $user->get_username();
        $html[] = '<br />' . $translator->trans('Status', [], 'Chamilo\Core\User') . ': ' .
            ($user->get_status() == 1 ? $translator->trans('Teacher', [], 'Chamilo\Core\User') :
                $translator->trans('Student', [], 'Chamilo\Core\User'));

        if ($user->is_platform_admin())
        {
            $html[] = ', ' . $translator->trans('PlatformAdministrator', [], 'Chamilo\Core\User');
        }

        $html[] = '</div>';

        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    public function renderUserDetailsForUserIdentifier(int $userIdentifier, User $requestingUser): string
    {
        return $this->renderUserDetails(
            $this->getUserService()->findUserByIdentifier($userIdentifier), $requestingUser
        );
    }
}
