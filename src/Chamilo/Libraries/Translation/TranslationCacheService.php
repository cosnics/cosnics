<?php
namespace Chamilo\Libraries\Translation;

use Chamilo\Libraries\Cache\FileBasedCacheService;
use Chamilo\Libraries\File\ConfigurablePathBuilder;
use Chamilo\Libraries\File\Path;

/**
 * Manages the cache for the symfony translations
 *
 * @package Chamilo\Libraries\Translation
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class TranslationCacheService extends FileBasedCacheService
{
    /**
     * @var \Chamilo\Libraries\File\ConfigurablePathBuilder
     */
    protected $configurablePathBuilder;

    /**
     * TranslationCacheService constructor.
     *
     * @param \Chamilo\Libraries\File\ConfigurablePathBuilder $configurablePathBuilder
     */
    public function __construct(ConfigurablePathBuilder $configurablePathBuilder)
    {
        $this->configurablePathBuilder = $configurablePathBuilder;
    }

    /**
     *
     * @see \Chamilo\Libraries\Cache\FileBasedCacheService::getCachePath()
     */
    function getCachePath()
    {
        return Path::getInstance()->getCachePath(__NAMESPACE__);
    }

    /**
     *
     * @see \Chamilo\Libraries\Cache\FileBasedCacheService::warmUp()
     */
    public function warmUp()
    {
        $translatorFactory = new TranslatorFactory($this->configurablePathBuilder);
        $translatorFactory->createTranslator('en_EN');

        return $this;
    }
}