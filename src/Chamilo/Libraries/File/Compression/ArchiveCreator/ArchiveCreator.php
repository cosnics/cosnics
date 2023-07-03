<?php
namespace Chamilo\Libraries\File\Compression\ArchiveCreator;

use Chamilo\Libraries\File\Compression\ZipArchive\ZipArchiveFilecompression;
use Chamilo\Libraries\File\ConfigurablePathBuilder;
use Chamilo\Libraries\File\FilesystemTools;
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

    protected Filesystem $filesystem;

    protected FilesystemTools $filesystemTools;

    public function __construct(
        Filesystem $filesystem, FilesystemTools $filesystemTools, ZipArchiveFilecompression $fileCompression,
        ConfigurablePathBuilder $configurablePathBuilder
    )
    {
        $this->filesystem = $filesystem;
        $this->filesystemTools = $filesystemTools;
        $this->fileCompression = $fileCompression;
        $this->configurablePathBuilder = $configurablePathBuilder;
    }

    public function createAndDownloadArchive(Archive $archive, Request $request)
    {
        $downloadResponse = $this->createArchiveWithDownloadResponse($archive);
        $downloadResponse->prepare($request);

        $downloadResponse->send();

        $this->removeArchiveAfterDownload($downloadResponse);
    }

    /**
     * Makes an actual zipped file from a given archive and returns the path to the archive
     */
    public function createArchive(Archive $archive): string
    {
        $temporaryFolder =
            $this->configurablePathBuilder->getTemporaryPath(__NAMESPACE__) . DIRECTORY_SEPARATOR . uniqid();

        $this->handleArchiveItems($archive->getArchiveItems(), $temporaryFolder);

        $archivePath = $this->fileCompression->createArchive($temporaryFolder);
        $this->filesystem->remove([$temporaryFolder]);

        return $archivePath;
    }

    /**
     * Makes an actual zipped file from a given archive and embeds the archive in a binary response
     */
    public function createArchiveWithDownloadResponse(Archive $archive): BinaryFileResponse
    {
        $archivePath = $this->createArchive($archive);

        $response = new BinaryFileResponse($archivePath, 200, ['Content-Type' => 'application/zip']);

        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT, $archive->getName() . '.zip',
            $this->getFilesystemTools()->createSafeName($archive->getName()) . '.zip'
        );

        return $response;
    }

    public function getFilesystemTools(): FilesystemTools
    {
        return $this->filesystemTools;
    }

    protected function handleArchiveFile(ArchiveFile $archiveFile, string $temporaryPath)
    {
        $fileName = $this->getFilesystemTools()->createUniqueName($temporaryPath, $archiveFile->getName());
        $filePath = $temporaryPath . DIRECTORY_SEPARATOR . $fileName;
        $originalPath = $archiveFile->getOriginalPath();

        if (is_dir($originalPath))
        {
            $this->filesystem->mirror($originalPath, $filePath);
        }
        else
        {
            $this->filesystem->copy($originalPath, $filePath);
        }
    }

    protected function handleArchiveFolder(ArchiveFolder $archiveFolder, string $temporaryPath)
    {
        $folderName = $this->getFilesystemTools()->createUniqueName($temporaryPath, $archiveFolder->getName());
        $folderPath = $temporaryPath . DIRECTORY_SEPARATOR . $folderName;
        $this->filesystem->mkdir($folderPath);

        foreach ($archiveFolder->getArchiveItems() as $archiveItem)
        {
            $this->handleArchiveItem($archiveItem, $folderPath);
        }
    }

    protected function handleArchiveItem(ArchiveItem $archiveItem, string $temporaryPath)
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
     */
    protected function handleArchiveItems(array $archiveItems, string $temporaryFolder)
    {
        foreach ($archiveItems as $archiveItem)
        {
            $this->handleArchiveItem($archiveItem, $temporaryFolder);
        }
    }

    /**
     * Removes the archive path after downloading the
     */
    public function removeArchiveAfterDownload(BinaryFileResponse $binaryFileResponse)
    {
        $archivePath = $binaryFileResponse->getFile()->getPathname();
        $this->filesystem->remove([$archivePath]);
    }
}