<?php
namespace Chamilo\Libraries\Translation;

use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Cache\FileBasedCacheService;

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
     *
     * @see \Chamilo\Libraries\Cache\FileBasedCacheService::warmUp()
     */
    public function warmUp()
    {
        $translatorFactory = new TranslatorFactory();
        $translatorFactory->createTranslator('en_EN');

        return $this;
    }

    /**
     *
     * @see \Chamilo\Libraries\Cache\FileBasedCacheService::getCachePath()
     */
    function getCachePath()
    {
        return Path::getInstance()->getCachePath(__NAMESPACE__);
    }
}