<?php
namespace Chamilo\Libraries\Console\Command\Vendor\Doctrine\Migrations;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Extension of the DiffCommand to automatically configure this command based on the given package
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 * @package Chamilo\Libraries\Console\Command\Vendor\Doctrine\Migrations
 */
class DiffCommand extends \Doctrine\Migrations\Tools\Console\Command\DiffCommand
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
     * @see \Doctrine\Migrations\Tools\Console\Command\DiffCommand::configure()
     */
    protected function configure(): void
    {
        $this->addArgument('package_path', InputArgument::REQUIRED, 'The package path');
        parent::configure();
        $this->setName('doctrine:migrations:diff');

        $this->setHelp(
            <<<EOT
The <info>%command.name%</info> command generates a migration by comparing your current database to your mapping information:

    <info>%command.full_name% package_path</info>

You can optionally specify a <comment>--editor-cmd</comment> option to open the generated file in your favorite editor:

    <info>%command.full_name% package_path --editor-cmd=mate</info>
EOT
        );
    }

    /**
     *
     * @see \Doctrine\Migrations\Tools\Console\Command\DiffCommand::execute()
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $namespace = $input->getArgument('package_path');

        $configuration = $this->getMigrationConfiguration($input, $output);
        $this->doctrineMigrationsCommandConfigurator->configure($configuration, $namespace);

        return parent::execute($input, $output);
    }
}