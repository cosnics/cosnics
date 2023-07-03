<?php
namespace Chamilo\Core\Reporting\Viewer\Rendition\Block\Type;

use Chamilo\Core\Reporting\Viewer\Manager;
use Chamilo\Core\Reporting\Viewer\Rendition\Block\BlockRendition;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\File\FilesystemTools;
use Chamilo\Libraries\Format\Structure\Glyph\NamespaceIdentGlyph;
use Chamilo\Libraries\Format\Tabs\Link\LinkTab;
use Chamilo\Libraries\Format\Tabs\Link\LinkTabsRenderer;
use Chamilo\Libraries\Format\Tabs\TabsCollection;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @author  Hans De Bisschop & Magali Gillard
 * @package reporting.viewer
 */
class Html extends BlockRendition
{
    public const FORMAT = 'Html';

    public const VIEW_3D_PIE = 'ThreeDeePieChart';
    public const VIEW_AREA = 'AreaChart';
    public const VIEW_BAR = 'BarChart';
    public const VIEW_CSV = 'Csv';
    public const VIEW_LINE = 'LineChart';
    public const VIEW_PDF = 'Pdf';
    public const VIEW_PIE = 'PieChart';
    public const VIEW_POLAR = 'PolarChart';
    public const VIEW_RADAR = 'RadarChart';
    public const VIEW_RING = 'RingChart';
    public const VIEW_STACKED_AREA = 'StackedAreaChart';
    public const VIEW_STACKED_BAR = 'StackedBarChart';
    public const VIEW_TABLE = 'Table';
    public const VIEW_XLSX = 'Xlsx';
    public const VIEW_XML = 'Xml';

    public function render()
    {
        $rendered_block = $this->get_content();

        if (count($this->get_block()->get_views()) > 1)
        {
            $tabs = new TabsCollection();

            $context_parameters = $this->get_context()->get_context()->get_parameters();

            foreach ($this->get_block()->get_views() as $view)
            {
                $view_parameters = $context_parameters[Manager::PARAM_VIEWS] ?: [];
                $view_parameters[$this->get_block()->get_id()] = $view;

                $view_parameters = array_merge($context_parameters, [Manager::PARAM_VIEWS => $view_parameters]);
                $view_parameters[Manager::PARAM_BLOCK_ID] = $this->get_context()->determine_current_block_id();

                $is_current_view = $view == $this->get_context()->determine_current_block_view(
                        $this->get_block()->get_id()
                    );

                $glyph = new NamespaceIdentGlyph(
                    'Chamilo\Core\Reporting\Viewer\Rendition\Block\Html\\' . $view
                );

                $tabs->add(
                    new LinkTab(
                        $view, Translation::get(
                        (string) StringUtilities::getInstance()->createString(self::FORMAT . '_' . $view)
                            ->upperCamelize()
                    ), $glyph, $this->get_context()->get_context()->get_url($view_parameters), $is_current_view
                    )
                );
            }

            return $this->getLinkTabsRenderer()->render($tabs, $rendered_block);
        }
        else
        {
            return $rendered_block;
        }
    }

    protected function getLinkTabsRenderer(): LinkTabsRenderer
    {
        return DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(
            LinkTabsRenderer::class
        );
    }

    protected function getFilesystemTools(): FilesystemTools
    {
        return DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(
            FilesystemTools::class
        );
    }

    protected function getFilesystem(): Filesystem
    {
        return DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(
            Filesystem::class
        );
    }
}
