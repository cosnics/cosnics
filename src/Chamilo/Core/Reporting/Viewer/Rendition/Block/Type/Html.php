<?php
namespace Chamilo\Core\Reporting\Viewer\Rendition\Block\Type;

use Chamilo\Core\Reporting\Viewer\Manager;
use Chamilo\Core\Reporting\Viewer\Rendition\Block\BlockRendition;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTab;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTabsRenderer;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @author Hans De Bisschop & Magali Gillard
 * @package reporting.viewer
 */
class Html extends BlockRendition
{
    const FORMAT = 'html';
    const VIEW_TABLE = 'table';
    const VIEW_PIE = 'pie_chart';
    const VIEW_RING = 'ring_chart';
    const VIEW_3D_PIE = 'three_dee_pie_chart';
    const VIEW_BAR = 'bar_chart';
    const VIEW_STACKED_BAR = 'stacked_bar_chart';
    const VIEW_LINE = 'line_chart';
    const VIEW_AREA = 'area_chart';
    const VIEW_STACKED_AREA = 'stacked_area_chart';
    const VIEW_POLAR = 'polar_chart';
    const VIEW_RADAR = 'radar_chart';
    const VIEW_CSV = 'csv';
    const VIEW_XML = 'xml';
    const VIEW_PDF = 'pdf';
    const VIEW_XLSX = 'xlsx';

    public function render()
    {
        $rendered_block = $this->get_content();

        if (count($this->get_block()->get_views()) > 1)
        {
            $tabs = new DynamicVisualTabsRenderer(
                ClassnameUtilities :: getInstance()->getClassnameFromObject($this->get_block(), true),
                $rendered_block);

            $context_parameters = $this->get_context()->get_context()->get_parameters();

            foreach ($this->get_block()->get_views() as $view)
            {
                $view_parameters = $context_parameters[Manager :: PARAM_VIEWS] ? $context_parameters[Manager :: PARAM_VIEWS] : array();
                $view_parameters[$this->get_block()->get_id()] = $view;

                $view_parameters = array_merge($context_parameters, array(Manager :: PARAM_VIEWS => $view_parameters));
                $view_parameters[Manager :: PARAM_BLOCK_ID] = $this->get_context()->determine_current_block_id();

                $is_current_view = $view == $this->get_context()->determine_current_block_view(
                    $this->get_block()->get_id()) ? true : false;

                $tabs->add_tab(
                    new DynamicVisualTab(
                        $view,
                        Translation :: get(
                            (string) StringUtilities :: getInstance()->createString(self :: FORMAT . '_' . $view)->upperCamelize()),
                        Theme :: getInstance()->getImagePath(
                            'Chamilo\Core\Reporting\Viewer',
                            'Rendition/Block/' . self :: FORMAT . '/' . $view),
                        $this->get_context()->get_context()->get_url($view_parameters),
                        $is_current_view));
            }

            return $tabs->render();
        }
        else
        {
            return $rendered_block;
        }
    }
}
