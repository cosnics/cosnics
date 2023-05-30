<?php
namespace Chamilo\Libraries\Console\Command;

use Chamilo\Libraries\Cache\CacheManagement\SymfonyCacheAdapterManager;
use Chamilo\Libraries\Utilities\StringUtilities;
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
class ClearCacheCommand extends ChamiloCommand
{
    public const ARG_CACHE_ADAPTERS = 'cache_adapters';

    public const OPT_CLEAR = 'clear';
    public const OPT_CLEAR_SHORT = 'c';
    public const OPT_LIST = 'list';
    public const OPT_LIST_SHORT = 'l';

    protected SymfonyCacheAdapterManager $symfonyCacheAdapterManager;

    public function __construct(Translator $translator, SymfonyCacheAdapterManager $symfonyCacheAdapterManager)
    {
        $this->symfonyCacheAdapterManager = $symfonyCacheAdapterManager;
        parent::__construct($translator);
    }

    protected function clear(InputInterface $input, OutputInterface $output)
    {
        $this->symfonyCacheAdapterManager->clear($this->getSelectedCacheDataPreLoadServices($input));
        $output->writeln($this->translator->trans('CacheCleared', [], StringUtilities::LIBRARIES));
    }

    protected function configure()
    {
        $this->setName('chamilo:cache:clear')->addOption(
            self::OPT_CLEAR, self::OPT_CLEAR_SHORT, InputOption::VALUE_NONE,
            $this->translator->trans('ClearCache', [], StringUtilities::LIBRARIES)
        )->setDescription(
            $this->translator->trans('ClearCacheDescription', [], StringUtilities::LIBRARIES)
        );

        $this->addOption(
            self::OPT_LIST, self::OPT_LIST_SHORT, InputOption::VALUE_NONE,
            $this->translator->trans('ListCacheAdapters', [], StringUtilities::LIBRARIES)
        )->addArgument(
            self::ARG_CACHE_ADAPTERS, InputArgument::IS_ARRAY | InputArgument::OPTIONAL,
            $this->translator->trans('CacheAdapters', [], StringUtilities::LIBRARIES)
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($this->listCacheAdapters($input, $output))
        {
            return 0;
        }

        if ($input->getOption(self::OPT_CLEAR))
        {
            $this->clear($input, $output);
        }

        return 0;
    }

    /**
     * @return string[]
     */
    protected function getSelectedCacheDataPreLoadServices(InputInterface $input): array
    {
        return $input->getArgument(self::ARG_CACHE_ADAPTERS);
    }

    protected function listCacheAdapters(InputInterface $input, OutputInterface $output): bool
    {
        if ($input->getOption(self::OPT_LIST))
        {
            $output->writeln(
                '<comment>' . $this->translator->trans('AvailableCacheAdapters', [], StringUtilities::LIBRARIES) . '</comment>'
            );
            $output->writeln('');

            foreach ($this->symfonyCacheAdapterManager->getCacheAdapterAliases() as $adapterAlias)
            {
                $output->writeln('<info>' . $adapterAlias . '</info>');
            }

            return true;
        }

        return false;
    }
}