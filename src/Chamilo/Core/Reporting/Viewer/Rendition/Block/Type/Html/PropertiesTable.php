<?php
namespace Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html;

use Chamilo\Core\Reporting\ReportingData;
use Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\Format\Table\PropertiesTableRenderer;

/**
 * @package core\reporting\viewer
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class PropertiesTable extends Html
{
    public const VIEW = 'properties_table';

    public function convert_reporting_data(ReportingData $data)
    {
        $properties = [];

        foreach ($data->get_rows() as $row_id => $row)
        {
            $properties[$row] = [];

            foreach ($data->get_categories() as $category_id => $category_name)
            {
                $properties[$row][] = $data->get_data_category_row($category_id, $row_id);
            }
        }

        return $properties;
    }

    public function getPropertiesTableRenderer(): PropertiesTableRenderer
    {
        return DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(
            PropertiesTableRenderer::class
        );
    }

    public function get_content()
    {
        $block = $this->get_block();
        $reporting_data = $block->get_data();

        return $this->getPropertiesTableRenderer()->render($this->convert_reporting_data($reporting_data));
    }
}
