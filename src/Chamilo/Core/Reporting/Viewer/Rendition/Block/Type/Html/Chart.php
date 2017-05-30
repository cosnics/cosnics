<?php
namespace Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html;

use Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\File\ConfigurablePathBuilder;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Display;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @author Hans De Bisschop & Magali Gillard
 * @package reporting.viewer
 */
abstract class Chart extends Html
{

    /**
     *
     * @return string
     */
    public function get_content()
    {
        $path = $this->get_path();
        if ($path)
        {
            return '<img src="' . $this->get_path() . '" border="0" />';
        }
        else
        {
            return Display::normal_message(Translation::get('NoDataNoChart'), true);
        }
    }

    /**
     *
     * @return string
     */
    abstract public function get_path();

    /**
     *
     * @return multitype:mixed
     */
    public function convert_reporting_data()
    {
        $reporting_data = $this->get_block()->retrieve_data();
        if ($reporting_data->is_empty())
        {
            return false;
        }
        else
        {
            $chart = array();
            $chart_description = array();
            $chart_data = array();
            
            $chart_description['Position'] = 'Name';
            $chart_description['Values'] = array();
            $chart_description['Description'] = array();
            foreach ($reporting_data->get_rows() as $row_id => $row_name)
            {
                $chart_description['Values'][$row_id] = 'Serie' . $row_id;
                $chart_description['Description']['Serie' . $row_id] = trim(
                    trim(trim(html_entity_decode(strip_tags($row_name), ENT_COMPAT, 'utf-8')), "\xC2\xA0"));
            }
            
            $chart[1] = $chart_description;
            
            foreach ($reporting_data->get_categories() as $category_id => $category_name)
            {
                $category_array = array();
                $category_array['Name'] = trim(
                    trim(html_entity_decode(strip_tags($category_name), ENT_COMPAT, 'utf-8')), 
                    "\xC2\xA0");
                foreach ($reporting_data->get_rows() as $row_id => $row_name)
                {
                    $category_array['Serie' . $row_id] = $reporting_data->get_data_category_row($category_id, $row_id);
                }
                $chart_data[] = $category_array;
            }
            
            $chart[0] = $chart_data;
            return $chart;
        }
    }

    protected function strip_data_names($data)
    {
        foreach ($data as $key => $value)
        {
            foreach ($value as $key2 => $value2)
            {
                if ($key2 == "Name")
                {
                    $value[$key2] = StringUtilities::getInstance()->truncate(
                        trim(html_entity_decode(strip_tags($value2), ENT_COMPAT, 'utf-8')), 
                        30, 
                        false, 
                        '...');
                }
            }
            $data[$key] = $value;
        }
        return $data;
    }

    /**
     *
     * @return string
     */
    public function getUrl($md5)
    {
        $graphUrl = new Redirect(
            array(
                Application::PARAM_CONTEXT => \Chamilo\Core\Reporting\Viewer\Ajax\Manager::context(), 
                Application::PARAM_ACTION => \Chamilo\Core\Reporting\Viewer\Ajax\Manager::ACTION_GRAPH, 
                \Chamilo\Core\Reporting\Viewer\Ajax\Manager::PARAM_GRAPHMD5 => $md5));
        
        return $graphUrl->getUrl();
    }

    /**
     *
     * @return string
     */
    public function getFilePath($md5)
    {
        $rootPath = $this->getConfigurablePathBuilder()->getTemporaryPath();
        return $rootPath . $md5 . '.png';
    }

    /**
     * @return object | ConfigurablePathBuilder
     */
    protected function getConfigurablePathBuilder()
    {
        $container = DependencyInjectionContainerBuilder::getInstance()->createContainer();
        return $container->get('chamilo.libraries.file.configurable_path_builder');
    }
}
