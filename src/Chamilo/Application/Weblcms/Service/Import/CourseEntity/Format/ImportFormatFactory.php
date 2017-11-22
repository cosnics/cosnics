<?php

namespace Chamilo\Application\Weblcms\Service\Import\CourseEntity\Format;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Factory class to determine the import format
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ImportFormatFactory
{

    /**
     * The import formats
     *
     * @var ImportFormatInterface[]
     */
    protected $importFormats;

    /**
     *
     * @param ImportFormatInterface[] $importFormats
     */
    public function __construct(array $importFormats)
    {
        $this->importFormats = $importFormats;
    }

    /**
     * Returns the import format for a given file
     *
     * @param UploadedFile $file
     *
     * @return ImportFormatInterface
     *
     * @throws \Exception
     */
    public function getImportFormatForFile(UploadedFile $file)
    {
        foreach ($this->importFormats as $importFormat)
        {
            if ($importFormat->canParseFile($file))
            {
                return $importFormat;
            }
        }

        throw new \Exception(
            sprintf('Importing with the given format %s is not supported', $file->getClientMimeType())
        );
    }
}