<?php
namespace Chamilo\Libraries\Filesystem\Service\Compression;

use Chamilo\Libraries\Filesystem\Architecture\Domain\ArchiveCreator\Archive;
use Chamilo\Libraries\Filesystem\Architecture\Domain\ArchiveCreator\ArchiveFile;
use Chamilo\Libraries\Filesystem\Architecture\Domain\ArchiveCreator\ArchiveFolder;
use Chamilo\Libraries\Filesystem\Architecture\Domain\ArchiveCreator\ArchiveItem;
use Chamilo\Libraries\Filesystem\Service\ConfigurablePathBuilder;
use Chamilo\Libraries\Filesystem\Service\FilesystemTools;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * @package Chamilo\Libraries\Filesystem\Service\Compression
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ArchiveCreator
{
    public function __construct(
        protected Filesystem $filesystem, protected FilesystemTools $filesystemTools,
        protected ZipArchiveFilecompression $fileCompression, protected ConfigurablePathBuilder $configurablePathBuilder
    )
    {
    }

    public function createAndDownloadArchive(Archive $archive, Request $request): static
    {
        $downloadResponse = $this->createArchiveWithDownloadResponse($archive);
        $downloadResponse->prepare($request);
        $downloadResponse->send();

        $this->removeArchiveAfterDownload($downloadResponse);

        return $this;
    }

    public function createArchive(Archive $archive): string
    {
        $temporaryFolder =
            $this->configurablePathBuilder->getTemporaryPath(__NAMESPACE__) . DIRECTORY_SEPARATOR . uniqid();

        $this->handleArchiveItems($archive->getArchiveItems(), $temporaryFolder);

        $archivePath = $this->fileCompression->createArchive($temporaryFolder);
        $this->filesystem->remove([$temporaryFolder]);

        return $archivePath;
    }

    public function createArchiveWithDownloadResponse(Archive $archive): BinaryFileResponse
    {
        $archivePath = $this->createArchive($archive);

        $response = new BinaryFileResponse($archivePath, 200, ['Content-Type' => 'application/zip']);

        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT, $archive->getName() . '.zip',
            $this->filesystemTools->createSafeName($archive->getName()) . '.zip'
        );

        return $response;
    }

    protected function handleArchiveFile(ArchiveFile $archiveFile, string $temporaryPath): static
    {
        $fileName = $this->filesystemTools->createUniqueName($temporaryPath, $archiveFile->getName());
        $filePath = $temporaryPath . DIRECTORY_SEPARATOR . $fileName;
        $originalPath = $archiveFile->getOriginalPath();

        if (is_dir($originalPath)) {
            $this->filesystem->mirror($originalPath, $filePath);
        }
        else {
            $this->filesystem->copy($originalPath, $filePath);
        }

        return $this;
    }

    protected function handleArchiveFolder(ArchiveFolder $archiveFolder, string $temporaryPath): static
    {
        $folderName = $this->filesystemTools->createUniqueName($temporaryPath, $archiveFolder->getName());
        $folderPath = $temporaryPath . DIRECTORY_SEPARATOR . $folderName;
        $this->filesystem->mkdir($folderPath);

        foreach ($archiveFolder->getArchiveItems() as $archiveItem) {
            $this->handleArchiveItem($archiveItem, $folderPath);
        }

        return $this;
    }

    protected function handleArchiveItem(ArchiveItem $archiveItem, string $temporaryPath): static
    {
        if ($archiveItem instanceof ArchiveFolder) {
            $this->handleArchiveFolder($archiveItem, $temporaryPath);

            return $this;
        }

        /** @var ArchiveFile $archiveItem */
        $this->handleArchiveFile($archiveItem, $temporaryPath);

        return $this;
    }

    /**
     * @param \Chamilo\Libraries\Filesystem\Architecture\Domain\ArchiveCreator\ArchiveItem[] $archiveItems
     */
    protected function handleArchiveItems(array $archiveItems, string $temporaryFolder): static
    {
        foreach ($archiveItems as $archiveItem) {
            $this->handleArchiveItem($archiveItem, $temporaryFolder);
        }

        return $this;
    }

    public function removeArchiveAfterDownload(BinaryFileResponse $binaryFileResponse): static
    {
        $archivePath = $binaryFileResponse->getFile()->getPathname();
        $this->filesystem->remove([$archivePath]);

        return $this;
    }
}