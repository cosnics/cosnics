<?php

namespace Chamilo\Application\Weblcms\Console\Command;

use Chamilo\Application\Weblcms\Service\LearningPathProgressFixer;
use Chamilo\Libraries\Utilities\Timer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command to execute the learning path progress fixer
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class LearningPathProgressFixerCommand extends Command
{
    const OPT_DRY_RUN = 'dry_run';
    const OPT_DRY_RUN_SHORT = 'd';

    /**
     * @var LearningPathProgressFixer
     */
    protected $learningPathProgressFixer;

    /**
     * LearningPathProgressFixerCommand constructor.
     *
     * @param LearningPathProgressFixer $learningPathProgressFixerDirector
     */
    public function __construct(LearningPathProgressFixer $learningPathProgressFixerDirector)
    {
        $this->learningPathProgressFixer = $learningPathProgressFixerDirector;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('chamilo:weblcms:learning_path:progress_fixer')
            ->setDescription('Fixes the learning path progress for each attempt')
            ->addOption(
                self::OPT_DRY_RUN, self::OPT_DRY_RUN_SHORT, InputOption::VALUE_NONE,
                'Executes the learning path progress fixer without actually updating in the database'
            );
    }

    /**
     * Executes this command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dryRun = $input->getOption(self::OPT_DRY_RUN);

        if($dryRun)
        {
            $output->writeln('Executing LearningPathProgressFixer in dry run mode');
        }

        ini_set('memory_limit', -1);

        $timer = new Timer();
        $timer->start();

        $this->learningPathProgressFixer->fixLearningPathProgress($dryRun);

        $timer->stop();
        $output->writeln('Time spent: ' . $timer->get_time_in_hours());
    }
}