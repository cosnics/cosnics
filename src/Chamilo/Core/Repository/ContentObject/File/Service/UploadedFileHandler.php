<?php

namespace Chamilo\Core\Repository\ContentObject\File\Service;

use Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File;
use Chamilo\Core\User\Storage\DataClass\User;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class UploadedFileHandler
 *
 * @author pjbro <pjbro@users.noreply.github.com>
 */
class UploadedFileHandler
{
    /**
     * @param UploadedFile $uploadedFile
     * @param User $user
     *
     * @return File
     * @throws \Exception
     */
    public function handle(UploadedFile $uploadedFile, User $user)
    {
        $file = File::fromUploadedFile($user, $uploadedFile);

        if (!$file->create())
        {
            throw new \Exception("Could not create file");
        }

        return $file;
    }
}
