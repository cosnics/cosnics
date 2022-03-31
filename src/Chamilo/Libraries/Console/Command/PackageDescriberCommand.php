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
     * @var \Chamilo\Libraries\Cache\CacheManagement\CacheManager
     */
    protected $cacheManager;

    /**
     *
     * @param \Symfony\Component\Translation\Translator $translator
     * @param \Chamilo\Libraries\Format\Utilities\PackageDescriber $packageDescriber
     * @param \Chamilo\Libraries\Cache\CacheManagement\CacheManager $cacheManager
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
            $this->translator->trans('ProcessResourcesCommandDescription', [], 'Chamilo\Libraries'))->addArgument(
            self::ARG_CONTEXT,
            InputArgument::IS_ARRAY | InputArgument::OPTIONAL,
            $this->translator->trans('ProcessResourcesContextDescription', [], 'Chamilo\Libraries'));
    }

    /**
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $packages = $input->getArgument(self::ARG_CONTEXT);
        $this->packageDescriber->processPackages($packages, $output);
        // TODO: Check which caches should be cleared?
        $this->cacheManager->clear(['chamilo_stylesheets', 'chamilo_javascript']);

        return 0;
    }
}
