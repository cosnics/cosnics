<?php
namespace Chamilo\Libraries\Console\Command\Vendor\Doctrine\Migrations;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Extension of the ExecuteCommand to automatically configure this command based on the given package
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 * @package Chamilo\Libraries\Console\Command\Vendor\Doctrine\Migrations
 */
class ExecuteCommand extends \Doctrine\Migrations\Tools\Console\Command\ExecuteCommand
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
     * @see \Doctrine\Migrations\Tools\Console\Command\ExecuteCommand::configure()
     */
    protected function configure(): void
    {
        $this->addArgument('package_path', InputArgument::REQUIRED, 'The package path');
        parent::configure();
        $this->setName('doctrine:migrations:execute');

        $this->setHelp(
            <<<EOT
The <info>%command.name%</info> command executes a single migration version up or down manually:

    <info>%command.full_name% package_path YYYYMMDDHHMMSS</info>

If no <comment>--up</comment> or <comment>--down</comment> option is specified it defaults to up:

    <info>%command.full_name% package_path YYYYMMDDHHMMSS --down</info>

You can also execute the migration as a <comment>--dry-run</comment>:

    <info>%command.full_name% package_path YYYYMMDDHHMMSS --dry-run</info>

You can output the would be executed SQL statements to a file with <comment>--write-sql</comment>:

    <info>%command.full_name% package_path YYYYMMDDHHMMSS --write-sql</info>

Or you can also execute the migration without a warning message which you need to interact with:

    <info>%command.full_name% package_path --no-interaction</info>
EOT
        );
    }

    /**
     *
     * @see \Doctrine\Migrations\Tools\Console\Command\ExecuteCommand::execute()
     */
    public function execute(InputInterface $input, OutputInterface $output): ?int
    {
        $namespace = $input->getArgument('package_path');

        $configuration = $this->getMigrationConfiguration($input, $output);
        $this->doctrineMigrationsCommandConfigurator->configure($configuration, $namespace);

        return parent::execute($input, $output);
    }
}