<?php
namespace Chamilo\Libraries\UserInterface\Translation\Service;

use Chamilo\Libraries\UserInterface\Translation\Architecture\Interface\TranslationResourcesFinderInterface;
use InvalidArgumentException;
use Symfony\Component\Translation\Loader\LoaderInterface;
use Symfony\Component\Translation\MessageCatalogue;

/**
 * @package Chamilo\Libraries\UserInterface\Translation\Service
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class TranslationResourcesOptimizer
{
    public function __construct(
        protected LoaderInterface $translationLoader,
        protected TranslationResourcesFinderInterface $translationResourcesFinder,
        protected string $optimizedTranslationsCachePath = ''
    )
    {
    }

    /**
     * Optimizes the translation resources and returns the paths to the optimized translation resources
     *
     * @return string[]
     */
    public function getOptimizedTranslationResources(): array
    {
        $cachePath = $this->optimizedTranslationsCachePath;
        $optimizedTranslationsCache = $cachePath . '/locale.php';

        if (!file_exists($optimizedTranslationsCache)) {
            return $this->optimizeResources($cachePath, $optimizedTranslationsCache);
        }
        else {
            return $this->retrieveOptimizedResources($cachePath, $optimizedTranslationsCache);
        }
    }

    /**
     * @return string[]
     */
    protected function optimizeResources(string $cachePath, string $optimizedTranslationsCache): array
    {
        $resources = [];

        $foundResources = $this->translationResourcesFinder->findTranslationResources();
        foreach ($foundResources as $locale => $localeFoundResources) {
            $messageCatalogue = new MessageCatalogue($locale);

            foreach ($localeFoundResources as $domain => $resource) {
                $messageCatalogue->addCatalogue($this->translationLoader->load($resource, $locale, $domain));
            }

            $resourcePath = $cachePath . '/' . $locale . '.php';
            file_put_contents($resourcePath, "<?php\n\nreturn " . var_export($messageCatalogue->all(), true) . ";\n");

            $resources[$locale] = $resourcePath;
        }

        file_put_contents(
            $optimizedTranslationsCache, "<?php\n\nreturn " . var_export(array_keys($resources), true) . ";\n"
        );

        return $resources;
    }

    /**
     * @return string[]
     */
    protected function retrieveOptimizedResources(string $cachePath, string $optimizedTranslationsCache): array
    {
        $resources = [];

        $locales = require($optimizedTranslationsCache);
        foreach ($locales as $locale) {
            $resources[$locale] = $cachePath . '/' . $locale . '.php';
        }

        return $resources;
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function setOptimizedTranslationsCachePath(string $optimizedTranslationsCachePath): void
    {
        if (empty($optimizedTranslationsCachePath)) {
            throw new InvalidArgumentException('You must provide a valid cache path');
        }

        $this->optimizedTranslationsCachePath = $optimizedTranslationsCachePath;
    }
}