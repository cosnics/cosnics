<?php
namespace Chamilo\Core\User\Implementation\User;

use Chamilo\Core\User\Architecture\Interface\UserDetailsRendererInterface;
use Chamilo\Core\User\Architecture\Interface\UserPictureProviderInterface;
use Chamilo\Core\User\Architecture\Trait\UserDetailsRendererTrait;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Service\UserSettingsParser;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Service\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\FontAwesomeGlyph;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\InlineGlyph;
use Symfony\Component\Translation\Translator;

class UserSettingsRenderer implements UserDetailsRendererInterface
{
    use UserDetailsRendererTrait;

    protected DatetimeUtilities $datetimeUtilities;

    protected StringUtilities $stringUtilities;

    protected UserPictureProviderInterface $userPictureProvider;

    protected UserSettingsParser $userSettingsParser;

    public function __construct(
        UserService $userService, Translator $translator, UserPictureProviderInterface $userPictureProvider,
        StringUtilities $stringUtilities, DatetimeUtilities $datetimeUtilities, UserSettingsParser $userSettingsParser
    )
    {
        $this->userService = $userService;
        $this->translator = $translator;
        $this->userPictureProvider = $userPictureProvider;
        $this->stringUtilities = $stringUtilities;
        $this->datetimeUtilities = $datetimeUtilities;
        $this->userSettingsParser = $userSettingsParser;
    }

    public function getDatetimeUtilities(): DatetimeUtilities
    {
        return $this->datetimeUtilities;
    }

    public function getGlyph(): InlineGlyph
    {
        return new FontAwesomeGlyph('cog');
    }

    public function getStringUtilities(): StringUtilities
    {
        return $this->stringUtilities;
    }

    public function getUserPictureProvider(): UserPictureProviderInterface
    {
        return $this->userPictureProvider;
    }

    public function getUserSettingsParser(): UserSettingsParser
    {
        return $this->userSettingsParser;
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
        return $this->getTranslator()->trans('UserSettings', [], Manager::CONTEXT);
    }

    public function renderUserDetails(User $user, User $requestingUser): string
    {
        if (!$requestingUser->isPlatformAdministrator()) {
            return '';
        }

        $translator = $this->getTranslator();

        $html = [];

        $configurableSettings = $this->getUserSettingsParser()->determineConfigurableSettings();

        foreach ($configurableSettings as $packageContext => $packageSettings) {
            $html[] = '<h5>' . $translator->trans('TypeName', [], $packageContext) . '</h5>';

            foreach ($packageSettings as $settingCategory => $categorySettings) {
                $html[] = '<div class="table-responsive">';
                $html[] = '<table class="table table-striped table-bordered table-hover">';
                $html[] = '<thead><th colspan="2">' . $translator->trans($settingCategory, [], $packageContext) .
                    '</th></thead>';
                $html[] = '<tbody>';

                foreach ($categorySettings as $setting => $settingConfiguration) {
                    $html[] = '<tr>';
                    $html[] = '<td class="w-25">' . $translator->trans($setting, [], $packageContext) . '</td>';
                    $html[] = '<td>' . $this->getUserService()->findUserSetting($user, $setting, '-') . '</td>';
                    $html[] = '</tr>';
                }

                $html[] = '</tbody>';
                $html[] = '</table>';
                $html[] = '</div>';
            }
        }

        return implode(PHP_EOL, $html);
    }
}
