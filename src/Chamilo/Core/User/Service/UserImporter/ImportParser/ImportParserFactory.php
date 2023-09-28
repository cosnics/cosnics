<?php
namespace Chamilo\Core\User\Service\UserImporter\ImportParser;

use Chamilo\Libraries\Utilities\StringUtilities;
use RuntimeException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Factory to determine the correct import parser for the given uploaded file
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ImportParserFactory
{
    /**
     * @var \Chamilo\Core\User\Service\UserImporter\ImportParser\ImportParserInterface[]
     */
    protected array $importParsers;

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
     */
    public function getImportParserForUploadedFile(UploadedFile $uploadedFile): ImportParserInterface
    {
        foreach ($this->importParsers as $importParser)
        {
            if ($importParser->canParseFile($uploadedFile))
            {
                return $importParser;
            }
        }

        throw new RuntimeException(
            'No import parser found for uploaded file ' . $uploadedFile->getClientOriginalName()
        );
    }
}