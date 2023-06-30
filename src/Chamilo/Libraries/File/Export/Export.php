<?php
namespace Chamilo\Libraries\File\Export;

use Chamilo\Libraries\File\ConfigurablePathBuilder;
use Chamilo\Libraries\File\Filesystem;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * @package Chamilo\Libraries\File\Export
 */
abstract class Export
{
    protected ConfigurablePathBuilder $configurablePathBuilder;

    protected \Symfony\Component\Filesystem\Filesystem $filesystem;

    public function __construct(
        ConfigurablePathBuilder $configurablePathBuilder, \Symfony\Component\Filesystem\Filesystem $filesystem
    )
    {
        $this->configurablePathBuilder = $configurablePathBuilder;
        $this->filesystem = $filesystem;
    }

    public function getConfigurablePathBuilder(): ConfigurablePathBuilder
    {
        return $this->configurablePathBuilder;
    }

    public function getFilesystem(): \Symfony\Component\Filesystem\Filesystem
    {
        return $this->filesystem;
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

        $file = $path . Filesystem::create_unique_name($path, $fileName);

        $this->getFilesystem()->dumpFile($file, $this->serializeData($data));

        return $file;
    }
}
