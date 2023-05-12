<?php
namespace Chamilo\Core\Repository\Filter;

use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;

/**
 * Abstract FilterRenderer to facilitate the processing of FilterData for various purposes
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class FilterRenderer
{

    /**
     * @var \core\repository\filter\FilterData
     */
    private $filter_data;

    private Workspace $workspace;

    /**
     * @param \core\repository\filter\FilterData $filter_data
     */
    public function __construct(FilterData $filter_data, Workspace $workspace)
    {
        $this->filter_data = $filter_data;
        $this->workspace = $workspace;
    }

    abstract public function render();

    /**
     * @return \core\repository\filter\FilterData
     */
    public function get_filter_data()
    {
        return $this->filter_data;
    }

    public function get_workspace(): Workspace
    {
        return $this->workspace;
    }

    /**
     * @param \core\repository\filter\FilterData $filter_data
     */
    public function set_filter_data(FilterData $filter_data)
    {
        $this->filter_data = $filter_data;
    }

    public function set_workspace(Workspace $workspace)
    {
        $this->workspace = $workspace;
    }
}