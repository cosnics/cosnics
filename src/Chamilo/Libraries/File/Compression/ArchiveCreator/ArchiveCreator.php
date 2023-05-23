<?php
namespace Chamilo\Libraries\File\Compression\ArchiveCreator;

use Chamilo\Libraries\File\Compression\ZipArchive\ZipArchiveFilecompression;
use Chamilo\Libraries\File\ConfigurablePathBuilder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * @package Chamilo\Libraries\File\Compression\ArchiveCreator
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ArchiveCreator
{
    protected ConfigurablePathBuilder $configurablePathBuilder;

    protected ZipArchiveFilecompression $fileCompression;

    protected Filesystem $fileSystem;

    public function __construct(
        Filesystem $fileSystem, ZipArchiveFilecompression $fileCompression,
        ConfigurablePathBuilder $configurablePathBuilder
    )
    {
        $this->fileSystem = $fileSystem;
        $this->fileCompression = $fileCompression;
        $this->configurablePathBuilder = $configurablePathBuilder;
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
     * Makes an actual zipped file from a given archive and returns the path to the archive
     *
     * @param \Chamilo\Libraries\File\Compression\ArchiveCreator\Archive $archive
     *
     * @return string
     */
    public function createArchive(Archive $archive)
    {
        $temporaryFolder =
            $this->configurablePathBuilder->getTemporaryPath(__NAMESPACE__) . DIRECTORY_SEPARATOR . uniqid();

        $this->handleArchiveItems($archive->getArchiveItems(), $temporaryFolder);

        $archivePath = $this->fileCompression->createArchive($temporaryFolder);
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

        $response = new BinaryFileResponse($archivePath, 200, ['Content-Type' => 'application/zip']);

        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT, $archive->getName() . '.zip',
            \Chamilo\Libraries\File\Filesystem::create_safe_name($archive->getName()) . '.zip'
        );

        return $response;
    }

    /**
     * @param \Chamilo\Libraries\File\Compression\ArchiveCreator\ArchiveFile $archiveFile
     * @param string $temporaryPath
     */
    protected function handleArchiveFile(ArchiveFile $archiveFile, $temporaryPath)
    {
        $fileName = \Chamilo\Libraries\File\Filesystem::create_unique_name($temporaryPath, $archiveFile->getName());
        $filePath = $temporaryPath . DIRECTORY_SEPARATOR . $fileName;
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

    /**
     * @param \Chamilo\Libraries\File\Compression\ArchiveCreator\ArchiveFolder $archiveFolder
     * @param string $temporaryPath
     */
    protected function handleArchiveFolder(ArchiveFolder $archiveFolder, $temporaryPath)
    {
        $folderName = \Chamilo\Libraries\File\Filesystem::create_unique_name($temporaryPath, $archiveFolder->getName());
        $folderPath = $temporaryPath . DIRECTORY_SEPARATOR . $folderName;
        $this->fileSystem->mkdir($folderPath);

        foreach ($archiveFolder->getArchiveItems() as $archiveItem)
        {
            $this->handleArchiveItem($archiveItem, $folderPath);
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
     * @param \Chamilo\Libraries\File\Compression\ArchiveCreator\ArchiveItem[] $archiveItems
     * @param string $temporaryFolder
     */
    protected function handleArchiveItems(array $archiveItems, $temporaryFolder)
    {
        foreach ($archiveItems as $archiveItem)
        {
            $this->handleArchiveItem($archiveItem, $temporaryFolder);
        }
    }

    /**
     * Removes the archive path after downloading the
     *
     * @param \Symfony\Component\HttpFoundation\BinaryFileResponse $binaryFileResponse
     */
    public function removeArchiveAfterDownload(BinaryFileResponse $binaryFileResponse)
    {
        $archivePath = $binaryFileResponse->getFile()->getPathname();
        $this->fileSystem->remove([$archivePath]);
    }
}