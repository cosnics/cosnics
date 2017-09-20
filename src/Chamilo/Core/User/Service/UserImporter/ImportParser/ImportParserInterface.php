<?php

namespace Chamilo\Core\User\Service\UserImporter\ImportParser;

use Chamilo\Core\User\Domain\UserImporter\ImportUserData;
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
     * Parses an upload file into
     *
     * @param UploadedFile $file
     * @param UserImporterResult $userImporterResult
     *
     * @return ImportUserData[]
     */
    public function parse(UploadedFile $file, UserImporterResult $userImporterResult);

    /**
     * Checks whether or not the current parser can parse the given file
     *
     * @param UploadedFile $file
     *
     * @return bool
     */
    public function canParseFile(UploadedFile $file);
}