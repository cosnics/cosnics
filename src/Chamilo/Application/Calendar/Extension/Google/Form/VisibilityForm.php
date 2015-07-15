<?php
namespace Chamilo\Application\Calendar\Extension\Google\Form;

use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Application\Calendar\Extension\Google\Service\VisibilityService;
use Chamilo\Application\Calendar\Extension\Google\Service\GoogleCalendarService;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 *
 * @package Chamilo\Application\Calendar\Extension\Google\Form
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class VisibilityForm extends FormValidator
{

    /**
     *
     * @var \Chamilo\Core\User\Storage\DataClass\User
     */
    private $user;

    /**
     *
     * @var \Chamilo\Application\Calendar\Extension\Google\Service\VisibilityService
     */
    private $visibilityService;

    /**
     *
     * @var \Chamilo\Application\Calendar\Extension\Google\Service\GoogleCalendarService
     */
    private $googleCalendarService;

    /**
     *
     * @param string $actionUrl
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Application\Calendar\Extension\Google\Service\VisibilityService $visibilityService
     * @param \Chamilo\Application\Calendar\Extension\Google\Service\GoogleCalendarService $googleCalendarService
     */
    public function __construct($actionUrl, User $user, VisibilityService $visibilityService,
        GoogleCalendarService $googleCalendarService)
    {
        parent :: __construct('visibility', 'post', $actionUrl);

        $this->user = $user;
        $this->visibilityService = $visibilityService;
        $this->googleCalendarService = $googleCalendarService;

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
     * @return \Chamilo\Application\Calendar\Extension\Google\Service\VisibilityService
     */
    public function getVisibilityService()
    {
        return $this->visibilityService;
    }

    /**
     *
     * @param \Chamilo\Application\Calendar\Extension\Google\Service\VisibilityService $visibilityService
     */
    public function setVisibilityService(VisibilityService $visibilityService)
    {
        $this->visibilityService = $visibilityService;
    }

    /**
     *
     * @return \Chamilo\Application\Calendar\Extension\Google\Service\GoogleCalendarService
     */
    public function getGoogleCalendarService()
    {
        return $this->googleCalendarService;
    }

    /**
     *
     * @param \Chamilo\Application\Calendar\Extension\Google\Service\GoogleCalendarService $googleCalendarService
     */
    public function setGoogleCalendarService(GoogleCalendarService $googleCalendarService)
    {
        $this->googleCalendarService = $googleCalendarService;
    }

    public function build()
    {
        $ownedCalendars = $this->getGoogleCalendarService()->getOwnedCalendars();

        while ($ownedCalendar = $ownedCalendars->next_result())
        {
            $this->addElement(
                'checkbox',
                VisibilityService :: PROPERTY_VISIBLE . '[' . $ownedCalendar->id . ']',
                $ownedCalendar->summary,
                $ownedCalendar->description,
                null,
                1,
                0);
        }

        $this->addSaveResetButtons();
    }

    private function setValues()
    {
        $defaultValues = array();
        $calendarVisibilities = $this->getVisibilityService()->getUserVisibilities($this->getUser());

        while ($calendarVisibility = $calendarVisibilities->next_result())
        {
            $defaultValues[VisibilityService :: PROPERTY_VISIBLE][$calendarVisibility->getCalendarId()] = $calendarVisibility->getVisibility();
        }

        $this->setDefaults($defaultValues);
    }
}