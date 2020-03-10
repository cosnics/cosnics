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
    const FORMAT_CSV = 'csv';

    const FORMAT_HTML = 'html';
    const FORMAT_JSON = 'json';

    const FORMAT_XML = 'xml';

    /**
     *
     * @var \Chamilo\Core\Reporting\Viewer\Rendition\Block\Implementation\AbstractBlockRenditionImplementation
     */
    private $rendition_implementation;

    /**
     *
     * @param \Chamilo\Core\Reporting\Viewer\Rendition\Block\Implementation\AbstractBlockRenditionImplementation $rendition_implementation
     */
    public function __construct(AbstractBlockRenditionImplementation $rendition_implementation)
    {
        $this->rendition_implementation = $rendition_implementation;
    }

    /**
     *
     * @param \Chamilo\Core\Reporting\Viewer\Rendition\Block\BlockRenditionImplementation $rendition_implementation
     *
     * @return \Chamilo\Core\Reporting\Viewer\Rendition\Block\BlockRendition
     */
    public static function factory($rendition_implementation)
    {
        $class = __NAMESPACE__ . '\Type\\' .
            (string) StringUtilities::getInstance()->createString($rendition_implementation->get_format())
                ->upperCamelize() . '\\' .
            StringUtilities::getInstance()->createString($rendition_implementation->get_view())->upperCamelize();

        return new $class($rendition_implementation);
    }

    /**
     *
     * @return \Chamilo\Core\Reporting\ReportingBlock
     */
    public function get_block()
    {
        return $this->rendition_implementation->get_block();
    }

    public function get_context()
    {
        return $this->rendition_implementation->get_context();
    }

    public static function get_format()
    {
        return static::FORMAT;
    }

    /**
     *
     * @return \Chamilo\Core\Reporting\Viewer\Rendition\Block\BlockRenditionImplementation
     */
    public function get_rendition_implementation()
    {
        return $this->rendition_implementation;
    }

    /**
     *
     * @param \Chamilo\Core\Reporting\Viewer\Rendition\Block\BlockRenditionImplementation $rendition_implementation
     */
    public function set_rendition_implementation(BlockRenditionImplementation $rendition_implementation)
    {
        $this->rendition_implementation = $rendition_implementation;
    }

    public static function get_view()
    {
        return static::VIEW;
    }

    /**
     *
     * @param \Chamilo\Core\Reporting\Viewer\Rendition\Block\BlockRenditionImplementation $rendition_implementation
     *
     * @return string
     */
    public static function launch($rendition_implementation)
    {
        return self::factory($rendition_implementation)->render();
    }

    /**
     *
     * @param \Chamilo\Core\Reporting\ReportingBlock $block
     */
    public function set_block($block)
    {
        $this->rendition_implementation->set_block($block);
    }

    public function set_context($context)
    {
        $this->rendition_implementation->set_context($context);
    }
}
