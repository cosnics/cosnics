<?php
namespace Chamilo\Core\Admin\Service\Consulter;

use Chamilo\Core\Admin\Storage\Repository\LanguageRepository;
use Chamilo\Libraries\File\FilesystemTools;
use Chamilo\Libraries\File\SystemPathBuilder;

/**
 * @package Chamilo\Core\Admin\Service\Consulter
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
class LanguageConsulter
{
    protected FilesystemTools $filesystemTools;

    protected LanguageRepository $languageRepository;

    protected SystemPathBuilder $systemPathBuilder;

    public function __construct(
        SystemPathBuilder $systemPathBuilder, FilesystemTools $filesystemTools, LanguageRepository $languageRepository
    )
    {
        $this->systemPathBuilder = $systemPathBuilder;
        $this->filesystemTools = $filesystemTools;
        $this->languageRepository = $languageRepository;
    }

    public function getFilesystemTools(): FilesystemTools
    {
        return $this->filesystemTools;
    }

    public function getLanguageNameFromIsocode(string $isocode): string
    {
        $languages = $this->getLanguages();

        return $languages[$isocode];
    }

    public function getLanguageRepository(): LanguageRepository
    {
        return $this->languageRepository;
    }

    /**
     * @return string[]
     */
    public function getLanguages(): array
    {
        return $this->getLanguageRepository()->findLanguagesAsArray();
    }

    /**
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
