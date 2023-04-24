<?php
namespace Chamilo\Libraries\Translation;

use Chamilo\Libraries\Cache\FileBasedCacheService;
use Chamilo\Libraries\File\ConfigurablePathBuilder;

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

    public function __construct(ConfigurablePathBuilder $configurablePathBuilder, TranslatorFactory $translatorFactory)
    {
        parent::__construct($configurablePathBuilder);

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

    public function preLoadCacheData()
    {
        $this->getTranslatorFactory()->createTranslator('en_EN');
    }
}