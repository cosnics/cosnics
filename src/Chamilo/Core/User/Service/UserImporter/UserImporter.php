<?php

namespace Chamilo\Core\User\Service\UserImporter;

use Chamilo\Core\User\Service\UserImporter\ImportParser\ImportParserFactory;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Imports users from a given uploaded file
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class UserImporter
{
    /**
     * @var ImportParserFactory
     */
    protected $userImportParserFactory;

    /**
     * UserImporter constructor.
     *
     * @param ImportParserFactory $userImportParserFactory
     */
    public function __construct(ImportParserFactory $userImportParserFactory)
    {
        $this->userImportParserFactory = $userImportParserFactory;
    }

    /**
     * Imports users from a given uploaded file
     *
     * @param UploadedFile $file
     */
    public function importUsersFromFile(UploadedFile $file)
    {
        $importParser = $this->userImportParserFactory->getImportParserForUploadedFile($file);
        $importUserData = $importParser->parse($file);
    }
}