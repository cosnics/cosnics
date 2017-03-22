<?php

namespace Chamilo\Core\Repository\Console\Command;

use Chamilo\Core\Repository\Service\ResourceFixer\ResourceFixerDirector;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ResourceFixerCommand extends Command
{
    const OPT_FORCE = 'force';
    const OPT_FORCE_SHORT = 'f';

    /**
     * @var ResourceFixerDirector
     */
    protected $resourceFixerDirector;

    /**
     * The translator
     *
     * @var \Symfony\Component\Translation\Translator
     */
    protected $translator;

    /**
     * Constructor.
     *
     * @param ResourceFixerDirector $resourceFixerDirector
     * @param \Symfony\Component\Translation\Translator $translator
     */
    public function __construct(
        ResourceFixerDirector $resourceFixerDirector, \Symfony\Component\Translation\Translator $translator
    )
    {
        $this->resourceFixerDirector = $resourceFixerDirector;
        $this->translator = $translator;

        parent::__construct();
    }

    /**
     * Configures the current command
     */
    protected function configure()
    {
        $this->setName('chamilo:repository:resource_fixer')
            ->setDescription(
                $this->translator->trans(
                    'ResourceFixerDescription', array(), 'Chamilo\Core\Repository'
                )
            )
            ->addOption(
                self::OPT_FORCE, self::OPT_FORCE_SHORT, InputOption::VALUE_NONE,
                $this->translator->trans('ResourceFixerForceOption', array(), 'Chamilo\Core\Repository')
            );
    }

    /**
     * Executes the current command.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->resourceFixerDirector->fixResources($input->getOption(self::OPT_FORCE));
    }

}