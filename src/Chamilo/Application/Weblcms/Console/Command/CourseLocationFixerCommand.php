<?php

namespace Chamilo\Application\Weblcms\Console\Command;

use Chamilo\Application\Weblcms\Console\ConsoleOutputLogger;
use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Rights\CourseManagementRights;
use Chamilo\Application\Weblcms\Service\RightsLocationFixer\RightsLocationFixer;
use Chamilo\Application\Weblcms\Storage\DataClass\RightsLocation;
use Chamilo\Application\Weblcms\Storage\Repository\CourseRepository;
use Chamilo\Application\Weblcms\Storage\Repository\RightsLocationRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Fixes the rights locations of a given course
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CourseLocationFixerCommand extends Command
{
    const ARG_COURSE = 'course';

    protected CourseRepository $courseRepository;

    public function __construct(CourseRepository $courseRepository)
    {
        parent::__construct();

        $this->courseRepository = $courseRepository;
    }

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this->setName('chamilo:weblcms:fix_course_location')
            ->setDescription('Fixes the right location for the course in the general courses tree (tree with types and courses)')
            ->addArgument(self::ARG_COURSE, InputArgument::REQUIRED, 'The course identifier');
    }

    /**
     * Executes the current command.
     *
     * @param InputInterface $input An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     *
     * @return null|int null or 0 if everything went fine, or an error code
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $courseId = $input->getArgument(self::ARG_COURSE);
        $course = $this->courseRepository->findCourse($courseId);
        if(!$course instanceof Course)
        {
            $output->writeln("The course with id {$courseId} could not be found");
            return -1;

        }

        $location = $course->get_rights_location();
        if($location instanceof RightsLocation)
        {
            $output->writeln("The location for the course was found. Nothing left to fix");
            return 0;
        }

        $output->writeln("The location for the course could not be found. Fixing location");

        // Create location in the course subtree
        $parent_id = $course->get_parent_rights_location()->get_id();

        if (
            !CourseManagementRights::getInstance()->create_location_in_courses_subtree(
                CourseManagementRights::TYPE_COURSE,
                $course->getId(),  $parent_id, 0, false, 0
            )
        )
        {
            $output->writeln("The location for the course could not be created");
            return -1;
        }

        $output->writeln("The location for the course has been created");

        return null;
    }
}