<?php
namespace Chamilo\Libraries\Console\Command;

use Chamilo\Libraries\Cache\CacheDirector;
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
    const OPT_LIST = 'list';
    const OPT_LIST_SHORT = 'l';
    const ARG_CACHE_SERVICES = 'cache_services';

    /**
     * The CacheDirector
     * 
     * @var CacheDirector
     */
    protected $cacheDirector;

    /**
     * Constructor
     * 
     * @param Translator $translator
     * @param CacheDirector $cacheDirector
     */
    public function __construct(Translator $translator, CacheDirector $cacheDirector)
    {
        $this->cacheDirector = $cacheDirector;
        parent::__construct($translator);
    }

    /**
     * Configures this command
     */
    protected function configure()
    {
        $this->addOption(
            self::OPT_LIST, 
            self::OPT_LIST_SHORT, 
            InputOption::VALUE_NONE, 
            $this->translator->trans('ListCacheServices', array(), 'Chamilo\Libraries'))->addArgument(
            self::ARG_CACHE_SERVICES, 
            InputArgument::IS_ARRAY | InputArgument::OPTIONAL, 
            $this->translator->trans('CacheServices', array(), 'Chamilo\Libraries'));
    }

    /**
     * Executes this command
     * 
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($this->listCacheServices($input, $output))
        {
            return null;
        }
        
        return $this->executeCacheCommand($input, $output);
    }

    /**
     * Returns the selected cache services from the interface
     * 
     * @param InputInterface $input
     *
     * @return string[]
     */
    protected function getSelectedCacheServices(InputInterface $input)
    {
        return $input->getArgument(self::ARG_CACHE_SERVICES);
    }

    /**
     * Lists the cache services if the option is selected and returns whether or not they were listed
     * 
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return bool
     */
    protected function listCacheServices(InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption(self::OPT_LIST))
        {
            $output->writeln(
                '<comment>' . $this->translator->trans('AvailableCacheServices', array(), 'Chamilo\Libraries') . '</comment>');
            $output->writeln('');
            
            foreach ($this->cacheDirector->getCacheServiceAliases() as $serviceAlias)
            {
                $output->writeln('<info>' . $serviceAlias . '</info>');
            }
            
            return true;
        }
        
        return false;
    }

    /**
     * Clears the cache
     * 
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function clearCache(InputInterface $input, OutputInterface $output)
    {
        $this->cacheDirector->clear($this->getSelectedCacheServices($input));
        $output->writeln($this->translator->trans('CacheCleared', array(), 'Chamilo\Libraries'));
    }

    /**
     * Warms up the cache
     * 
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function warmUpCache(InputInterface $input, OutputInterface $output)
    {
        $this->cacheDirector->warmUp($this->getSelectedCacheServices($input));
        $output->writeln($this->translator->trans('CacheWarmedUp', array(), 'Chamilo\Libraries'));
    }

    /**
     * Executes the specific cache code for this command
     * 
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|null
     */
    abstract protected function executeCacheCommand(InputInterface $input, OutputInterface $output);
}