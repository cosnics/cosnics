<?php
namespace Chamilo\Libraries\UserInterface\Translation\Service;

use Chamilo\Libraries\Filesystem\Service\ConfigurablePathBuilder;
use Chamilo\Libraries\Storage\Service\FileBasedCacheService;
use Chamilo\Libraries\UserInterface\Translation\Factory\TranslatorFactory;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @package Chamilo\Libraries\UserInterface\Translation\Service
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class TranslationCacheService extends FileBasedCacheService
{
    protected TranslatorFactory $translatorFactory;

    public function __construct(
        ConfigurablePathBuilder $configurablePathBuilder, TranslatorFactory $translatorFactory, Filesystem $filesystem
    )
    {
        parent::__construct($configurablePathBuilder, $filesystem);

        $this->translatorFactory = $translatorFactory;
    }

    public function getCachePath(): string
    {
        return $this->getTranslatorFactory()->getTranslationCachePath();
    }

    public function getTranslatorFactory(): TranslatorFactory
    {
        return $this->translatorFactory;
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function initializeCache(): void
    {
        $this->getTranslatorFactory()->createTranslator('en_EN');
    }
}