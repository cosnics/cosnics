<?php
namespace Chamilo\Libraries\Implementation\Admin;

use Chamilo\Core\Admin\Architecture\Interface\SettingsConnectorInterface;
use Chamilo\Core\Admin\Service\Consulter\LanguageConsulter;
use Chamilo\Libraries\Protocol\Mail\Factory\MailerFactory;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use DateTimeZone;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Libraries\Implementation\Admin
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class SettingsConnector implements SettingsConnectorInterface
{
    protected LanguageConsulter $languageConsulter;

    protected MailerFactory $mailerFactory;

    protected Translator $translator;

    /**
     * @var array<bool>
     */
    protected array $userRights;

    public function __construct(
        LanguageConsulter $languageConsulter, MailerFactory $mailerFactory, Translator $translator, array $userRights
    )
    {
        $this->languageConsulter = $languageConsulter;
        $this->mailerFactory = $mailerFactory;
        $this->translator = $translator;
        $this->userRights = $userRights;
    }

    public function getContext(): string
    {
        return StringUtilities::LIBRARIES;
    }

    public function getLanguageConsulter(): LanguageConsulter
    {
        return $this->languageConsulter;
    }

    /**
     * @return string[]
     */
    public function getLanguages(): array
    {
        return $this->getLanguageConsulter()->getLanguages();
    }

    /**
     * @return string[]
     */
    public static function getTimeZones(): array
    {
        $timezones = [];
        $timezoneIdentifiers = DateTimeZone::listIdentifiers();

        foreach ($timezoneIdentifiers as $timezoneIdentifier) {
            $timezones[$timezoneIdentifier] = $timezoneIdentifier;
        }

        return $timezones;
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    public function getUserRight(string $rightName): bool
    {
        return array_key_exists($rightName, $this->userRights) ? $this->userRights[$rightName] : false;
    }

    public function getUserRights(?string $rightName = null): array|bool
    {
        if ($rightName) {
            return $this->userRights[$rightName];
        }

        return $this->userRights;
    }

    /**
     * @return int[]
     */
    public function getWorkingHours(): array
    {
        $start = 0;
        $end = 24;
        $workingHours = [];

        for ($i = $start; $i <= $end; $i ++) {
            $workingHours[$i] = $i;
        }

        return $workingHours;
    }

    public function isAllowedToChangePlatformLanguage(): bool
    {
        return $this->getUserRight('changeLanguage');
    }

    public function isAllowedToChangePlatformTimezone(): bool
    {
        return $this->getUserRight('changeTimezone');
    }
}
