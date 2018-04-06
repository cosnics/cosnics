<?php

namespace Chamilo\Libraries\File\Compression\ArchiveCreator;

use Chamilo\Libraries\File\Compression\Filecompression;
use Chamilo\Libraries\File\ConfigurablePathBuilder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * @package Chamilo\Libraries\File\Compression\ArchiveCreator
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ArchiveCreator
{
    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    protected $fileSystem;

    /**
     * @var \Chamilo\Libraries\File\Compression\Filecompression
     */
    protected $fileCompression;

    /**
     * @var \Chamilo\Libraries\File\ConfigurablePathBuilder
     */
    protected $configurablePathBuilder;

    /**
     * ArchiveCreator constructor.
     *
     * @param \Symfony\Component\Filesystem\Filesystem $fileSystem
     * @param \Chamilo\Libraries\File\Compression\Filecompression $fileCompression
     * @param \Chamilo\Libraries\File\ConfigurablePathBuilder $configurablePathBuilder
     */
    public function __construct(
        Filesystem $fileSystem, Filecompression $fileCompression, ConfigurablePathBuilder $configurablePathBuilder
    )
    {
        $this->fileSystem = $fileSystem;
        $this->fileCompression = $fileCompression;
        $this->configurablePathBuilder = $configurablePathBuilder;
    }

    /**
     * Makes an actual zipped file from a given archive and returns the path to the archive
     *
     * @param \Chamilo\Libraries\File\Compression\ArchiveCreator\Archive $archive
     *
     * @return string
     */
    public function createArchive(Archive $archive)
    {
        $temporaryFolder = $this->configurablePathBuilder->getTemporaryPath(__NAMESPACE__) .
            DIRECTORY_SEPARATOR . uniqid();

        foreach ($archive->getArchiveItems() as $archiveItem)
        {
            $this->handleArchiveItem($archiveItem, $temporaryFolder);
        }

        $archivePath = $this->fileCompression->create_archive($temporaryFolder);
        $this->fileSystem->remove([$temporaryFolder]);

        return $archivePath;
    }

    /**
     * Makes an actual zipped file from a given archive and embeds the archive in a binary response
     *
     * @param \Chamilo\Libraries\File\Compression\ArchiveCreator\Archive $archive
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function createArchiveWithDownloadResponse(Archive $archive)
    {
        $archivePath = $this->createArchive($archive);

        $response = new BinaryFileResponse($archivePath, 200, array('Content-Type' => 'application/zip'));

        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT, $archive->getName() . '.zip',
            \Chamilo\Libraries\File\Filesystem::create_safe_name($archive->getName()) . '.zip'
        );

        return $response;
    }

    /**
     * @param \Chamilo\Libraries\File\Compression\ArchiveCreator\Archive $archive
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function createAndDownloadArchive(Archive $archive, Request $request)
    {
        $downloadResponse = $this->createArchiveWithDownloadResponse($archive);
        $downloadResponse->prepare($request);

        $downloadResponse->send();

        $this->removeArchiveAfterDownload($downloadResponse);
    }

    /**
     * Removes the archive path after downloading the
     * @param \Symfony\Component\HttpFoundation\BinaryFileResponse $binaryFileResponse
     */
    public function removeArchiveAfterDownload(BinaryFileResponse $binaryFileResponse)
    {
        $archivePath = $binaryFileResponse->getFile()->getPathname();
        $this->fileSystem->remove([$archivePath]);
    }

    /**
     * @param \Chamilo\Libraries\File\Compression\ArchiveCreator\ArchiveItem[] $archiveItems
     * @param string $temporaryFolder
     */
    protected function handleArchiveItems($archiveItems = [], $temporaryFolder)
    {
        foreach ($archiveItems as $archiveItem)
        {
            $this->handleArchiveItem($archiveItem, $temporaryFolder);
        }
    }

    /**
     * @param \Chamilo\Libraries\File\Compression\ArchiveCreator\ArchiveItem $archiveItem
     * @param string $temporaryPath
     */
    protected function handleArchiveItem(ArchiveItem $archiveItem, $temporaryPath)
    {
        if ($archiveItem instanceof ArchiveFolder)
        {
            $this->handleArchiveFolder($archiveItem, $temporaryPath);

            return;
        }

        /** @var ArchiveFile $archiveItem */
        $this->handleArchiveFile($archiveItem, $temporaryPath);
    }

    /**
     * @param \Chamilo\Libraries\File\Compression\ArchiveCreator\ArchiveFolder $archiveFolder
     * @param string $temporaryPath
     */
    protected function handleArchiveFolder(ArchiveFolder $archiveFolder, $temporaryPath)
    {
        $folderPath = $temporaryPath . DIRECTORY_SEPARATOR . $archiveFolder->getName();
        $this->fileSystem->mkdir($folderPath);

        foreach ($archiveFolder->getArchiveItems() as $archiveItem)
        {
            $this->handleArchiveItem($archiveItem, $folderPath);
        }
    }

    /**
     * @param \Chamilo\Libraries\File\Compression\ArchiveCreator\ArchiveFile $archiveFile
     * @param string $temporaryPath
     */
    protected function handleArchiveFile(ArchiveFile $archiveFile, $temporaryPath)
    {
        $filePath = $temporaryPath . DIRECTORY_SEPARATOR . $archiveFile->getName();
        $originalPath = $archiveFile->getOriginalPath();

        if (is_dir($originalPath))
        {
            $this->fileSystem->mirror($originalPath, $filePath);
        }
        else
        {
            $this->fileSystem->copy($originalPath, $filePath);
        }
    }
}