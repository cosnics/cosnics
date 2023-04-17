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
    protected ConfigurablePathBuilder $configurablePathBuilder;

    protected TranslatorFactory $translatorFactory;

    public function __construct(TranslatorFactory $translatorFactory)
    {
        $this->translatorFactory = $translatorFactory;
    }

    public function getCachePath(): string
    {
        return $this->configurablePathBuilder->getCachePath(__NAMESPACE__);
    }

    public function getTranslatorFactory(): TranslatorFactory
    {
        return $this->translatorFactory;
    }

    /**
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function warmUp(): bool
    {
        $this->getTranslatorFactory()->createTranslator('en_EN');

        return true;
    }
}