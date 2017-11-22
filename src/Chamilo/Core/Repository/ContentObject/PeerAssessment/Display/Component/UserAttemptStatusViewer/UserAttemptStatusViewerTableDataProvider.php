<?php
namespace Chamilo\Core\Repository\ContentObject\PeerAssessment\Display\Component\UserAttemptStatusViewer;

use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
use Chamilo\Libraries\Storage\ResultSet\ArrayResultSet;

/**
 *
 * @package repository.lib.complex_display.peer_asessment.component.user_attempt_status_viewer
 */
/**
 * This class represents a data provider for a results candidate table
 */
class UserAttemptStatusViewerTableDataProvider extends DataClassTableDataProvider
{

    public function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        try
        {
            $array_result = $this->get_component()->get_attempts($this->get_component()->get_publication_id());
            return new ArrayResultSet($array_result);
        }
        catch (\ErrorException $e)
        {
            return false;
        }
    }

    public function count_data($condition)
    {
        try
        {
            $array_result = $this->get_component()->get_attempts($this->get_component()->get_publication_id());
            return count($array_result);
        }
        catch (\ErrorException $e)
        {
            return false;
        }
    }
}
