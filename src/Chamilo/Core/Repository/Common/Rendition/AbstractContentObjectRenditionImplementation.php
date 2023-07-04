<?php
namespace Chamilo\Core\Repository\Common\Rendition;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\File\ConfigurablePathBuilder;
use Chamilo\Libraries\File\FilesystemTools;
use Chamilo\Libraries\File\WebPathBuilder;

/**
 * @package Chamilo\Core\Repository\Common\Rendition
 */
abstract class AbstractContentObjectRenditionImplementation
{

    private $content_object;

    public function __construct(ContentObject $content_object)
    {
        $this->content_object = $content_object;
    }

    public function getConfigurablePathBuilder(): ConfigurablePathBuilder
    {
        return $this->getService(ConfigurablePathBuilder::class);
    }

    public function getFilesystemTools(): FilesystemTools
    {
        return $this->getService(FilesystemTools::class);
    }

    /**
     * @template getService
     *
     * @param class-string<getService> $serviceName
     *
     * @return getService
     */
    protected function getService(string $serviceName)
    {
        return DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(
            $serviceName
        );
    }

    public function getWebPathBuilder(): WebPathBuilder
    {
        return $this->getService(WebPathBuilder::class);
    }

    /**
     * @return
     */
    public function get_content_object()
    {
        return $this->content_object;
    }

    abstract public function get_format();

    abstract public function get_view();

    /**
     * @param $content_object
     */
    public function set_content_object($content_object)
    {
        $this->content_object = $content_object;
    }
}
