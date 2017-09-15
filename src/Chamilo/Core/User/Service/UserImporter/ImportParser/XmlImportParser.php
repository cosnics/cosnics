<?php
namespace Chamilo\Core\User\Service\UserImporter\ImportParser;

use Chamilo\Core\User\Domain\UserImporter\ImportUserData;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Parses an XML based user import file
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class XmlImportParser implements ImportParserInterface
{

    /**
     * Parses an upload file into
     *
     * @param UploadedFile $file
     *
     * @return ImportUserData[]
     */
    public function parse(UploadedFile $file)
    {
        // TODO: Implement parse() method.
    }

    /**
     * Checks whether or not the current parser can parse the given file
     *
     * @param UploadedFile $file
     *
     * @return bool
     */
    public function canParseFile(UploadedFile $file)
    {
        $allowedMimeTypes = ['text/xml'];

        return in_array($file->getClientMimeType(), $allowedMimeTypes);
    }
}