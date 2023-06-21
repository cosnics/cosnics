<?php
namespace Chamilo\Core\User\Service\UserDetails;

use Chamilo\Core\User\Architecture\Interfaces\UserDetailsRendererInterface;
use Chamilo\Core\User\Architecture\Traits\UserDetailsRendererTrait;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Picture\UserPictureProviderInterface;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\InlineGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\NamespaceIdentGlyph;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\StringUtilities;
use HTML_Table;
use Symfony\Component\Translation\Translator;

class UserDetailsRenderer implements UserDetailsRendererInterface
{
    use UserDetailsRendererTrait;

    protected DatetimeUtilities $datetimeUtilities;

    protected StringUtilities $stringUtilities;

    protected UserPictureProviderInterface $userPictureProvider;

    public function __construct(
        UserService $userService, Translator $translator, UserPictureProviderInterface $userPictureProvider,
        StringUtilities $stringUtilities, DatetimeUtilities $datetimeUtilities
    )
    {
        $this->userService = $userService;
        $this->translator = $translator;
        $this->userPictureProvider = $userPictureProvider;
        $this->stringUtilities = $stringUtilities;
        $this->datetimeUtilities = $datetimeUtilities;
    }

    public function getDatetimeUtilities(): DatetimeUtilities
    {
        return $this->datetimeUtilities;
    }

    public function getGlyph(): InlineGlyph
    {
        return new NamespaceIdentGlyph(Manager::CONTEXT, true);
    }

    public function getStringUtilities(): StringUtilities
    {
        return $this->stringUtilities;
    }

    public function getUserPictureProvider(): UserPictureProviderInterface
    {
        return $this->userPictureProvider;
    }

    public function renderTitle(User $user, User $requestingUser): string
    {
        return $this->getTranslator()->trans('TypeName', [], Manager::CONTEXT);
    }

    /**
     * @throws \TableException
     */
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

        $html[] = $translator->trans('Email', [], Manager::CONTEXT) . ': ' .
            StringUtilities::getInstance()->encryptMailLink($user->get_email());
        $html[] = '<br />' . $translator->trans('Username', [], Manager::CONTEXT) . ': ' . $user->get_username();
        $html[] = '<br />' . $translator->trans('Status', [], Manager::CONTEXT) . ': ' .
            ($user->get_status() == 1 ? $translator->trans('Teacher', [], Manager::CONTEXT) :
                $translator->trans('Student', [], Manager::CONTEXT));

        if ($user->isPlatformAdmin())
        {
            $html[] = ', ' . $translator->trans('PlatformAdministrator', [], Manager::CONTEXT);
        }

        $html[] = '</div>';

        $html[] = '</div>';

        if ($requestingUser->isPlatformAdmin())
        {
            $html[] = $this->renderUserProperties($user, $requestingUser);
        }

        return implode(PHP_EOL, $html);
    }

    /**
     * @throws \TableException
     */
    public function renderUserProperties(User $user, User $requestingUser): string
    {
        $translator = $this->getTranslator();
        $datetimeUtilities = $this->getDatetimeUtilities();

        $table = new HTML_Table(['class' => 'table table-striped table-bordered table-hover table-responsive']);

        $attributes = [
            'official_code',
            'auth_source',
            'phone',
            'language',
            'active',
            'activation_date',
            'expiration_date',
            'registration_date',
            'disk_quota',
            'database_quota'
        ];

        foreach ($attributes as $i => $attribute)
        {
            $table->setCellContents(
                $i, 0, $translator->trans(
                $this->getStringUtilities()->createString($attribute)->upperCamelize()->toString(), [], Manager::CONTEXT
            )
            );

            $value = $user->getDefaultProperty($attribute);

            $value = match ($attribute)
            {
                User::PROPERTY_ACTIVE => $translator->trans(($value ? 'ConfirmYes' : 'ConfirmNo'), [],
                    StringUtilities::LIBRARIES),
                User::PROPERTY_ACTIVATION_DATE, User::PROPERTY_EXPIRATION_DATE => $value == 0 ?
                    $translator->trans('Forever', [], StringUtilities::LIBRARIES) :
                    $datetimeUtilities->formatLocaleDate(null, $value),
                User:: PROPERTY_REGISTRATION_DATE => $datetimeUtilities->formatLocaleDate(null, $value),
                default => $value,
            };

            $table->setCellContents($i, 1, $value);
        }

        return $table->toHtml();
    }
}
