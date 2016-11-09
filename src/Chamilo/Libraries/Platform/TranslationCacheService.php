<?php
namespace Chamilo\Libraries\Platform;

use Chamilo\Configuration\Configuration;
use Chamilo\Configuration\Package\Finder\InternationalizationBundles;
use Chamilo\Configuration\Package\PackageList;
use Chamilo\Libraries\Cache\Doctrine\Service\DoctrinePhpFileCacheService;
use Chamilo\Libraries\File\Path;

/**
 *
 * @package Chamilo\Libraries\Platform
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class TranslationCacheService extends DoctrinePhpFileCacheService
{

    /**
     *
     * @var string[]
     */
    private $internationalizationContexts;

    /**
     *
     * @see \Chamilo\Libraries\Cache\IdentifiableCacheService::getIdentifiers()
     */
    public function getIdentifiers()
    {
        return array_keys(Configuration :: getInstance()->getLanguages());
    }

    /**
     *
     * @see \Chamilo\Libraries\Cache\Doctrine\DoctrineCacheService::getCachePathNamespace()
     */
    public function getCachePathNamespace()
    {
        return __NAMESPACE__ . '\Translation';
    }

    /**
     *
     * @return string[]
     */
    private function getInternationalizationContexts()
    {
        if (! isset($this->internationalizationContexts))
        {
            $internationalizationBundles = new InternationalizationBundles(PackageList :: ROOT);
            $this->internationalizationContexts = $internationalizationBundles->getPackageNamespaces();
        }

        return $this->internationalizationContexts;
    }

    /**
     *
     * @param string $context
     * @param string $isocode
     * @return string
     */
    private function getInternationalizationPath($context, $isocode)
    {
        return Path :: getInstance()->getI18nPath($context) . $isocode . '.i18n';
    }

    /**
     *
     * @see \Chamilo\Libraries\Cache\IdentifiableCacheService::warmUpForIdentifier()
     */
    public function warmUpForIdentifier($identifier)
    {
        $i18nStrings = array();

        foreach ($this->getInternationalizationContexts() as $internationalizationContext)
        {
            $i18nPath = $this->getInternationalizationPath($internationalizationContext, $identifier);

            if (! is_readable($i18nPath))
            {
                continue;
            }

            $i18nContextStrings = parse_ini_file($i18nPath);

            if (! $i18nContextStrings)
            {
                continue;
            }

            $i18nStrings[$internationalizationContext] = $i18nContextStrings;
        }

        return $this->getCacheProvider()->save($identifier, $i18nStrings);
    }
}