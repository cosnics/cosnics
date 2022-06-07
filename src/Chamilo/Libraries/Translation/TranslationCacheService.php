<?php
namespace Chamilo\Libraries\Translation;

use Chamilo\Libraries\Cache\FileBasedCacheService;
use Chamilo\Libraries\File\ConfigurablePathBuilder;

/**
 * Manages the cache for the symfony translations
 *
 * @package Chamilo\Libraries\Translation
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class TranslationCacheService extends FileBasedCacheService
{
    protected ConfigurablePathBuilder $configurablePathBuilder;

    public function __construct(ConfigurablePathBuilder $configurablePathBuilder)
    {
        $this->configurablePathBuilder = $configurablePathBuilder;
    }

    function getCachePath(): string
    {
        return $this->configurablePathBuilder->getCachePath(__NAMESPACE__);
    }

    public function warmUp(): TranslationCacheService
    {
        $translatorFactory = new TranslatorFactory($this->configurablePathBuilder);
        $translatorFactory->createTranslator('en_EN');

        return $this;
    }
}