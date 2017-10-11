<?php
namespace Chamilo\Libraries\Console\Command;

use Chamilo\Libraries\Cache\CacheManagement\CacheManager;
use Chamilo\Libraries\Format\Utilities\PackageDescriber;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Translation\Translator;

/**
 *
 * @package Chamilo\Libraries\Console\Command
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class PackageDescriberCommand extends ChamiloCommand
{
    const ARG_CONTEXT = 'context';

    /**
     *
     * @var \Chamilo\Libraries\Format\Utilities\PackageDescriber
     */
    protected $packageDescriber;

    /**
     *
     * @var CacheManager
     */
    protected $cacheManager;

    /**
     *
     * @param Translator $translator
     * @param PackageDescriber $packageDescriber
     * @param CacheManager $cacheManager
     */
    public function __construct(Translator $translator, PackageDescriber $packageDescriber, CacheManager $cacheManager)
    {
        $this->packageDescriber = $packageDescriber;
        $this->cacheManager = $cacheManager;

        parent::__construct($translator);
    }

    /**
     * Configures this command
     */
    protected function configure()
    {
        $this->setName('chamilo:process_package_description')->setDescription(
            $this->translator->trans('ProcessResourcesCommandDescription', array(), 'Chamilo\Libraries'))->addArgument(
            self::ARG_CONTEXT,
            InputArgument::IS_ARRAY | InputArgument::OPTIONAL,
            $this->translator->trans('ProcessResourcesContextDescription', array(), 'Chamilo\Libraries'));
    }

    /**
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $packages = $input->getArgument(self::ARG_CONTEXT);
        $this->packageDescriber->processPackages($packages, $output);
        $this->cacheManager->clear(['chamilo_stylesheets', 'chamilo_javascript']);
    }
}
