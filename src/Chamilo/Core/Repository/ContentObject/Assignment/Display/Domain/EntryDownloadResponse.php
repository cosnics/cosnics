<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Domain;

use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Domain
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class EntryDownloadResponse extends BinaryFileResponse
{
    /**
     * @var bool
     */
    protected $removeFileAfterDownload;

    /**
     * @param \SplFileInfo|string $file The file to stream
     * @param int $status The response status code
     * @param array $headers An array of response headers
     * @param bool $public Files are public by default
     * @param null|string $contentDisposition The type of Content-Disposition to set automatically with the filename
     * @param bool $autoEtag Whether the ETag header should be automatically set
     * @param bool $autoLastModified Whether the Last-Modified header should be automatically set
     * @param bool $removeFileAfterDownload
     */
    public function __construct(
        $file, $status = 200, $headers = array(), $public = true, $contentDisposition = null, $autoEtag = false,
        $autoLastModified = true, $removeFileAfterDownload = true
    )
    {
        parent::__construct($file, $status, $headers, $public, $contentDisposition, $autoEtag, $autoLastModified);

        $this->removeFileAfterDownload = $removeFileAfterDownload;
    }

    /**
     * @return bool
     */
    public function removeFileAfterDownload(): bool
    {
        return $this->removeFileAfterDownload;
    }

    /**
     * @param bool $removedFileAfterDownload
     */
    public function setRemoveFileAfterDownload(bool $removedFileAfterDownload)
    {
        $this->removeFileAfterDownload = $removedFileAfterDownload;
    }
}