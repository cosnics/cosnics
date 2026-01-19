<?php
namespace Chamilo\Application\Calendar\UserInterface\Form;

use Chamilo\Application\Calendar\Manager;
use Chamilo\Application\Calendar\Service\AvailabilityService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Form\FormValidator;

/**
 * @package Chamilo\Application\Calendar\Form
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class AvailabilityForm extends FormValidator
{

    private AvailabilityService $availabilityService;

    /**
     * @var \Chamilo\Application\Calendar\Storage\DataClass\AvailableCalendar[][]
     */
    private array $availableCalendars;

    private User $user;

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \QuickformException
     */
    public function __construct(string $actionUrl, User $user, AvailabilityService $availabilityService)
    {
        parent::__construct('Availability', self::FORM_METHOD_POST, $actionUrl);

        $this->user = $user;
        $this->availabilityService = $availabilityService;

        $this->build();
        $this->setValues();
    }

    /**
     * @throws \QuickformException
     */
    public function build(): void
    {
        $this->add_information_message(
            'calendar_availability', null,
            $this->getTranslation('CalendarAvailabilityInformation', [], Manager::CONTEXT), true
        );

        $availableCalendars = $this->getAvailableCalendars();

        foreach ($availableCalendars as $ownedCalendarType => $ownedCalendars)
        {
            $calendarElements = [];

            foreach ($ownedCalendars as $ownedCalendar)
            {
                $calendarElements[] = $this->createElement(
                    'checkbox', AvailabilityService::PROPERTY_CALENDAR . '[' . $ownedCalendar->getType() . '][' .
                    $ownedCalendar->getIdentifier() . '][' . AvailabilityService::PROPERTY_AVAILABLE . ']', null,
                    $ownedCalendar->getName(), null, 1, 0
                );

                $calendarElements[] = $this->createElement('static', null, null, $ownedCalendar->getDescription());
            }

            $this->addGroup($calendarElements, 'calendars', $this->getTranslation('TypeName', [], $ownedCalendarType),
                '', false);
        }

        $this->addSaveResetButtons();
    }

    public function getAvailabilityService(): AvailabilityService
    {
        return $this->availabilityService;
    }

    /**
     * @return \Chamilo\Application\Calendar\Storage\DataClass\AvailableCalendar[][]
     */
    public function getAvailableCalendars(): array
    {
        if (!isset($this->availableCalendars))
        {
            $this->availableCalendars = $this->getAvailabilityService()->getAvailableCalendars($this->getUser());
        }

        return $this->availableCalendars;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \QuickformException
     */
    private function setValues()
    {
        $defaultValues = [];
        $calendarAvailabilities = $this->getAvailabilityService()->getAvailabilitiesForUser($this->getUser());

        foreach ($calendarAvailabilities as $calendarAvailability)
        {
            $defaultValues[AvailabilityService::PROPERTY_CALENDAR][$calendarAvailability->getCalendarType(
            )][$calendarAvailability->getCalendarId()][AvailabilityService::PROPERTY_AVAILABLE] =
                $calendarAvailability->getAvailability();
            $defaultValues[AvailabilityService::PROPERTY_CALENDAR][$calendarAvailability->getCalendarType(
            )][$calendarAvailability->getCalendarId()][AvailabilityService::PROPERTY_COLOUR] =
                $calendarAvailability->getColour();
        }

        foreach ($this->getAvailableCalendars() as $ownedCalendars)
        {
            foreach ($ownedCalendars as $ownedCalendar)
            {
                $calendarType = $ownedCalendar->getType();
                $calendarIdentifier = $ownedCalendar->getIdentifier();

                if (!isset(
                    $defaultValues[AvailabilityService::PROPERTY_CALENDAR][$calendarType][$calendarIdentifier][AvailabilityService::PROPERTY_AVAILABLE]
                ))
                {
                    $defaultValues[AvailabilityService::PROPERTY_CALENDAR][$calendarType][$calendarIdentifier][AvailabilityService::PROPERTY_AVAILABLE] =
                        1;
                }
            }
        }

        $this->setDefaults($defaultValues);
    }
}