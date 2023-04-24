<?php
namespace Chamilo\Libraries\Console\Command;

use Chamilo\Libraries\Cache\CacheManagement\CacheDataPreLoaderManager;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Libraries\Console\Command
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class PreLoadCacheCommand extends ChamiloCommand
{
    public const ARG_CACHE_DATA_PRELOADER_SERVICES = 'cache_data_preloader_services';

    public const OPT_LIST = 'list';
    public const OPT_LIST_SHORT = 'l';

    public const OPT_PRELOAD = 'preload';
    public const OPT_PRELOAD_SHORT = 'p';

    protected CacheDataPreLoaderManager $cacheDataPreLoaderManager;

    public function __construct(Translator $translator, CacheDataPreLoaderManager $symfonyCacheAdapterManager)
    {
        $this->cacheDataPreLoaderManager = $symfonyCacheAdapterManager;
        parent::__construct($translator);
    }

    protected function configure()
    {
        $this->setName('chamilo:cache:preload')->addOption(
            self::OPT_PRELOAD, self::OPT_PRELOAD_SHORT, InputOption::VALUE_NONE,
            $this->translator->trans('PreLoadCache', [], 'Chamilo\Libraries')
        )->setDescription(
            $this->translator->trans('PreLoadCacheDescription', [], 'Chamilo\Libraries')
        );

        $this->addOption(
            self::OPT_LIST, self::OPT_LIST_SHORT, InputOption::VALUE_NONE,
            $this->translator->trans('ListCacheDataPreLoaderManagers', [], 'Chamilo\Libraries')
        )->addArgument(
            self::ARG_CACHE_DATA_PRELOADER_SERVICES, InputArgument::IS_ARRAY | InputArgument::OPTIONAL,
            $this->translator->trans('CacheDataPreLoaderManagers', [], 'Chamilo\Libraries')
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($this->listCacheDataPreLoaderServices($input, $output))
        {
            return 0;
        }

        if ($input->getOption(self::OPT_PRELOAD))
        {
            $this->preLoad($input, $output);
        }

        return 0;
    }

    /**
     * @return string[]
     */
    protected function getSelectedCacheDataPreLoadServices(InputInterface $input): array
    {
        return $input->getArgument(self::ARG_CACHE_DATA_PRELOADER_SERVICES);
    }

    protected function listCacheDataPreLoaderServices(InputInterface $input, OutputInterface $output): bool
    {
        if ($input->getOption(self::OPT_LIST))
        {
            $output->writeln(
                '<comment>' . $this->translator->trans('AvailableCacheDataPreLoaderServices', [], 'Chamilo\Libraries') .
                '</comment>'
            );
            $output->writeln('');

            foreach ($this->cacheDataPreLoaderManager->getCacheDataPreLoaderServiceAliases() as $serviceAlias)
            {
                $output->writeln('<info>' . $serviceAlias . '</info>');
            }

            return true;
        }

        return false;
    }

    protected function preLoad(InputInterface $input, OutputInterface $output)
    {
        $this->cacheDataPreLoaderManager->preLoad($this->getSelectedCacheDataPreLoadServices($input));
        $output->writeln($this->translator->trans('CachePreLoaded', [], 'Chamilo\Libraries'));
    }
}