<?php
namespace Chamilo\Core\User\Implementation\User;

use Chamilo\Core\User\Architecture\Interface\UserDetailsRendererInterface;
use Chamilo\Core\User\Architecture\Interface\UserPictureProviderInterface;
use Chamilo\Core\User\Architecture\Trait\UserDetailsRendererTrait;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Service\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\InlineGlyph;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\NamespaceIdentGlyph;
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

    public function hasContentForUser(User $user, User $requestingUser): bool
    {
        if (!$requestingUser->isPlatformAdministrator()) {
            return false;
        }

        return true;
    }

    public function renderTitle(User $user, User $requestingUser): string
    {
        return $this->getTranslator()->trans('UserDetails', [], Manager::CONTEXT);
    }

    /**
     * @throws \TableException
     */
    public function renderUserDetails(User $user, User $requestingUser): string
    {
        if (!$requestingUser->isPlatformAdministrator()) {
            return '';
        }

        $translator = $this->getTranslator();
        $datetimeUtilities = $this->getDatetimeUtilities();

        $table = new HTML_Table(['class' => 'table table-striped table-bordered table-hover table-responsive']);

        $attributes = [
            User::PROPERTY_PICTURE_URI,
            User::PROPERTY_GIVEN_NAME,
            User::PROPERTY_SURNAME,
            User::PROPERTY_USERNAME,
            User::PROPERTY_EMAIL,
            User::PROPERTY_OFFICIAL_CODE,
            User::PROPERTY_AUTHENTICATION_SOURCE,
            User::PROPERTY_REGISTRATION_DATE,
            User::PROPERTY_PLATFORM_ADMINISTRATOR,
            User::PROPERTY_ACTIVE
        ];

        $userPicture = $this->getUserPictureProvider()->getUserPictureAsBase64String($user, $requestingUser);

        foreach ($attributes as $i => $attribute) {
            $table->setCellContents(
                $i, 0, $translator->trans(
                $this->getStringUtilities()->createString($attribute)->upperCamelize()->toString(), [], Manager::CONTEXT
            ), 'th'
            );

            $value = $user->getDefaultProperty($attribute);

            $value = match ($attribute) {
                User::PROPERTY_ACTIVE, User::PROPERTY_PLATFORM_ADMINISTRATOR => $translator->trans(
                    ($value ? 'ConfirmYes' : 'ConfirmNo'), [], StringUtilities::LIBRARIES
                ),
                User::PROPERTY_PICTURE_URI => '<img class="img-thumbnail" src="' . $userPicture . '" alt="' .
                    $user->getFullName() . '" style="max-height: 150px;"/>',
                User::PROPERTY_REGISTRATION_DATE => $datetimeUtilities->formatLocaleDate($value),
                User::PROPERTY_EMAIL => $this->getStringUtilities()->encryptMailLink($value),
                default => $value,
            };

            $table->setCellContents($i, 1, $value);
        }

        return $table->toHtml();
    }
}
