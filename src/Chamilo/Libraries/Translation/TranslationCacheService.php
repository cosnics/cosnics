<?php
namespace Chamilo\Libraries\Translation;

use Chamilo\Libraries\Cache\FileBasedCacheService;
use Chamilo\Libraries\File\ConfigurablePathBuilder;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Manages the cache for the symfony translations
 *
 * @package Chamilo\Libraries\Translation
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
    public function initializeCache()
    {
        $this->getTranslatorFactory()->createTranslator('en_EN');
    }
}