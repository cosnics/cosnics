<?php
namespace Chamilo\Application\Calendar\Service;

use Chamilo\Configuration\Service\RegistrationConsulter;
use Chamilo\Configuration\Storage\DataClass\Registration;

/**
 *
 * @package Chamilo\Application\Calendar\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class FullCalendarRendererProvider extends \Chamilo\Libraries\Calendar\Renderer\Service\FullCalendarRendererProvider
{

    /**
     *
     * @var \Chamilo\Configuration\Service\RegistrationConsulter
     */
    private $registrationConsulter;

    /**
     *
     * @param \Chamilo\Configuration\Service\RegistrationConsulter $registrationConsulter
     */
    public function __construct(RegistrationConsulter $registrationConsulter)
    {
        $this->registrationConsulter = $registrationConsulter;
    }

    /**
     *
     * @return \Chamilo\Configuration\Service\RegistrationConsulter
     */
    public function getRegistrationConsulter()
    {
        return $this->registrationConsulter;
    }

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Renderer\Service\FullCalendarRendererProvider::getEventSources()
     */
    public function getEventSources()
    {
        $registrations = $this->getRegistrationConsulter()->getIntegrationRegistrations(
            \Chamilo\Application\Calendar\Manager::package());

        $sources = array();

        foreach ($registrations as $registration)
        {
            if ($registration[Registration::PROPERTY_STATUS])
            {
                $context = $registration[Registration::PROPERTY_CONTEXT];
                $className = $context . '\Service\CalendarEventDataProvider';

                if (class_exists($className))
                {
                    $reflectionClass = new \ReflectionClass($className);
                    if ($reflectionClass->isAbstract())
                    {
                        continue;
                    }

                    $sources[] = $context;
                }
            }
        }

        return $sources;
    }
}