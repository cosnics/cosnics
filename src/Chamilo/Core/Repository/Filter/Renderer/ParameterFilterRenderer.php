<?php
namespace Chamilo\Core\Repository\Filter\Renderer;

use Chamilo\Core\Repository\Filter\FilterData;
use Chamilo\Core\Repository\Filter\FilterRenderer;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;

/**
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ParameterFilterRenderer extends FilterRenderer
{

    /**
     * @var string
     */
    private $filter_property;

    /**
     * @param \core\repository\filter\FilterData $filter_data
     */
    public function __construct(FilterData $filter_data, Workspace $workspace, $filter_property)
    {
        parent::__construct($filter_data, $workspace);
        $this->filter_property = $filter_property;
    }

    public function render()
    {
        $filter_data = $this->get_filter_data();

        switch ($this->get_filter_property())
        {
            case FilterData::FILTER_CATEGORY :
                $filter_data->set_filter_property(FilterData::FILTER_CATEGORY, null);
                $filter_data->set_filter_property(FilterData::FILTER_CATEGORY_RECURSIVE, null);
                break;
            case HtmlFilterRenderer::CLEAR_ALL :
                $filter_data->clear();
            default :
                $filter_data->set_filter_property($this->get_filter_property(), null);
                break;
        }
    }

    /**
     * @param \core\repository\filter\FilterData $filter_data
     *
     * @return \core\repository\filter\renderer\ParameterFilterRenderer
     */
    public static function factory(FilterData $filter_data, Workspace $workspace, $filter_property)
    {
        $class_name = $filter_data->get_context() . '\Filter\Renderer\ParameterFilterRenderer';

        return new $class_name($filter_data, $workspace, $filter_property);
    }

    /**
     * @return string
     */
    public function get_filter_property()
    {
        return $this->filter_property;
    }

    /**
     * @param string $filter_property
     */
    public function set_filter_property($filter_property)
    {
        $this->filter_property = $filter_property;
    }
}