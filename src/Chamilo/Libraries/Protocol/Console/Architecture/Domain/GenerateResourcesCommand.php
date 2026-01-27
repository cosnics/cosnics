<?php
namespace Chamilo\Libraries\Protocol\Console\Architecture\Domain;

use Chamilo\Libraries\Service\Resource\ResourceGenerator;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Libraries\Console\Command
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class GenerateResourcesCommand extends ChamiloCommand
{

    protected ResourceGenerator $resourceGenerator;

    public function __construct(Translator $translator, ResourceGenerator $resourceGenerator)
    {
        $this->resourceGenerator = $resourceGenerator;
        parent::__construct($translator);
    }

    protected function configure()
    {
        $this->setName('chamilo:generate_resources')->setDescription(
            $this->translator->trans('GenerateResourcesCommandDescription', [], StringUtilities::LIBRARIES)
        );
    }

    /**
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->resourceGenerator->generateResources();

        return 0;
    }
}
