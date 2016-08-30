<?php
namespace Chamilo\Core\Reporting\Viewer\Rendition\Block;

use Chamilo\Core\Reporting\Viewer\Rendition\Block\Implementation\AbstractBlockRenditionImplementation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @author Hans De Bisschop & Magali Gillard
 * @package reporting.viewer
 */
abstract class BlockRendition
{
    const FORMAT_XML = 'xml';
    const FORMAT_HTML = 'html';
    const FORMAT_JSON = 'json';
    const FORMAT_CSV = 'csv';

    /**
     *
     * @var \core\reporting\AbstractBlockRenditionImplementation
     */
    private $rendition_implementation;

    /**
     *
     * @param \core\reporting\viewer\AbstractBlockRenditionImplementation $rendition_implementation
     */
    public function __construct(AbstractBlockRenditionImplementation $rendition_implementation)
    {
        $this->rendition_implementation = $rendition_implementation;
    }

    /**
     *
     * @return \core\reporting\viewer\BlockRenditionImplementation
     */
    public function get_rendition_implementation()
    {
        return $this->rendition_implementation;
    }

    /**
     *
     * @param \core\reporting\viewer\BlockRenditionImplementation $rendition_implementation
     */
    public function set_rendition_implementation(BlockRenditionImplementation $rendition_implementation)
    {
        $this->rendition_implementation = $rendition_implementation;
    }

    public function get_context()
    {
        return $this->rendition_implementation->get_context();
    }

    public function set_context($context)
    {
        $this->rendition_implementation->set_context($context);
    }

    /**
     *
     * @return \core\reporting\ReportingBlock
     */
    public function get_block()
    {
        return $this->rendition_implementation->get_block();
    }

    /**
     *
     * @param \core\reporting\ReportingBlock $block
     */
    public function set_block($block)
    {
        $this->rendition_implementation->set_block($block);
    }

    /**
     *
     * @param \core\reporting\viewer\BlockRenditionImplementation $rendition_implementation
     * @return string
     */
    public static function launch($rendition_implementation)
    {
        return self :: factory($rendition_implementation)->render();
    }

    /**
     *
     * @param \core\reporting\viewer\BlockRenditionImplementation $rendition_implementation
     * @return \core\reporting\viewer\BlockRendition
     */
    public static function factory($rendition_implementation)
    {
        $class = __NAMESPACE__ . '\Type\\' .
             (string) StringUtilities :: getInstance()->createString($rendition_implementation->get_format())->upperCamelize() .
             '\\' .
             StringUtilities :: getInstance()->createString($rendition_implementation->get_view())->upperCamelize();
        return new $class($rendition_implementation);
    }

    public static function get_format()
    {
        return static :: FORMAT;
    }

    public static function get_view()
    {
        return static :: VIEW;
    }
}
