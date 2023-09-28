<?php
namespace Chamilo\Core\User\Service\UserImporter\ImportParser;

use Chamilo\Core\User\Domain\UserImporter\UserImporterResult;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Interface for import parsers
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
interface ImportParserInterface
{

    /**
     * Checks whether or not the current parser can parse the given file
     */
    public function canParseFile(UploadedFile $file): bool;

    /**
     * Parses an upload file into
     *
     * @return \Chamilo\Core\User\Domain\UserImporter\ImportUserData[]
     */
    public function parse(UploadedFile $file, UserImporterResult $userImporterResult): array;
}