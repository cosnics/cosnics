<?php
namespace Chamilo\Core\Repository\Common\Rendition;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\File\ConfigurablePathBuilder;
use Chamilo\Libraries\File\SystemPathBuilder;
use Chamilo\Libraries\File\WebPathBuilder;

/**
 * @package repository.lib
 *          A class to render a ContentObject.
 */
abstract class AbstractContentObjectRenditionImplementation
{

    private $content_object;

    public function __construct(ContentObject $content_object)
    {
        $this->content_object = $content_object;
    }

    public function getWebPathBuilder(): WebPathBuilder
    {
        return DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(WebPathBuilder::class);
    }

    public function getConfigurablePathBuilder(): ConfigurablePathBuilder
    {
        return DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(ConfigurablePathBuilder::class);
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
