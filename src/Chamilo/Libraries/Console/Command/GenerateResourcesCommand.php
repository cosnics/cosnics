<?php
namespace Chamilo\Libraries\Console\Command;

use Chamilo\Libraries\Architecture\Resource\ResourceGenerator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Translation\Translator;

/**
 * Command to generate the aggregated resource files
 *
 * @package Chamilo\Libraries\Console\Command
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class GenerateResourcesCommand extends ChamiloCommand
{
    /**
     *
     * @var \Chamilo\Libraries\Architecture\Resource\ResourceGenerator
     */
    protected $resourceGenerator;

    /**
     * @param \Symfony\Component\Translation\Translator $translator
     * @param \Chamilo\Libraries\Architecture\Resource\ResourceGenerator $resourceGenerator
     */
    public function __construct(Translator $translator, ResourceGenerator $resourceGenerator)
    {
        $this->resourceGenerator = $resourceGenerator;

        parent::__construct($translator);
    }

    /**
     * Configures this command
     */
    protected function configure()
    {
        $this->setName('chamilo:generate_resources')->setDescription(
            $this->translator->trans('GenerateResourcesCommandDescription', [], 'Chamilo\Libraries')
        );
    }

    /**
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->resourceGenerator->generateResources();

        return 0;
    }
}
