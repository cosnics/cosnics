<?php
namespace Chamilo\Core\Repository\ContentObject\File\Service;
use Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File;
use Chamilo\Core\User\Storage\DataClass\User;
use Exception;
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
     * @return File
     * @throws \Exception
     */
    public function handle(UploadedFile $uploadedFile, User $user)
    {
        $file = new File();
        $title = substr($uploadedFile->getClientOriginalName(), 0, -(strlen($uploadedFile->getClientOriginalExtension()) + 1));

        $file->set_title($title);
        $file->set_description($uploadedFile->getClientOriginalName());
        $file->set_owner_id($user->getId());
        $file->set_parent_id(0);
        $file->set_filename($uploadedFile->getClientOriginalName());

        $file->set_temporary_file_path($uploadedFile->getRealPath());

        if (!$file->create()) {
            throw new Exception("Could not create file");
        }

        return $file;
    }
}