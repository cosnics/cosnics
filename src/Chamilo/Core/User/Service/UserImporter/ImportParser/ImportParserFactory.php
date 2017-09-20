<?php

namespace Chamilo\Core\User\Service\UserImporter\ImportParser;

use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Factory to determine the correct import parser for the given uploaded file
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ImportParserFactory
{
    /**
     * @var ImportParserInterface[]
     */
    protected $importParsers;

    /**
     * ImportParserFactory constructor.
     */
    public function __construct()
    {
        $this->importParsers = [
            new CsvImportParser(StringUtilities::getInstance())
        ];
    }

    /**
     * Returns the import parser for the given uploaded file
     *
     * @param UploadedFile $uploadedFile
     *
     * @return ImportParserInterface
     */
    public function getImportParserForUploadedFile(UploadedFile $uploadedFile)
    {
        foreach($this->importParsers as $importParser)
        {
            if($importParser->canParseFile($uploadedFile))
            {
                return $importParser;
            }
        }

        throw new \RuntimeException(
            'No import parser found for uploaded file ' . $uploadedFile->getClientOriginalName()
        );
    }
}