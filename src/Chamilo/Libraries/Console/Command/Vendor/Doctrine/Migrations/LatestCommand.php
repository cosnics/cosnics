<?php
namespace Chamilo\Libraries\Console\Command\Vendor\Doctrine\Migrations;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Extension of the LatestCommand to automatically configure this command based on the given package
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 * @package Chamilo\Libraries\Console\Command\Vendor\Doctrine\Migrations
 */
class LatestCommand extends \Doctrine\Migrations\Tools\Console\Command\LatestCommand
{

    /**
     * The configurator for doctrine migration commands
     *
     * @var \Chamilo\Libraries\Console\Command\Vendor\Doctrine\Migrations\DoctrineMigrationsCommandConfigurator
     */
    private $doctrineMigrationsCommandConfigurator;

    /**
     * Constructor
     *
     * @param \Chamilo\Libraries\Console\Command\Vendor\Doctrine\Migrations\DoctrineMigrationsCommandConfigurator $doctrineMigrationsCommandConfigurator
     */
    public function __construct(DoctrineMigrationsCommandConfigurator $doctrineMigrationsCommandConfigurator)
    {
        parent::__construct();

        $this->doctrineMigrationsCommandConfigurator = $doctrineMigrationsCommandConfigurator;
    }

    /**
     *
     * @see \Doctrine\Migrations\Tools\Console\Command\LatestCommand::configure()
     */
    protected function configure(): void
    {
        $this->addArgument('package_path', InputArgument::REQUIRED, 'The package path');
        parent::configure();
        $this->setName('doctrine:migrations:latest');
    }

    /**
     *
     * @see \Doctrine\Migrations\Tools\Console\Command\LatestCommand::execute()
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $namespace = $input->getArgument('package_path');

        $configuration = $this->getMigrationConfiguration($input, $output);
        $this->doctrineMigrationsCommandConfigurator->configure($configuration, $namespace);

        return parent::execute($input, $output);
    }
}