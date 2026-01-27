<?php
namespace Chamilo\Libraries\UserInterface\Translation\Service;

use Chamilo\Libraries\UserInterface\Translation\Architecture\Interface\TranslationResourcesFinderInterface;
use InvalidArgumentException;
use Symfony\Component\Translation\Loader\LoaderInterface;
use Symfony\Component\Translation\MessageCatalogue;

/**
 * Optimizes the translation resources
 *
 * @package Chamilo\Libraries\Translation
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class TranslationResourcesOptimizer
{

    private string $optimizedTranslationsCachePath;

    private LoaderInterface $translationLoader;

    private TranslationResourcesFinderInterface $translationResourcesFinder;

    public function __construct(
        LoaderInterface $translationLoader, TranslationResourcesFinderInterface $translationResourcesFinder,
        string $optimizedTranslationsCachePath = ''
    )
    {
        $this->setTranslationLoader($translationLoader);
        $this->setTranslationResourcesFinder($translationResourcesFinder);
        $this->setOptimizedTranslationsCachePath($optimizedTranslationsCachePath);
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

        if (!file_exists($optimizedTranslationsCache))
        {
            return $this->optimizeResources($cachePath, $optimizedTranslationsCache);
        }
        else
        {
            return $this->retrieveOptimizedResources($cachePath, $optimizedTranslationsCache);
        }
    }

    public function getOptimizedTranslationsCachePath(): string
    {
        return $this->optimizedTranslationsCachePath;
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function setOptimizedTranslationsCachePath(string $optimizedTranslationsCachePath): void
    {
        if (empty($optimizedTranslationsCachePath))
        {
            throw new InvalidArgumentException('You must provide a valid cache path');
        }

        $this->optimizedTranslationsCachePath = $optimizedTranslationsCachePath;
    }

    public function getTranslationLoader(): LoaderInterface
    {
        return $this->translationLoader;
    }

    public function setTranslationLoader(LoaderInterface $translationLoader): void
    {
        $this->translationLoader = $translationLoader;
    }

    public function getTranslationResourcesFinder(): TranslationResourcesFinderInterface
    {
        return $this->translationResourcesFinder;
    }

    public function setTranslationResourcesFinder(TranslationResourcesFinderInterface $translationResourcesFinder): void
    {
        $this->translationResourcesFinder = $translationResourcesFinder;
    }

    /**
     * @return string[]
     */
    protected function optimizeResources(string $cachePath, string $optimizedTranslationsCache): array
    {
        $resources = [];

        $foundResources = $this->translationResourcesFinder->findTranslationResources();
        foreach ($foundResources as $locale => $localeFoundResources)
        {
            $messageCatalogue = new MessageCatalogue($locale);

            $translationLoader = $this->getTranslationLoader();

            foreach ($localeFoundResources as $domain => $resource)
            {
                $messageCatalogue->addCatalogue($translationLoader->load($resource, $locale, $domain));
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
        foreach ($locales as $locale)
        {
            $resources[$locale] = $cachePath . '/' . $locale . '.php';
        }

        return $resources;
    }
}