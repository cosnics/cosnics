<?php
namespace Chamilo\Core\Admin\Language\Component;

use Chamilo\Core\Admin\Language\Manager;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Platform\Configuration\PlatformSetting;

class ExporterComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        ini_set("memory_limit", "-1");
        set_time_limit(0);

        $translations_file = Path :: getInstance()->getTemporaryPath(__NAMESPACE__) . 'translations.csv';
        Filesystem :: create_dir(dirname($translations_file));

        $time_start = microtime(true);

        $file_handle = fopen($translations_file, 'w');

        $base_language = PlatformSetting :: get('base_language', __NAMESPACE__);
        $source_language = PlatformSetting :: get('source_language', __NAMESPACE__);
        $target_languages = PlatformSetting :: get('target_languages', __NAMESPACE__);

        $this->write_line(
            $file_handle,
            array('Package', 'Variable', 'Base language', 'Source language', 'Target Languages'));

        $language_values = array('', '', $base_language, $source_language);
        $target_languages = explode(',', $target_languages);
        foreach ($target_languages as $target_language)
        {
            $language_values[] = $target_language;
        }

        $languages = array_merge($target_languages, array($base_language, $source_language));

        $this->write_line($file_handle, $language_values);

        $package_list = \Chamilo\Configuration\Package\PlatformPackageBundles :: getInstance()->get_type_packages();

        foreach ($package_list as $packages)
        {
            foreach ($packages as $package)
            {
                $translations = array();
                $language_path = Path :: getInstance()->namespaceToFullPath($package) . 'resources/i18n/';

                foreach (array_unique($languages) as $language)
                {
                    $language_file = $language_path . $language . '.i18n';
                    $translations[$language] = parse_ini_file($language_file);
                }

                $variables = array();

                foreach ($translations as $language => $translation_values)
                {
                    $variables = array_merge($variables, array_keys($translation_values));
                }

                $variables = array_unique($variables);

                foreach ($variables as $variable)
                {
                    $row = array();
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

        $time_end = microtime(true);

        Filesystem :: file_send_for_download($translations_file, true);
    }

    /**
     * Writes a line into a csv file
     *
     * @param Row $row - The row as array
     */
    private function write_line($file_handle, $row = array())
    {
        fputcsv($file_handle, $row, ';', '"');
    }
}
