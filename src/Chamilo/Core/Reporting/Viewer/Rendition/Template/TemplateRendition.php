<?php
namespace Chamilo\Core\Reporting\Viewer\Rendition\Template;

use Chamilo\Core\Reporting\Viewer\NoBlockTabsAllowed;
use Chamilo\Core\Reporting\Viewer\Rendition\Template\Implementation\AbstractTemplateRenditionImplementation;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\DependencyInjection\Traits\DependencyInjectionContainerTrait;
use Chamilo\Libraries\File\ConfigurablePathBuilder;
use Chamilo\Libraries\File\FilesystemTools;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @author  Hans De Bisschop & Magali Gillard
 * @package reporting.viewer
 */
abstract class TemplateRendition
{

    use DependencyInjectionContainerTrait;

    public const FORMAT_CSV = 'csv';

    public const FORMAT_HTML = 'html';
    public const FORMAT_JSON = 'json';

    public const FORMAT_PDF = 'pdf';

    public const FORMAT_XLSX = 'xlsx';

    public const FORMAT_XML = 'xml';

    public const VIEW_BASIC = 'basic';

    /**
     * @var \core\reporting\viewer\TemplateRenditionImplementation
     */
    private $rendition_implementation;

    /**
     * @param \core\reporting\viewer\TemplateRenditionImplementation $rendition_implementation
     */
    public function __construct(AbstractTemplateRenditionImplementation $rendition_implementation)
    {
        $this->rendition_implementation = $rendition_implementation;
    }

    public function determine_current_block_id()
    {
        $selected_block = $this->get_context()->get_current_block();

        return $selected_block ?: 0;
    }

    /**
     * @param \core\reporting\viewer\TemplateRenditionImplementation $rendition_implementation
     *
     * @return \core\reporting\viewer\TemplateRendition
     */
    public static function factory($rendition_implementation)
    {
        $class = __NAMESPACE__ . '\Type\\' .
            (string) StringUtilities::getInstance()->createString($rendition_implementation->get_format())
                ->upperCamelize() . '\\' .
            StringUtilities::getInstance()->createString($rendition_implementation->get_view())->upperCamelize();

        return new $class($rendition_implementation);
    }

    public function getArchivePath(): string
    {
        $container = DependencyInjectionContainerBuilder::getInstance()->createContainer();
        /**
         * @var \Chamilo\Libraries\File\ConfigurablePathBuilder $configurablePathBuilder
         */
        $configurablePathBuilder = $container->get(ConfigurablePathBuilder::class);

        return $configurablePathBuilder->getArchivePath();
    }

    public function getFilesystemTools(): FilesystemTools
    {
        return DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(FilesystemTools::class);
    }

    /**
     * @return \Chamilo\Libraries\Architecture\Application\Application
     */
    public function get_context()
    {
        return $this->rendition_implementation->get_context();
    }

    public static function get_format()
    {
        return static::FORMAT;
    }

    /**
     * @return \core\reporting\viewer\TemplateRenditionImplementation
     */
    public function get_rendition_implementation()
    {
        return $this->rendition_implementation;
    }

    /**
     * @return \core\reporting\viewer\ReportingTemplate
     */
    public function get_template()
    {
        return $this->rendition_implementation->get_template();
    }

    /**
     * @param \core\reporting\viewer\TemplateRenditionImplementation $rendition_implementation
     *
     * @return string
     */
    public static function launch($rendition_implementation)
    {
        return self::factory($rendition_implementation)->render();
    }

    /**
     * @param \Chamilo\Libraries\Architecture\Application\Application $context
     */
    public function set_context($context)
    {
        $this->rendition_implementation->set_context($context);
    }

    /**
     * @param \core\reporting\viewer\TemplateRenditionImplementation $rendition_implementation
     */
    public function set_rendition_implementation($rendition_implementation)
    {
        $this->rendition_implementation = $rendition_implementation;
    }

    /**
     * @param \core\reporting\viewer\ReportingTemplate $template
     */
    public function set_template($template)
    {
        $this->rendition_implementation->set_template($template);
    }

    public function show_all()
    {
        return $this->get_context()->show_all() || $this->get_template() instanceof NoBlockTabsAllowed;
    }
}
