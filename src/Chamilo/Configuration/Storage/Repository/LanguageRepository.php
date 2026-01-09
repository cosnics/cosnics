<?php
namespace Chamilo\Configuration\Storage\Repository;

use Chamilo\Configuration\Storage\DataClass\Language;
use Chamilo\Libraries\File\FilesystemTools;
use Chamilo\Libraries\File\SystemPathBuilder;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Finder\Iterator\FileTypeFilterIterator;

/**
 * @package Chamilo\Configuration\Repository
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
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Configuration\Storage\DataClass\Language>
     */
    public function findLanguages(): ArrayCollection
    {
        $languagesPath = $this->getSystemPathBuilder()->namespaceToFullPath('Chamilo\Libraries') . 'Resources/I18n/';
        $languageFiles =
            $this->getFilesystemTools()->getDirectoryContent($languagesPath, FileTypeFilterIterator::ONLY_FILES, false);

        $languages = new ArrayCollection();

        foreach ($languageFiles as $languageFile)
        {
            if ($languageFile->getExtension() == 'json')
            {
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

    public function getFilesystemTools(): FilesystemTools
    {
        return $this->filesystemTools;
    }

    public function getSystemPathBuilder(): SystemPathBuilder
    {
        return $this->systemPathBuilder;
    }
}