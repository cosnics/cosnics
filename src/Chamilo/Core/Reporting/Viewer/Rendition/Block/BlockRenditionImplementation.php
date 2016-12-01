<?php
namespace Chamilo\Core\Reporting\Viewer\Rendition\Block;

use Chamilo\Core\Reporting\ReportingBlock;
use Chamilo\Core\Reporting\Viewer\Rendition\Block\Implementation\AbstractBlockRenditionImplementation;
use Chamilo\Core\Reporting\Viewer\Rendition\Block\Implementation\DummyBlockRenditionImplementation;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @author Hans De Bisschop & Magali Gillard
 * @package reporting.viewer
 */
abstract class BlockRenditionImplementation extends AbstractBlockRenditionImplementation
{

    /**
     *
     * @param \libraries\architecture\application\Application $context
     * @param ReportingBlock $block
     * @param string $format
     * @param string $view
     */
    public static function launch($context, ReportingBlock $block, $format = BlockRendition :: FORMAT_HTML, $view = null)
    {
        return self::factory($context, $block, $format, $view)->render();
    }

    /**
     *
     * @param \libraries\architecture\application\Application $context
     * @param ReportingBlock $block
     * @param string $format
     * @param string $view
     * @return \core\reporting\DummyContentObjectRenditionImplementation \core\reporting\BlockRenditionImplementation
     */
    public static function factory($context, ReportingBlock $block, $format = BlockRendition :: FORMAT_HTML, $view = null)
    {
        $namespace = ClassnameUtilities::getInstance()->getNamespaceFromObject($block);
        $class = $namespace . '\Implementation\\' .
             (string) StringUtilities::getInstance()->createString($format)->upperCamelize() . '\\' . StringUtilities::getInstance()->createString(
                $view)->upperCamelize();
        
        if (! class_exists($class, true))
        {
            return new DummyBlockRenditionImplementation($context, $block, $format, $view);
        }
        else
        {
            return new $class($context, $block);
        }
    }

    /**
     *
     * @return string
     */
    public function get_view()
    {
        $class_name_parts = explode('_', ClassnameUtilities::getInstance()->getClassnameFromObject($this, true));
        return $class_name_parts[1];
    }

    /**
     *
     * @return string
     */
    public function get_format()
    {
        $class_name_parts = explode('_', ClassnameUtilities::getInstance()->getClassnameFromObject($this, true));
        return $class_name_parts[0];
    }
}
