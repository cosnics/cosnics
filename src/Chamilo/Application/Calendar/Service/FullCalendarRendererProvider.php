<?php
namespace Chamilo\Application\Calendar\Service;

use Chamilo\Configuration\Service\RegistrationConsulter;
use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Libraries\Architecture\ClassnameUtilities;

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
     * @var \Chamilo\Libraries\Architecture\ClassnameUtilities
     */
    private $classnameUtilities;

    /**
     *
     * @param \Chamilo\Configuration\Service\RegistrationConsulter $registrationConsulter
     * @param \Chamilo\Libraries\Architecture\ClassnameUtilities $classnameUtilities
     */
    public function __construct(RegistrationConsulter $registrationConsulter, ClassnameUtilities $classnameUtilities)
    {
        $this->registrationConsulter = $registrationConsulter;
        $this->classnameUtilities = $classnameUtilities;
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
     * @return \Chamilo\Libraries\Architecture\ClassnameUtilities
     */
    public function getClassnameUtilities()
    {
        return $this->classnameUtilities;
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

                    $sources[] = $this->getClassnameUtilities()->getNamespaceParent($context, 4);
                }
            }
        }

        return $sources;
    }
}