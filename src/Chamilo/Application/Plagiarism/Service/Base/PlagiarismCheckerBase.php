<?php

namespace Chamilo\Application\Plagiarism\Service\Base;

use Chamilo\Application\Plagiarism\Domain\SubmissionStatus;
use Chamilo\Application\Plagiarism\Service\PlagiarismCheckerInterface;
use Chamilo\Core\User\Storage\DataClass\User;

abstract class PlagiarismCheckerBase implements PlagiarismCheckerInterface
{
    const MAX_ALLOWED_FILE_SIZE = 100 * 1024 * 1024;
    
    public function canCheckForPlagiarism(string $filePath, string $filename)
    {
        return !$this->isInMaintenanceMode() &&
            $this->canUploadFile($filePath, $filename);
    }

    public function isInMaintenanceMode()
    {
        return $this->configurationConsulter->getSetting(['Chamilo\Application\Plagiarism', 'maintenance_mode']) == 1;
    }

    public function canUploadFile(string $filePath, string $filename)
    {
        if (!$this->isPlagiarismCheckerActive())
        {
            return false;
        }

        if (!file_exists($filePath))
        {
            return false;
        }

        $fileParts = explode('.', $filename);
        $extension = array_pop($fileParts);
        if (!in_array($extension, $this->getAllowedFileExtensions()))
        {
            return false;
        }

        $fileSize = filesize($filePath);
        if($fileSize > static::MAX_ALLOWED_FILE_SIZE)
        {
            return false;
        }

        return true;
    }

    protected function getAllowedFileExtensions()
    {
        return ['doc', 'txt', 'rtf', 'sxw', 'odt', 'pdf', 'html', 'htm', 'docx', 'wpd'];
    }

}