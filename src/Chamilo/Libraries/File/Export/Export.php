<?php
namespace Chamilo\Libraries\File\Export;

use Chamilo\Libraries\File\ConfigurablePathBuilder;
use Chamilo\Libraries\File\Filesystem;
use Exception;

/**
 * @package Chamilo\Libraries\File\Export
 */
abstract class Export
{

    protected ConfigurablePathBuilder $configurablePathBuilder;

    public function __construct(ConfigurablePathBuilder $configurablePathBuilder)
    {
        $this->configurablePathBuilder = $configurablePathBuilder;
    }

    public function getConfigurablePathBuilder(): ConfigurablePathBuilder
    {
        return $this->configurablePathBuilder;
    }

    abstract public function render_data($data): string;

    /**
     * @throws \Exception
     */
    public function send_to_browser(string $fileName, array $data, ?string $path = null)
    {
        $file = $this->write_to_file($fileName, $data, $path);

        if ($file)
        {
            Filesystem::file_send_for_download($file, true, $fileName);
            exit();
        }
    }

    /**
     * @throws \Exception
     */
    public function write_to_file(string $fileName, array $data, ?string $path = null): string
    {
        if (!$path)
        {
            $path = $this->getConfigurablePathBuilder()->getArchivePath();
        }

        $file = $path . Filesystem::create_unique_name($path, $fileName);
        $handle = fopen($file, 'a+');

        if (!fwrite($handle, $this->render_data($data)))
        {
            throw new Exception('Writing to ' . $file . ' failed');
        }

        fclose($handle);

        return $file;
    }
}
