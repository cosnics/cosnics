<?php

namespace Chamilo\Libraries\Console\Command;

use Chamilo\Configuration\Configuration;
use Chamilo\Libraries\Cache\CacheManagement\CacheManager;
use Chamilo\Libraries\File\Filesystem;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Translation\Translator;

/**
 * Abstract cache command for clear and warmup cache
 *
 * @package Chamilo\Libraries\Console\Command
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class CacheCommand extends ChamiloCommand
{
    public const ARG_CACHE_SERVICES = 'cache_services';

    public const OPT_LIST = 'list';

    public const OPT_LIST_SHORT = 'l';

    protected CacheManager $cacheManager;

    public function __construct(Translator $translator, CacheManager $cacheManager)
    {
        $this->cacheManager = $cacheManager;
        parent::__construct($translator);
    }

    /**
     * Clears the cache
     */
    protected function clearCache(InputInterface $input, OutputInterface $output)
    {
        $this->cacheManager->clear($this->getSelectedCacheServices($input));
        $output->writeln($this->translator->trans('CacheCleared', [], 'Chamilo\Libraries'));

        $cachePath = Configuration::getInstance()->get(
            'Chamilo\Configuration', 'storage', 'cache_path'
        );

        Filesystem::chmod($cachePath, '2770', true);
    }

    protected function configure()
    {
        $this->addOption(
            self::OPT_LIST, self::OPT_LIST_SHORT, InputOption::VALUE_NONE,
            $this->translator->trans('ListCacheServices', [], 'Chamilo\Libraries')
        )->addArgument(
            self::ARG_CACHE_SERVICES, InputArgument::IS_ARRAY | InputArgument::OPTIONAL,
            $this->translator->trans('CacheServices', [], 'Chamilo\Libraries')
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($this->listCacheServices($input, $output))
        {
            return null;
        }

        return $this->executeCacheCommand($input, $output);
    }

    /**
     * Executes the specific cache code for this command
     */
    abstract protected function executeCacheCommand(InputInterface $input, OutputInterface $output);

    /**
     * Returns the selected cache services from the interface
     *
     * @return string[]
     */
    protected function getSelectedCacheServices(InputInterface $input): array
    {
        return $input->getArgument(self::ARG_CACHE_SERVICES);
    }

    /**
     * Lists the cache services if the option is selected and returns whether or not they were listed
     */
    protected function listCacheServices(InputInterface $input, OutputInterface $output): bool
    {
        if ($input->getOption(self::OPT_LIST))
        {
            $output->writeln(
                '<comment>' . $this->translator->trans('AvailableCacheServices', [], 'Chamilo\Libraries') . '</comment>'
            );
            $output->writeln('');

            foreach ($this->cacheManager->getCacheServiceAliases() as $serviceAlias)
            {
                $output->writeln('<info>' . $serviceAlias . '</info>');
            }

            return true;
        }

        return false;
    }

    /**
     * Warms up the cache
     */
    protected function warmUpCache(InputInterface $input, OutputInterface $output)
    {
        $this->cacheManager->warmUp($this->getSelectedCacheServices($input));
        $output->writeln($this->translator->trans('CacheWarmedUp', [], 'Chamilo\Libraries'));
    }
}
