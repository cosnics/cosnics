<?php

namespace Chamilo\Application\Weblcms\Service;

use Chamilo\Application\Weblcms\CourseSettingsController;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Service\Interfaces\CourseServiceInterface;
use Chamilo\Application\Weblcms\Service\Interfaces\CourseSettingsServiceInterface;
use Chamilo\Application\Weblcms\Service\Interfaces\PublicationServiceInterface;
use Chamilo\Application\Weblcms\Service\Interfaces\RightsServiceInterface;
use Chamilo\Application\Weblcms\Storage\Repository\CourseRepository;
use Chamilo\Application\Weblcms\Storage\Repository\PublicationRepository;
use Chamilo\Core\User\Storage\Repository\UserRepository;

/**
 * Service factory for Weblcms services
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ServiceFactory
{
    const SERVICE_COURSE = 'course_service';
    const SERVICE_RIGHTS = 'rights_service';
    const SERVICE_PUBLICATION = 'publication_service';
    const SERVICE_COURSE_SETTINGS = 'course_settings_service';

    /**
     * ServiceFactory instance
     *
     * @var ServiceFactory
     */
    protected static $instance;

    /**
     * An array of created services
     *
     * @var mixed[]
     */
    protected $services;

    /**
     * ServiceFactory constructor.
     */
    public function __construct()
    {
        $this->services = array();
    }

    /**
     * Singleton
     *
     * @return ServiceFactory
     */
    public static function getInstance()
    {
        if (!isset(self::$instance))
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Returns the course service
     *
     * @return CourseServiceInterface
     */
    public function getCourseService()
    {
        if (!isset($this->services[self::SERVICE_COURSE]))
        {
            $courseService = new CourseService(
                new CourseRepository(), $this->getCourseSettingsService(), new UserRepository()
            );

            $this->services[self::SERVICE_COURSE] = $courseService;

            $courseService->setRightsService($this->getRightsService());
        }

        return $this->services[self::SERVICE_COURSE];
    }

    /**
     * Returns the rights service
     *
     * @return RightsServiceInterface
     */
    public function getRightsService()
    {
        if (!isset($this->services[self::SERVICE_RIGHTS]))
        {
            $rightsService = new RightsService(WeblcmsRights::getInstance(), $this->getCourseSettingsService());
            $this->services[self::SERVICE_RIGHTS] = $rightsService;

            $rightsService->setCourseService($this->getCourseService());
            $rightsService->setPublicationService($this->getPublicationService());
        }

        return $this->services[self::SERVICE_RIGHTS];
    }

    /**
     * Returns the publication service
     *
     * @return PublicationServiceInterface
     */
    public function getPublicationService()
    {
        if (!isset($this->services[self::SERVICE_PUBLICATION]))
        {
            $publicationService = new PublicationService(new PublicationRepository());
            $this->services[self::SERVICE_PUBLICATION] = $publicationService;

            $publicationService->setCourseService($this->getCourseService());
            $publicationService->setRightsService($this->getRightsService());
        }

        return $this->services[self::SERVICE_PUBLICATION];
    }

    /**
     * Returns the course settings service
     *
     * @return CourseSettingsServiceInterface
     */
    public function getCourseSettingsService()
    {
        if (!isset($this->services[self::SERVICE_COURSE_SETTINGS]))
        {
            $courseSettingsService = new CourseSettingsService(CourseSettingsController::getInstance());
            $this->services[self::SERVICE_COURSE_SETTINGS] = $courseSettingsService;
        }

        return $this->services[self::SERVICE_COURSE_SETTINGS];
    }
}