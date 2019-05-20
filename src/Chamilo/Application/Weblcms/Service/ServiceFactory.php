<?php
namespace Chamilo\Application\Weblcms\Service;

use Chamilo\Application\Weblcms\Service\Interfaces\CourseServiceInterface;
use Chamilo\Application\Weblcms\Service\Interfaces\CourseSettingsServiceInterface;
use Chamilo\Application\Weblcms\Service\Interfaces\PublicationServiceInterface;
use Chamilo\Application\Weblcms\Service\Interfaces\RightsServiceInterface;

/**
 * Service factory for Weblcms services
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ServiceFactory
{
    use \Chamilo\Libraries\Architecture\Traits\DependencyInjectionContainerTrait;

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
        $this->initializeContainer();
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
        return $this->getService('chamilo.application.weblcms.service.course');
    }

    /**
     * Returns the rights service
     *
     * @return RightsServiceInterface
     */
    public function getRightsService()
    {
        return $this->getService('chamilo.application.weblcms.service.rights');
    }

    /**
     * Returns the publication service
     *
     * @return PublicationServiceInterface
     */
    public function getPublicationService()
    {
        return $this->getService('chamilo.application.weblcms.service.publication');
    }

    /**
     * Returns the course settings service
     *
     * @return CourseSettingsServiceInterface
     */
    public function getCourseSettingsService()
    {
        return $this->getService('chamilo.application.weblcms.service.course_settings');
    }

    /**
     *
     * @return \Chamilo\Core\User\Service\UserService
     */
    public function getUserService()
    {
        return $this->getUserService();
    }
}