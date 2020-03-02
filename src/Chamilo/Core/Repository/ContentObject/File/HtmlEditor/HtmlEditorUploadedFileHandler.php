<?php
namespace Chamilo\Core\Repository\ContentObject\File\HtmlEditor;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Core\Repository\ContentObject\File\Service\UploadedFileHandler;
use Chamilo\Core\Repository\DTO\HtmlEditorContentObjectPlaceholder;
use Chamilo\Core\User\Storage\DataClass\User;
use Exception;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class HtmlEditorUploadedFileHandler
 *
 * @author pjbro <pjbro@users.noreply.github.com>
 */
class HtmlEditorUploadedFileHandler
{

    /**
     *
     * @param UploadedFile $uploadedFile
     *
     * @return bool
     */
    public function canHandleUploadedFile(UploadedFile $uploadedFile)
    {
        return true; // can handle all files
    }

    /**
     *
     * @param $fileContentObject
     *
     * @return string
     */
    protected function getThumbnailUrl($fileContentObject)
    {
        try
        {
            $display = ContentObjectRenditionImplementation::factory($fileContentObject, 'json', 'image', $this);

            $rendition = $display->render();
        }
        catch (Exception $ex)
        {
            $rendition = array('url' => null);
        }

        return $rendition['url'];
    }

    /**
     *
     * @param UploadedFile $uploadedFile
     * @param User $user
     *
     * @return HtmlEditorContentObjectPlaceholder
     * @throws \Exception
     */
    public function handle(UploadedFile $uploadedFile, User $user)
    {
        $uploadedFileHandler = new UploadedFileHandler();
        $file = $uploadedFileHandler->handle($uploadedFile, $user);

        return new HtmlEditorContentObjectPlaceholder(
            $file->get_filename(), $file->getId(), $file->calculate_security_code(),
            $file->is_image() ? 'image' : 'file', $this->getThumbnailUrl($file)
        );
    }
}