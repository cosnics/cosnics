<?php
namespace Chamilo\Application\Calendar\Form;

use Chamilo\Application\Calendar\Service\AvailabilityService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package Chamilo\Application\Calendar\Form
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class AvailabilityForm extends FormValidator
{

    /**
     *
     * @var \Chamilo\Core\User\Storage\DataClass\User
     */
    private $user;

    /**
     *
     * @var \Chamilo\Application\Calendar\Service\AvailabilityService
     */
    private $availabilityService;

    /**
     *
     * @var string[]
     */
    private $availableCalendars;

    /**
     *
     * @param string $actionUrl
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Application\Calendar\Service\AvailabilityService $availabilityService
     */
    public function __construct($actionUrl, User $user, AvailabilityService $availabilityService)
    {
        parent :: __construct('Availability', 'post', $actionUrl);

        $this->user = $user;
        $this->availabilityService = $availabilityService;

        $this->build();
        $this->setValues();
    }

    /**
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    }

    /**
     *
     * @return \Chamilo\Application\Calendar\Service\AvailabilityService
     */
    public function getAvailabilityService()
    {
        return $this->availabilityService;
    }

    /**
     *
     * @param \Chamilo\Application\Calendar\Service\AvailabilityService $AvailabilityService
     */
    public function setAvailabilityService(AvailabilityService $AvailabilityService)
    {
        $this->AvailabilityService = $AvailabilityService;
    }

    public function getAvailableCalendars()
    {
        if (! isset($this->availableCalendars))
        {
            $this->availableCalendars = $this->getAvailabilityService()->getAvailableCalendars();
        }

        return $this->availableCalendars;
    }

    public function build()
    {
        $this->add_information_message(
            'calendar_availability',
            null,
            Translation :: get('CalendarAvailabilityInformation'),
            true);

        $availableCalendars = $this->getAvailableCalendars();

        foreach ($availableCalendars as $ownedCalendarType => $ownedCalendars)
        {
            if (count($ownedCalendars) > 1)
            {
                $this->addElement('category', Translation :: get('TypeName', null, $ownedCalendarType));

                foreach ($ownedCalendars as $ownedCalendar)
                {
                    $calendarElements = array();

                    $calendarElements[] = $this->createElement(
                        'checkbox',
                        AvailabilityService :: PROPERTY_CALENDAR . '[' . $ownedCalendar->getType() . '][' .
                             $ownedCalendar->getIdentifier() . '][' . AvailabilityService :: PROPERTY_AVAILABLE . ']',
                            $ownedCalendar->getName(),
                            null,
                            null,
                            1,
                            0);

                    // $colourElement = $this->createElement(
                    // 'text',
                    // AvailabilityService :: PROPERTY_CALENDAR . '[' . $ownedCalendar->getType() . '][' .
                    // $ownedCalendar->getIdentifier() . '][' . AvailabilityService :: PROPERTY_COLOUR . ']',
                    // $ownedCalendar->getName() . ' Colour',
                    // array('class' => 'colour-selection'));
                    // $colourElement->setType('color');

                    // $calendarElements[] = $colourElement;
                    $calendarElements[] = $this->createElement('static', null, null, $ownedCalendar->getDescription());

                    $this->addGroup($calendarElements, 'buttons', $ownedCalendar->getName(), '&nbsp;', false);
                }

                $this->addElement('category');
            }
        }

        $this->addElement('category', Translation :: get('VariousCalendars'));

        foreach ($availableCalendars as $ownedCalendarType => $ownedCalendars)
        {
            if (count($ownedCalendars) == 1)
            {
                foreach ($ownedCalendars as $ownedCalendar)
                {
                    $calendarElements = array();

                    $calendarElements[] = $this->createElement(
                        'checkbox',
                        AvailabilityService :: PROPERTY_CALENDAR . '[' . $ownedCalendar->getType() . '][' .
                             $ownedCalendar->getIdentifier() . '][' . AvailabilityService :: PROPERTY_AVAILABLE . ']',
                            $ownedCalendar->getName(),
                            null,
                            null,
                            1,
                            0);

                    $calendarElements[] = $this->createElement('static', null, null, $ownedCalendar->getDescription());

                    $this->addGroup($calendarElements, 'buttons', $ownedCalendar->getName(), '&nbsp;', false);
                }
            }
        }

        $this->addElement('category');

        $this->addSaveResetButtons();
    }

    private function setValues()
    {
        $defaultValues = array();
        $calendarAvailabilities = $this->getAvailabilityService()->getAvailabilitiesForUser($this->getUser());

        while ($calendarAvailability = $calendarAvailabilities->next_result())
        {
            $defaultValues[AvailabilityService :: PROPERTY_CALENDAR][$calendarAvailability->getCalendarType()][$calendarAvailability->getCalendarId()][AvailabilityService :: PROPERTY_AVAILABLE] = $calendarAvailability->getAvailability();
            $defaultValues[AvailabilityService :: PROPERTY_CALENDAR][$calendarAvailability->getCalendarType()][$calendarAvailability->getCalendarId()][AvailabilityService :: PROPERTY_COLOUR] = $calendarAvailability->getColour();
        }

        foreach ($this->getAvailableCalendars() as $ownedCalendarType => $ownedCalendars)
        {
            foreach ($ownedCalendars as $ownedCalendar)
            {
                $calendarType = $ownedCalendar->getType();
                $calendarIdentifier = $ownedCalendar->getIdentifier();

                if (! isset(
                    $defaultValues[AvailabilityService :: PROPERTY_CALENDAR][$calendarType][$calendarIdentifier][AvailabilityService :: PROPERTY_AVAILABLE]))
                {
                    $defaultValues[AvailabilityService :: PROPERTY_CALENDAR][$calendarType][$calendarIdentifier][AvailabilityService :: PROPERTY_AVAILABLE] = 1;
                }
            }
        }

        $this->setDefaults($defaultValues);
    }
}