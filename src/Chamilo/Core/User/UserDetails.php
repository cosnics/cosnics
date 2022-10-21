<?php
namespace Chamilo\Core\User;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Traits\DependencyInjectionContainerTrait;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

class UserDetails
{
    use DependencyInjectionContainerTrait;

    /**
     * The user
     */
    private $user;

    /**
     * Indicates if a border should be included
     */
    private $border;

    /**
     * Constructor
     *
     * @param $user User
     * @param $border bool Indicates if a border should be included
     */
    public function __construct($user, $border = true)
    {
        $this->user = $user;
        $this->border = $border;

        $this->initializeContainer();
    }

    /**
     * Returns a HTML representation of the user details
     *
     * @return string
     * @todo Implement further details
     */
    public function toHtml()
    {
        $html[] = '<div class="panel panel-default">';

        $html[] = '<div class="panel-heading">';
        $html[] = '<h3 class="panel-title">';

        $glyph = new FontAwesomeGlyph('user-circle', [], null, 'fas');
        $html[] = $glyph->render() . '&nbsp;' . $this->user->get_fullname();

        $html[] = '</h3>';
        $html[] = '</div>';

        $userPictureProvider = $this->getService('Chamilo\Core\User\Picture\UserPictureProvider');
        $userPicture = $userPictureProvider->getUserPictureAsBase64String($this->user, $this->user);

        $html[] = '<div class="panel-body">';

        $html[] =
            '<img class="img-thumbnail pull-right" src="' . $userPicture . '" alt="' . $this->user->get_fullname() .
            '" style="max-height: 150px;"/>';

        $html[] = Translation::get('Email') . ': ' .
            StringUtilities::getInstance()->encryptMailLink($this->user->get_email());
        $html[] = '<br />' . Translation::get('Username') . ': ' . $this->user->get_username();
        $html[] = '<br />' . Translation::get('Status') . ': ' .
            ($this->user->get_status() == 1 ? Translation::get('Teacher') : Translation::get('Student'));

        if ($this->user->is_platform_admin())
        {
            $html[] = ', ' . Translation::get('PlatformAdministrator');
        }

        $html[] = '</div>';

        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }
}
