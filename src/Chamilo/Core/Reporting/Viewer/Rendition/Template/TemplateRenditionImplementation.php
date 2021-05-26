<?php
namespace Chamilo\Core\Reporting\Viewer\Rendition\Template;

use Chamilo\Core\Reporting\ReportingTemplate;
use Chamilo\Core\Reporting\Viewer\Rendition\Template\Implementation\AbstractTemplateRenditionImplementation;
use Chamilo\Core\Reporting\Viewer\Rendition\Template\Implementation\DummyTemplateRenditionImplementation;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @author Hans De Bisschop & Magali Gillard
 * @package reporting.viewer
 */
abstract class TemplateRenditionImplementation extends AbstractTemplateRenditionImplementation
{

    /**
     *
     * @param \Chamilo\Libraries\Architecture\Application\Application $context
     * @param ReportingTemplate $template
     * @param string $format
     * @param string $view
     */
    public static function launch($context, ReportingTemplate $template, $format = TemplateRendition::FORMAT_HTML, 
        $view = TemplateRendition::VIEW_BASIC)
    {
        return self::factory($context, $template, $format, $view)->render();
    }

    /**
     *
     * @param \Chamilo\Libraries\Architecture\Application\Application $context
     * @param ReportingTemplate $template
     * @param string $format
     * @param string $view
     * @return \core\reporting\DummyContentObjectRenditionImplementation \core\reporting\TemplateRenditionImplementation
     */
    public static function factory($context, ReportingTemplate $template, $format = TemplateRendition::FORMAT_HTML, 
        $view = TemplateRendition::VIEW_BASIC)
    {
        $namespace = ClassnameUtilities::getInstance()->getNamespaceFromObject($template);
        $class = $namespace . '\\' . StringUtilities::getInstance()->createString($format)->upperCamelize() . StringUtilities::getInstance()->createString(
            $view)->upperCamelize() . 'TemplateRenditionImplementation';
        
        if (! class_exists($class, true))
        {
            return new DummyTemplateRenditionImplementation($context, $template, $format, $view);
        }
        else
        {
            return new $class($context, $template);
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
