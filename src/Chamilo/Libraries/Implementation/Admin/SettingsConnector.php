<?php
namespace Chamilo\Libraries\Implementation\Admin;

use Chamilo\Core\Admin\Architecture\Interface\SettingsConnectorInterface;
use Chamilo\Core\Admin\Service\Consulter\LanguageConsulter;
use Chamilo\Libraries\Protocol\Mail\Factory\MailerFactory;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use DateTimeZone;
use stdClass;
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

    public function getCalendarFirstDaysOfWeek(): array
    {
        $dayOptions = ['sunday' => 'Sunday', 'monday' => 'Monday'];
        $days = [];

        foreach ($dayOptions as $value => $label) {
            $day = new stdClass();
            $day->value = $value;
            $day->label = $this->getTranslator()->trans($label, [], StringUtilities::LIBRARIES);
            $day->attributes = [];
            $days[] = $day;
        }

        return $days;
    }

    public function getCalendarViews(): array
    {
        $viewOptions = ['Month', 'Week', 'Day', 'List'];
        $views = [];

        foreach ($viewOptions as $viewOption) {
            $view = new stdClass();
            $view->value = $viewOption;
            $view->label = $this->getTranslator()->trans($viewOption, [], StringUtilities::LIBRARIES);
            $view->attributes = [];
            $views[] = $view;
        }

        return $views;
    }

    public function getCalendarWorkingHours(): array
    {
        $hours = [];

        for ($i = 0; $i < 24; $i ++) {
            $hour = new stdClass();
            $hour->value = $i;
            $hour->label = $i;
            $hour->attributes = [];
            $hours[] = $hour;
        }

        return $hours;
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
        $languages = [];

        foreach ($this->getLanguageConsulter()->getLanguages() as $isoCode => $name) {
            $language = new stdClass();
            $language->value = $isoCode;
            $language->label = $name;
            $language->attributes = [];

            $languages[] = $language;
        }

        return $languages;
    }

    /**
     * @return string[]
     */
    public function getTimeZones(): array
    {
        $timezones = [];
        $timezoneIdentifiers = DateTimeZone::listIdentifiers();

        foreach ($timezoneIdentifiers as $timezoneIdentifier) {
            $timezone = new stdClass();
            $timezone->value = $timezoneIdentifier;
            $timezone->label = $timezoneIdentifier;
            $timezone->attributes = [];

            $timezones[] = $timezone;
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
