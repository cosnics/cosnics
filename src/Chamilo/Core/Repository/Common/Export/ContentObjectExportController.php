<?php
namespace Chamilo\Core\Repository\Common\Export;

use Chamilo\Core\Repository\Workspace\Service\ContentObjectRelationService;
use Chamilo\Libraries\DependencyInjection\Traits\DependencyInjectionContainerTrait;
use Chamilo\Libraries\File\Properties\FileProperties;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package repository.lib
 */
abstract class ContentObjectExportController
{
    use DependencyInjectionContainerTrait;

    private $parameters;

    /**
     * @param ExportParameters $parameters
     */
    public function __construct(ExportParameters $parameters)
    {
        $this->initializeContainer();

        $this->parameters = $parameters;
    }

    /**
     * @return string
     */
    abstract public function run();

    public function download()
    {
        $path = $this->run();

        $file_properties = FileProperties::from_path($path);
        $this->getFilesystemTools()->sendFileForDownload($path, $this->get_filename(), $file_properties->getType());
        $this->getFilesystem()->remove($path);
    }

    /**
     * @param $parameters ExportParameters
     */
    public static function factory(ExportParameters $parameters)
    {
        $format = (string) StringUtilities::getInstance()->createString($parameters->get_format())->upperCamelize();
        $class = __NAMESPACE__ . '\\' . $format . '\\' . $format . 'ContentObjectExportController';

        return new $class($parameters);
    }

    protected function getContentObjectRelationService(): ContentObjectRelationService
    {
        return $this->getService(ContentObjectRelationService::class);
    }

    /**
     * @return string
     */
    abstract public function get_filename();

    /**
     * @var ExportParameters
     */
    public function get_parameters()
    {
        return $this->parameters;
    }

    public function set_parameters($parameters)
    {
        $this->parameters = $parameters;
    }
}
