<?php
namespace Chamilo\Libraries\File\Export;

use Chamilo\Libraries\File\ConfigurablePathBuilder;
use Chamilo\Libraries\File\FilesystemTools;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * @package Chamilo\Libraries\File\Export
 */
abstract class Export
{
    protected ConfigurablePathBuilder $configurablePathBuilder;

    protected Filesystem $filesystem;

    protected FilesystemTools $filesystemTools;

    public function __construct(
        ConfigurablePathBuilder $configurablePathBuilder, Filesystem $filesystem, FilesystemTools $filesystemTools
    )
    {
        $this->configurablePathBuilder = $configurablePathBuilder;
        $this->filesystem = $filesystem;
        $this->filesystemTools = $filesystemTools;
    }

    public function getConfigurablePathBuilder(): ConfigurablePathBuilder
    {
        return $this->configurablePathBuilder;
    }

    public function getFilesystem(): Filesystem
    {
        return $this->filesystem;
    }

    public function getFilesystemTools(): FilesystemTools
    {
        return $this->filesystemTools;
    }

    public function sendtoBrowser(string $fileName, array $data, ?string $path = null): void
    {
        $file = $this->writeToFile($fileName, $data, $path);

        if ($file)
        {
            $fileResponse = new BinaryFileResponse($file);
            $fileResponse->send();

            $this->getFilesystem()->remove($file);
            exit;
        }
    }

    abstract public function serializeData($data): string;

    public function writeToFile(string $fileName, array $data, ?string $path = null): string
    {
        if (!$path)
        {
            $path = $this->getConfigurablePathBuilder()->getArchivePath();
        }

        $file = $path . $this->getFilesystemTools()->createUniqueName($path, $fileName);

        $this->getFilesystem()->dumpFile($file, $this->serializeData($data));

        return $file;
    }
}
