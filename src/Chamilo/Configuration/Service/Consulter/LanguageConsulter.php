<?php
namespace Chamilo\Configuration\Service\Consulter;

use Chamilo\Configuration\Service\DataLoader\LanguageCacheDataPreLoader;
use Chamilo\Configuration\Storage\DataClass\Language;
use Chamilo\Libraries\File\FilesystemTools;
use Chamilo\Libraries\File\SystemPathBuilder;
use DOMDocument;
use DOMXPath;
use Symfony\Component\Finder\Iterator\FileTypeFilterIterator;

/**
 * @package Chamilo\Configuration\Service\Consulter
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
class LanguageConsulter
{
    protected FilesystemTools $filesystemTools;

    protected LanguageCacheDataPreLoader $languageCacheDataPreLoader;

    protected SystemPathBuilder $systemPathBuilder;

    public function __construct(
        SystemPathBuilder $systemPathBuilder, FilesystemTools $filesystemTools,
        LanguageCacheDataPreLoader $languageCacheDataPreLoader
    )
    {
        $this->systemPathBuilder = $systemPathBuilder;
        $this->filesystemTools = $filesystemTools;
        $this->languageCacheDataPreLoader = $languageCacheDataPreLoader;
    }

    public function getFilesystemTools(): FilesystemTools
    {
        return $this->filesystemTools;
    }

    public function getLanguageCacheDataPreLoader(): LanguageCacheDataPreLoader
    {
        return $this->languageCacheDataPreLoader;
    }

    public function getLanguageNameFromIsocode(string $isocode): string
    {
        $languages = $this->getLanguages();

        return $languages[$isocode];
    }

    /**
     * @return string[]
     */
    public function getLanguages(): array
    {
        return $this->getLanguageCacheDataPreLoader()->getLanguages();
    }

    public function getLanguagesFromFilesystem(): array
    {
        $languagePath = $this->getSystemPathBuilder()->namespaceToFullPath('Chamilo\Configuration') . 'Resources/I18n/';
        $languageFiles =
            $this->getFilesystemTools()->getDirectoryContent($languagePath, FileTypeFilterIterator::ONLY_FILES, false);

        $languageList = [];

        foreach ($languageFiles as $language_file)
        {
            $fileInfo = pathinfo($language_file);
            $languageInfoFile = $languagePath . $fileInfo['filename'] . '.info';

            if (file_exists($languageInfoFile))
            {
                $domDocument = new DOMDocument('1.0', 'UTF-8');
                $domDocument->load($languageInfoFile);
                $domXpath = new DOMXPath($domDocument);

                $languageNode = $domXpath->query('/packages/package')->item(0);

                $language = [];

                $language[Language::PROPERTY_ORIGINAL_NAME] =
                    $domXpath->query('name', $languageNode)->item(0)->nodeValue;
                $language[Language::PROPERTY_ENGLISH_NAME] =
                    $domXpath->query('extra/english', $languageNode)->item(0)->nodeValue;
                $language[Language::PROPERTY_FAMILY] = $domXpath->query('category', $languageNode)->item(0)->nodeValue;
                $language[Language::PROPERTY_ISOCODE] =
                    $domXpath->query('extra/isocode', $languageNode)->item(0)->nodeValue;

                $languageList[$language[Language::PROPERTY_ISOCODE]] = $language;
            }
        }

        return $languageList;
    }

    /**
     * @param string $isocodeToExclude
     *
     * @return string[]
     */
    public function getOtherLanguages(string $isocodeToExclude): array
    {
        $languages = [];

        foreach ($this->getLanguages() as $isocode => $language)
        {
            if ($isocode !== $isocodeToExclude)
            {
                $languages[$isocode] = $language;
            }
        }

        return $languages;
    }

    public function getSystemPathBuilder(): SystemPathBuilder
    {
        return $this->systemPathBuilder;
    }
}
