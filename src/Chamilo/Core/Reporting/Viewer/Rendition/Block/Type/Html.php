<?php
namespace Chamilo\Core\Reporting\Viewer\Rendition\Block\Type;

use Chamilo\Core\Reporting\Viewer\Manager;
use Chamilo\Core\Reporting\Viewer\Rendition\Block\BlockRendition;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Structure\Glyph\NamespaceIdentGlyph;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTab;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTabsRenderer;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @author Hans De Bisschop & Magali Gillard
 * @package reporting.viewer
 */
class Html extends BlockRendition
{
    const FORMAT = 'Html';

    const VIEW_3D_PIE = 'ThreeDeePieChart';

    const VIEW_AREA = 'AreaChart';

    const VIEW_BAR = 'BarChart';

    const VIEW_CSV = 'Csv';

    const VIEW_LINE = 'LineChart';

    const VIEW_PDF = 'Pdf';

    const VIEW_PIE = 'PieChart';

    const VIEW_POLAR = 'PolarChart';

    const VIEW_RADAR = 'RadarChart';

    const VIEW_RING = 'RingChart';

    const VIEW_STACKED_AREA = 'StackedAreaChart';

    const VIEW_STACKED_BAR = 'StackedBarChart';

    const VIEW_TABLE = 'Table';

    const VIEW_XLSX = 'Xlsx';

    const VIEW_XML = 'Xml';

    public function render()
    {
        $rendered_block = $this->get_content();

        if (count($this->get_block()->get_views()) > 1)
        {
            $tabs = new DynamicVisualTabsRenderer(
                ClassnameUtilities::getInstance()->getClassnameFromObject($this->get_block(), true), $rendered_block
            );

            $context_parameters = $this->get_context()->get_context()->get_parameters();

            foreach ($this->get_block()->get_views() as $view)
            {
                $view_parameters =
                    $context_parameters[Manager::PARAM_VIEWS] ? $context_parameters[Manager::PARAM_VIEWS] : array();
                $view_parameters[$this->get_block()->get_id()] = $view;

                $view_parameters = array_merge($context_parameters, array(Manager::PARAM_VIEWS => $view_parameters));
                $view_parameters[Manager::PARAM_BLOCK_ID] = $this->get_context()->determine_current_block_id();

                $is_current_view = $view == $this->get_context()->determine_current_block_view(
                    $this->get_block()->get_id()
                ) ? true : false;

                $glyph = new NamespaceIdentGlyph(
                    'Chamilo\Core\Reporting\Viewer\Rendition\Block\Html\\' . $view
                );

                $tabs->add_tab(
                    new DynamicVisualTab(
                        $view, Translation::get(
                        (string) StringUtilities::getInstance()->createString(self::FORMAT . '_' . $view)
                            ->upperCamelize()
                    ), $glyph, $this->get_context()->get_context()->get_url($view_parameters), $is_current_view
                    )
                );
            }

            return $tabs->render();
        }
        else
        {
            return $rendered_block;
        }
    }
}
