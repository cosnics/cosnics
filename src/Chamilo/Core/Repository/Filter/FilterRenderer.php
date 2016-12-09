<?php
namespace Chamilo\Core\Repository\Filter;

use Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface;

/**
 * Abstract FilterRenderer to facilitate the processing of FilterData for various purposes
 * 
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class FilterRenderer
{

    /**
     *
     * @var \Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface
     */
    private $workspace;

    /**
     *
     * @var \core\repository\filter\FilterData
     */
    private $filter_data;

    /**
     *
     * @param \core\repository\filter\FilterData $filter_data
     */
    public function __construct(FilterData $filter_data, WorkspaceInterface $workspace)
    {
        $this->filter_data = $filter_data;
        $this->workspace = $workspace;
    }

    /**
     *
     * @return \core\repository\filter\FilterData
     */
    public function get_filter_data()
    {
        return $this->filter_data;
    }

    /**
     *
     * @param \core\repository\filter\FilterData $filter_data
     */
    public function set_filter_data(FilterData $filter_data)
    {
        $this->filter_data = $filter_data;
    }

    /**
     *
     * @return \Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface
     */
    public function get_workspace()
    {
        return $this->workspace;
    }

    /**
     *
     * @param \Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface $workspace
     */
    public function set_workspace($workspace)
    {
        $this->workspace = $workspace;
    }

    abstract function render();
}