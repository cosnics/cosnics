<?php
namespace Chamilo\Core\Admin\Language\Component;

use Chamilo\Configuration\Package\Service\PackageBundlesCacheService;
use Chamilo\Core\Admin\Language\Manager;
use League\Csv\ColumnConsistency;
use League\Csv\Writer;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Response;

class ExporterComponent extends Manager
{

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     * @throws \League\Csv\CannotInsertRecord
     * @throws \League\Csv\Exception
     */
    public function run()
    {
        ini_set('memory_limit', '-1');
        set_time_limit(0);

        $configurationConsulter = $this->getConfigurationConsulter();

        $validator = new ColumnConsistency();

        $csvWriter = Writer::createFromString();
        $csvWriter->addValidator($validator, 'column_consistency');

        $baseLanguage = $configurationConsulter->getSetting(['Chamilo\Core\Admin', 'base_language']);
        $sourceLanguage = $configurationConsulter->getSetting(['Chamilo\Core\Admin', 'source_language']);
        $targetLanguages =
            explode(',', $configurationConsulter->getSetting(['Chamilo\Core\Admin', 'target_languages']));

        $headers = ['package', 'variable', 'base_language', 'source_language'];

        foreach ($targetLanguages as $targetLanguage)
        {
            $headers[] = 'target_language_' . $targetLanguage;
        }

        $csvWriter->insertOne($headers);

        $languages = $this->determineLanguages($baseLanguage, $sourceLanguage, $targetLanguages);

        $packages = $this->getPackageBundlesCacheService()->getAllPackages()->getNestedPackages();

        foreach ($packages as $package)
        {
            $translations = [];

            $languagePath = $this->getSystemPathBuilder()->getI18nPath($package->get_context());

            foreach (array_unique($languages) as $language)
            {
                $languageFilePath = $languagePath . $language . '.i18n';

                if (file_exists($languageFilePath))
                {
                    $translations[$language] = parse_ini_file($languageFilePath);
                }
            }

            $variables = [];

            foreach ($translations as $languageTranslations)
            {
                $languageVariables = array_keys($languageTranslations);

                foreach ($languageVariables as $languageVariable)
                {
                    if (!in_array($languageVariable, $variables))
                    {
                        $variables[] = $languageVariable;
                    }
                }
            }

            foreach ($variables as $variable)
            {
                $row = [];

                $row[] = $package->get_context();
                $row[] = $variable;
                $row[] = $translations[$baseLanguage][$variable];
                $row[] = $translations[$sourceLanguage][$variable];

                foreach ($targetLanguages as $target_language)
                {
                    $row[] = $translations[$target_language][$variable];
                }

                $csvWriter->insertOne($row);
            }
        }

        $disposition = HeaderUtils::makeDisposition(
            HeaderUtils::DISPOSITION_ATTACHMENT, 'translations.csv'
        );

        $response = new Response(
            $csvWriter->toString(), 200, ['Content-Type' => 'text/csv', 'Content-Disposition' => $disposition]
        );

        $response->setCharset('utf-8');

        return $response;
    }

    /**
     * @param string[] $targetLanguages
     */
    public function determineLanguages(string $baseLanguage, string $sourceLanguage, array $targetLanguages): array
    {
        $languages = [$baseLanguage];

        if (!in_array($sourceLanguage, $languages))
        {
            $languages[] = $sourceLanguage;
        }

        foreach ($targetLanguages as $targetLanguage)
        {
            if (!in_array($targetLanguage, $languages))
            {
                $languages[] = $targetLanguage;
            }
        }

        return $languages;
    }

    public function getPackageBundlesCacheService(): PackageBundlesCacheService
    {
        return $this->getService(PackageBundlesCacheService::class);
    }
}
