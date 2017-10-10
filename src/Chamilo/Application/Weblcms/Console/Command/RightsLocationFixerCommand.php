<?php

namespace Chamilo\Application\Weblcms\Console\Command;

use Chamilo\Application\Weblcms\Console\ConsoleOutputLogger;
use Chamilo\Application\Weblcms\Service\RightsLocationFixer\RightsLocationFixer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Fixes the rights locations of a given course
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class RightsLocationFixerCommand extends Command
{
    const ARG_COURSE = 'course';

    /**
     * @var \Chamilo\Application\Weblcms\Service\RightsLocationFixer\RightsLocationFixer
     */
    protected $rightsLocationFixer;

    /**
     * @param \Chamilo\Application\Weblcms\Service\RightsLocationFixer\RightsLocationFixer $rightsLocationFixer
     */
    public function __construct(RightsLocationFixer $rightsLocationFixer)
    {
        $this->rightsLocationFixer = $rightsLocationFixer;

        parent::__construct();
    }

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this->setName('chamilo:weblcms:fix_rights_locations')
            ->setDescription('Fixes the rights locations of a given course')
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
        $logger = new ConsoleOutputLogger($output);
        $this->rightsLocationFixer->fixRightsLocationsForCourseId($input->getArgument(self::ARG_COURSE), $logger);

        return null;
    }
}