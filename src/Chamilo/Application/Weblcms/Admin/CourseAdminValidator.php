<?php
namespace Chamilo\Application\Weblcms\Admin;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Configuration\Service\Consulter\RegistrationConsulter;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use RuntimeException;

/**
 * Service class to validate if a user is an admin of a course
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CourseAdminValidator implements CourseAdminValidatorInterface
{

    protected static ?CourseAdminValidator $instance = null;

    /**
     * The course admin validator extensions
     *
     * @var \Chamilo\Application\Weblcms\Admin\CourseAdminValidatorInterface[]
     */
    protected array $courseAdminValidatorExtensions;

    protected RegistrationConsulter $registrationConsulter;

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function __construct(RegistrationConsulter $registrationConsulter)
    {
        $extensionPackages =
            $registrationConsulter->getRegistrationsByType('Chamilo\Application\Weblcms\Admin\Extension');

        foreach ($extensionPackages as $extensionPackage)
        {
            $extensionPackageContext = $extensionPackage['context'];

            $class = $extensionPackageContext . '\\CourseAdminValidator';
            if (!class_exists($class))
            {
                throw new RuntimeException(
                    sprintf(
                        'The given package %s does not contain a valid CourseAdminValidator class',
                        $extensionPackageContext
                    )
                );
            }

            $this->courseAdminValidatorExtensions[] = new $class();
        }
    }



    /*
     * Constructor
     * @param Configuration $configuration
     */

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\ConnectionException
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public static function getInstance(): CourseAdminValidator
    {
        if (!self::$instance)
        {
            /**
             * @var \Chamilo\Configuration\Service\Consulter\RegistrationConsulter $registrationConsulter
             */
            $registrationConsulter = DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(
                RegistrationConsulter::class
            );

            self::$instance = new self($registrationConsulter);
        }

        return self::$instance;
    }

    public function getRegistrationConsulter(): RegistrationConsulter
    {
        return $this->registrationConsulter;
    }

    /**
     * Validates whether or not a user is an admin of a course
     *
     * @param User $user
     * @param Course $course
     *
     * @return bool
     */
    public function isUserAdminOfCourse(User $user, Course $course): bool
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