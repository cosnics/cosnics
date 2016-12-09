<?php
namespace Chamilo\Application\Weblcms\Admin;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Configuration\Configuration;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * Service class to validate if a user is an admin of a course
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CourseAdminValidator implements CourseAdminValidatorInterface
{

    /**
     * The course admin validator extensions
     * 
     * @var CourseAdminValidatorInterface[]
     */
    protected $courseAdminValidatorExtensions;

    /**
     * Singleton
     * 
     * @var CourseAdminValidator
     */
    protected static $instance;

    /*
     * Constructor
     * @param Configuration $configuration
     */
    public function __construct(Configuration $configuration = null)
    {
        if (! $configuration instanceof Configuration)
        {
            $configuration = Configuration::getInstance();
        }
        
        $extensionPackages = $configuration->get_registrations_by_type('Chamilo\Application\Weblcms\Admin\Extension');
        foreach ($extensionPackages as $extensionPackage)
        {
            $extensionPackageContext = $extensionPackage['context'];
            
            $class = $extensionPackageContext . '\\CourseAdminValidator';
            if (! class_exists($class))
            {
                throw new \RuntimeException(
                    sprintf(
                        'The given package %s does not contain a valid CourseAdminValidator class', 
                        $extensionPackageContext));
            }
            
            $this->courseAdminValidatorExtensions[] = new $class();
        }
    }

    /**
     * Singleton
     * 
     * @return CourseAdminValidator
     */
    public static function getInstance()
    {
        if (! self::$instance)
        {
            self::$instance = new self();
        }
        
        return self::$instance;
    }

    /**
     * Validates whether or not a user is an admin of a course
     * 
     * @param User $user
     * @param Course $course
     *
     * @return bool
     */
    public function isUserAdminOfCourse(User $user, Course $course)
    {
        foreach ($this->courseAdminValidatorExtensions as $courseAdminValidator)
        {
            if ($courseAdminValidator->isUserAdminOfCourse($user, $course))
            {
                return true;
            }
        }
        
        return false;
    }
}