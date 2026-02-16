<?php
namespace Chamilo\Core\Admin\Storage\Repository;

use Chamilo\Core\Admin\Architecture\Domain\Language;
use Chamilo\Core\Admin\Architecture\Domain\LanguageCodeEnum;
use Chamilo\Libraries\Filesystem\Service\FilesystemTools;
use Chamilo\Libraries\Filesystem\Service\SystemPathBuilder;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Finder\Iterator\FileTypeFilterIterator;

/**
 * @package Chamilo\Core\Admin\Storage\Repository
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
class LanguageRepository
{
    protected FilesystemTools $filesystemTools;

    protected SystemPathBuilder $systemPathBuilder;

    public function __construct(SystemPathBuilder $systemPathBuilder, FilesystemTools $filesystemTools)
    {
        $this->systemPathBuilder = $systemPathBuilder;
        $this->filesystemTools = $filesystemTools;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Admin\Architecture\Domain\Language>
     */
    public function findLanguages(): ArrayCollection
    {
        $languagesPath = $this->getSystemPathBuilder()->getTranslationPath();
        $languageFiles =
            $this->getFilesystemTools()->getDirectoryContent($languagesPath, FileTypeFilterIterator::ONLY_FILES, false);

        $languages = new ArrayCollection();

        foreach ($languageFiles as $languageFile) {
            if ($languageFile->getExtension() == 'json') {
                $languageValues = json_decode(file_get_contents($languageFile->getPathname()), true);

                $languages->add(
                    new Language(
                        $languageValues['codes'], $languageValues['families'], $languageValues['name'],
                        $languageValues['translations']
                    )
                );
            }
        }

        return $languages;
    }

    public function findLanguagesAsArray(): array
    {
        $languageValues = [];
        $languages = $this->findLanguages();

        foreach ($languages as $language) {
            $languageValues[$language->getCode(LanguageCodeEnum::ISO_639_1)] = $language->getName();
        }

        return $languageValues;
    }

    public function getFilesystemTools(): FilesystemTools
    {
        return $this->filesystemTools;
    }

    public function getSystemPathBuilder(): SystemPathBuilder
    {
        return $this->systemPathBuilder;
    }
}