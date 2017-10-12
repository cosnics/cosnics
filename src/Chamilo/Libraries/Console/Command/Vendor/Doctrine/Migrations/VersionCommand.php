<?php
namespace Chamilo\Libraries\Console\Command\Vendor\Doctrine\Migrations;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Extension of the VersionCommand to automatically configure this command based on the given package
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 * @package Chamilo\Libraries\Console\Command\Vendor\Doctrine\Migrations
 */
class VersionCommand extends \Doctrine\DBAL\Migrations\Tools\Console\Command\VersionCommand
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
     * @see \Doctrine\DBAL\Migrations\Tools\Console\Command\VersionCommand::configure()
     */
    protected function configure()
    {
        $this->addArgument('package_path', InputArgument::REQUIRED, 'The package path');
        parent::configure();
        $this->setName('doctrine:migrations:version');

        $this->setHelp(
            <<<EOT
The <info>%command.name%</info> command allows you to manually add and delete migration versions from the version table:

    <info>%command.full_name% package_path YYYYMMDDHHMMSS --add</info>

If you want to delete a version you can use the <comment>--delete</comment> option:

    <info>%command.full_name% package_path YYYYMMDDHHMMSS --delete</info>
EOT
);
    }

    /**
     *
     * @see \Doctrine\DBAL\Migrations\Tools\Console\Command\VersionCommand::execute()
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $namespace = $input->getArgument('package_path');

        $configuration = $this->getMigrationConfiguration($input, $output);
        $this->doctrineMigrationsCommandConfigurator->configure($configuration, $namespace);

        parent::execute($input, $output);
    }
}