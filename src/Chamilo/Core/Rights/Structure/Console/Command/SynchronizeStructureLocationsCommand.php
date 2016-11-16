<?php
namespace Chamilo\Core\Rights\Structure\Console\Command;

use Chamilo\Core\Rights\Structure\Service\StructureLocationConfiguration\Interfaces\SynchronizerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Synchronizes structure locations with the database
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class SynchronizeStructureLocationsCommand extends Command
{

    /**
     *
     * @var SynchronizerInterface
     */
    protected $structureLocationsConfigurationSynchronizer;

    /**
     * SynchronizeStructureLocationsCommand constructor.
     * 
     * @param SynchronizerInterface $structureLocationsConfigurationSynchronizer
     */
    public function __construct(SynchronizerInterface $structureLocationsConfigurationSynchronizer)
    {
        $this->structureLocationsConfigurationSynchronizer = $structureLocationsConfigurationSynchronizer;
        
        parent::__construct();
    }

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this->setName('chamilo:rights:structure:synchronize_locations')->setDescription(
            'Synchronizes structure locations between the configuration files and the database');
    }

    /**
     * Executes the current command.
     * 
     * @param InputInterface $input An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     * @return null|int null or 0 if everything went fine, or an error code
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->structureLocationsConfigurationSynchronizer->synchronize();
    }
}