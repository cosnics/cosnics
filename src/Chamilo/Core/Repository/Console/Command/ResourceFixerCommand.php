<?php

namespace Chamilo\Core\Repository\Console\Command;

use Chamilo\Core\Repository\Service\ResourceFixer\ResourceFixerDirector;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ResourceFixerCommand extends Command
{
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
     * @param ResourceFixerDirector $contentObjectResourceFixer
     * @param \Symfony\Component\Translation\Translator $translator
     */
    public function __construct(
        ResourceFixerDirector $contentObjectResourceFixer, \Symfony\Component\Translation\Translator $translator
    )
    {
        $this->resourceFixerDirector = $contentObjectResourceFixer;
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
        $this->resourceFixerDirector->fixResources();
    }

}