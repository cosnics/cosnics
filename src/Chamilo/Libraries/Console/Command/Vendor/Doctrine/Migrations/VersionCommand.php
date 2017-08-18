<?php

namespace Chamilo\Libraries\Console\Command\Vendor\Doctrine\Migrations;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Extension of the VersionCommand to automatically configure this command based on the given package
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class VersionCommand extends \Doctrine\DBAL\Migrations\Tools\Console\Command\VersionCommand
{
    /**
     * The configurator for doctrine migration commands
     *
     * @var DoctrineMigrationsCommandConfigurator
     */
    private $doctrineMigrationsCommandConfigurator;

    /**
     * Constructor
     *
     * @param DoctrineMigrationsCommandConfigurator $doctrineMigrationsCommandConfigurator
     */
    public function __construct(DoctrineMigrationsCommandConfigurator $doctrineMigrationsCommandConfigurator)
    {
        parent::__construct();

        $this->doctrineMigrationsCommandConfigurator = $doctrineMigrationsCommandConfigurator;
    }

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this->addArgument('package_path', InputArgument::REQUIRED, 'The package path');
        parent::configure();
        $this->setName('doctrine:migrations:version');

        $this->setHelp(<<<EOT
The <info>%command.name%</info> command allows you to manually add and delete migration versions from the version table:

    <info>%command.full_name% package_path YYYYMMDDHHMMSS --add</info>

If you want to delete a version you can use the <comment>--delete</comment> option:

    <info>%command.full_name% package_path YYYYMMDDHHMMSS --delete</info>
EOT
        );

    }

    /**
     * Executes the current command.
     *
     * This method is not abstract because you can use this class
     * as a concrete class. In this case, instead of defining the
     * execute() method, you set the code to execute by passing
     * a Closure to the setCode() method.
     *
     * @param InputInterface  $input  An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     *
     * @return null|int     null or 0 if everything went fine, or an error code
     *
     * @throws \LogicException When this abstract method is not implemented
     * @see    setCode()
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $namespace = $input->getArgument('package_path');

        $configuration = $this->getMigrationConfiguration($input, $output);
        $this->doctrineMigrationsCommandConfigurator->configure($configuration, $namespace);

        parent::execute($input, $output);
    }
}