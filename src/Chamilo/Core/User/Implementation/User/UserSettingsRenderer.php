<?php
namespace Chamilo\Core\User\Implementation\User;

use Chamilo\Core\User\Architecture\Interface\UserDetailsRendererInterface;
use Chamilo\Core\User\Architecture\Interface\UserPictureProviderInterface;
use Chamilo\Core\User\Architecture\Trait\UserDetailsRendererTrait;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Service\UserSettingsParser;
use Chamilo\Core\User\Service\UserSettingsService;
use Chamilo\Core\User\Storage\Entity\User;
use Chamilo\Libraries\Service\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\FontAwesomeGlyph;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\InlineGlyph;
use Symfony\Component\Translation\Translator;

class UserSettingsRenderer implements UserDetailsRendererInterface
{
    use UserDetailsRendererTrait;

    public function __construct(
        protected UserService $userService, protected Translator $translator,
        protected UserPictureProviderInterface $userPictureProvider, protected StringUtilities $stringUtilities,
        protected DatetimeUtilities $datetimeUtilities, protected UserSettingsParser $userSettingsParser,
        protected UserSettingsService $userSettingsService
    )
    {
    }

    public function getGlyph(): InlineGlyph
    {
        return new FontAwesomeGlyph('cog');
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
        return $this->translator->trans('UserSettings', [], Manager::CONTEXT);
    }

    public function renderUserDetails(User $user, User $requestingUser): string
    {
        if (!$requestingUser->isPlatformAdministrator()) {
            return '';
        }

        $html = [];

        $configurableSettings = $this->userSettingsParser->determineConfigurableSettings();

        foreach ($configurableSettings as $packageContext => $packageSettings) {
            $html[] = '<h5>' . $this->translator->trans('TypeName', [], $packageContext) . '</h5>';

            foreach ($packageSettings as $settingCategory => $categorySettings) {
                $html[] = '<div class="table-responsive">';
                $html[] = '<table class="table table-striped table-bordered table-hover">';
                $html[] = '<thead><th colspan="2">' . $this->translator->trans($settingCategory, [], $packageContext) .
                    '</th></thead>';
                $html[] = '<tbody>';

                foreach ($categorySettings as $setting => $settingConfiguration) {
                    $html[] = '<tr>';
                    $html[] = '<td class="w-25">' . $this->translator->trans($setting, [], $packageContext) . '</td>';
                    $html[] = '<td>' . $this->userSettingsService->findUserSetting($user, $setting, '-') . '</td>';
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
