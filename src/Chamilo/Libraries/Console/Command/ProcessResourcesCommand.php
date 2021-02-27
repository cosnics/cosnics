<?php

namespace Chamilo\Libraries\Console\Command;

use Chamilo\Libraries\Cache\CacheManagement\CacheManager;
use Chamilo\Libraries\Format\Utilities\ResourceProcessor;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Translation\Translator;

/**
 * Command to process resources of all (or a given) package(s)
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 * @package Chamilo\Libraries\Console\Command
 */
class ProcessResourcesCommand extends ChamiloCommand
{
    const ARG_CONTEXT = 'context';
    const OPT_DISABLE_CACHE_CLEAR = 'disable-cache-clear';

    /**
     *
     * @var \Chamilo\Libraries\Format\Utilities\ResourceProcessor
     */
    protected $resourceProcessor;

    /**
     *
     * @var \Chamilo\Libraries\Cache\CacheManagement\CacheManager
     */
    protected $cacheManager;

    /**
     * ProcessResourcesCommand constructor.
     *
     * @param \Symfony\Component\Translation\Translator $translator
     * @param \Chamilo\Libraries\Format\Utilities\ResourceProcessor $resourceProcessor
     * @param \Chamilo\Libraries\Cache\CacheManagement\CacheManager $cacheManager
     */
    public function __construct(Translator $translator, ResourceProcessor $resourceProcessor, CacheManager $cacheManager
    )
    {
        $this->resourceProcessor = $resourceProcessor;
        $this->cacheManager = $cacheManager;

        parent::__construct($translator);
    }

    /**
     * Configures this command
     */
    protected function configure()
    {
        $this->setName('chamilo:process_resources')
            ->setDescription(
                $this->translator->trans('ProcessResourcesCommandDescription', array(), 'Chamilo\Libraries')
            )->addArgument(
                self::ARG_CONTEXT,
                InputArgument::IS_ARRAY | InputArgument::OPTIONAL,
                $this->translator->trans('ProcessResourcesContextDescription', array(), 'Chamilo\Libraries')
            )
            ->addOption(
                self::OPT_DISABLE_CACHE_CLEAR, 'd', InputOption::VALUE_NONE,
                $this->translator->trans('ProcessResourcesDisableCacheClear', [], 'Chamilo\Libraries')
            );
    }

    /**
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $packages = $input->getArgument(self::ARG_CONTEXT);
        $this->resourceProcessor->processResources($output, $packages);

        if(!$input->getOption(self::OPT_DISABLE_CACHE_CLEAR))
        {
            $this->cacheManager->clear(['chamilo_stylesheets', 'chamilo_javascript']);
        }
    }
}
