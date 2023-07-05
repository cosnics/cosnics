<?php
namespace Chamilo\Core\Admin\Language\Component;

use Chamilo\Configuration\Package\Service\PackageBundlesCacheService;
use Chamilo\Core\Admin\Language\Manager;

class ExporterComponent extends Manager
{

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function run()
    {
        ini_set('memory_limit', '-1');
        set_time_limit(0);

        $configurationConsulter = $this->getConfigurationConsulter();

        $translationsFile = $this->getConfigurablePathBuilder()->getTemporaryPath(__NAMESPACE__) . 'translations.csv';
        $this->getFilesystem()->mkdir(dirname($translationsFile));

        $file_handle = fopen($translationsFile, 'w');

        $base_language = $configurationConsulter->getSetting([__NAMESPACE__, 'base_language']);
        $source_language = $configurationConsulter->getSetting([__NAMESPACE__, 'source_language']);
        $target_languages = $configurationConsulter->getSetting([__NAMESPACE__, 'target_languages']);

        $this->writeLine(
            $file_handle, ['Package', 'Variable', 'Base language', 'Source language', 'Target Languages']
        );

        $language_values = ['', '', $base_language, $source_language];
        $target_languages = explode(',', $target_languages);

        foreach ($target_languages as $target_language)
        {
            $language_values[] = $target_language;
        }

        $languages = array_merge($target_languages, [$base_language, $source_language]);

        $this->writeLine($file_handle, $language_values);

        $packages = $this->getPackageBundlesCacheService()->getAllPackages()->getNestedPackages();

        foreach ($packages as $package)
        {
            $translations = [];
            $languagePath = $this->getSystemPathBuilder()->getI18nPath($package->get_context());

            foreach (array_unique($languages) as $language)
            {
                $language_file = $languagePath . $language . '.i18n';
                $translations[$language] = parse_ini_file($language_file);
            }

            $variables = [];

            foreach ($translations as $translation_values)
            {
                $variables = array_merge($variables, array_keys($translation_values));
            }

            $variables = array_unique($variables);

            foreach ($variables as $variable)
            {
                $row = [];
                $row[] = $package;
                $row[] = $variable;
                $row[] = $translations[$base_language][$variable];
                $row[] = $translations[$source_language][$variable];

                foreach ($target_languages as $target_language)
                {
                    $row[] = $translations[$target_language][$variable];
                }

                $this->writeLine($file_handle, $row);
            }
        }

        $this->getFilesystemTools()->sendFileForDownload($translationsFile);
    }

    public function getPackageBundlesCacheService(): PackageBundlesCacheService
    {
        return $this->getService(PackageBundlesCacheService::class);
    }

    private function writeLine($file_handle, array $row = []): void
    {
        fputcsv($file_handle, $row, ';');
    }
}
