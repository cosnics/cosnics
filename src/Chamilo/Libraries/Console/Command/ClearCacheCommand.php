<?php
namespace Chamilo\Libraries\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * This console command is used to clear all the cache folders
 *
 * @package Chamilo\Libraries\Console\Command
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ClearCacheCommand extends CacheCommand
{
    const OPT_WARMUP = 'warmup';
    const OPT_WARMUP_SHORT = 'w';

    /**
     * Configures this command
     */
    protected function configure()
    {
        $this->setName('chamilo:cache:clear')->addOption(
            self::OPT_WARMUP,
            self::OPT_WARMUP_SHORT,
            InputOption::VALUE_NONE,
            $this->translator->trans('WarmupCache', [], 'Chamilo\Libraries'))->setDescription(
            $this->translator->trans('ClearCacheDescription', [], 'Chamilo\Libraries'));

        parent::configure();
    }

    /**
     * Executes this command
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function executeCacheCommand(InputInterface $input, OutputInterface $output)
    {
        $this->clearCache($input, $output);

        if ($input->getOption(self::OPT_WARMUP))
        {
            $this->warmUpCache($input, $output);
        }
    }
}