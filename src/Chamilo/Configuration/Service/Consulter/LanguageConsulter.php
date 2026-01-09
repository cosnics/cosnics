<?php
namespace Chamilo\Configuration\Service\Consulter;

use Chamilo\Configuration\Service\DataLoader\LanguageCacheDataPreLoader;
use Chamilo\Libraries\File\FilesystemTools;
use Chamilo\Libraries\File\SystemPathBuilder;

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
