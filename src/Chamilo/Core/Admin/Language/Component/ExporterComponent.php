<?php
namespace Chamilo\Core\Admin\Language\Component;

use Chamilo\Configuration\Configuration;
use Chamilo\Configuration\Package\PlatformPackageBundles;
use Chamilo\Core\Admin\Language\Manager;

class ExporterComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        ini_set('memory_limit', '-1');
        set_time_limit(0);

        $translations_file = $this->getConfigurablePathBuilder()->getTemporaryPath(__NAMESPACE__) . 'translations.csv';
        $this->getFilesystem()->mkdir(dirname($translations_file));

        $time_start = microtime(true);

        $file_handle = fopen($translations_file, 'w');

        $base_language = Configuration::getInstance()->get_setting([__NAMESPACE__, 'base_language']);
        $source_language = Configuration::getInstance()->get_setting([__NAMESPACE__, 'source_language']);
        $target_languages = Configuration::getInstance()->get_setting([__NAMESPACE__, 'target_languages']);

        $this->write_line(
            $file_handle, ['Package', 'Variable', 'Base language', 'Source language', 'Target Languages']
        );

        $language_values = ['', '', $base_language, $source_language];
        $target_languages = explode(',', $target_languages);
        foreach ($target_languages as $target_language)
        {
            $language_values[] = $target_language;
        }

        $languages = array_merge($target_languages, [$base_language, $source_language]);

        $this->write_line($file_handle, $language_values);

        $package_list = PlatformPackageBundles::getInstance()->get_type_packages();

        foreach ($package_list as $packages)
        {
            foreach ($packages as $package)
            {
                $translations = [];
                $language_path = $this->getSystemPathBuilder()->namespaceToFullPath($package) . 'resources/i18n/';

                foreach (array_unique($languages) as $language)
                {
                    $language_file = $language_path . $language . '.i18n';
                    $translations[$language] = parse_ini_file($language_file);
                }

                $variables = [];

                foreach ($translations as $language => $translation_values)
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

                    $this->write_line($file_handle, $row);
                }
            }
        }

        $this->getFilesystemTools()->sendFileForDownload($translations_file);
    }

    /**
     * Writes a line into a csv file
     *
     * @param Row $row - The row as array
     */
    private function write_line($file_handle, $row = [])
    {
        fputcsv($file_handle, $row, ';');
    }
}
