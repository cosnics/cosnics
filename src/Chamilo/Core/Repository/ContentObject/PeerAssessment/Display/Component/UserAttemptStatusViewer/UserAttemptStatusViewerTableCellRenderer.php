<?php
namespace Chamilo\Core\Repository\ContentObject\PeerAssessment\Display\Component\UserAttemptStatusViewer;

use Chamilo\Core\Repository\ContentObject\PeerAssessment\Display\Manager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;

/**
 * This class is a cell renderer for a publication candidate table
 */
class UserAttemptStatusViewerTableCellRenderer extends DataClassTableCellRenderer
{

    public function get_status($user_attempt_status_item)
    {
        if (! isset($this->status[$user_attempt_status_item->get_id()]))
        {
            $this->status[$user_attempt_status_item->get_id()] = $this->get_component()->get_user_attempt_status(
                $this->get_component()->get_user_id(),
                $user_attempt_status_item->get_id());
        }
        return $this->status[$user_attempt_status_item->get_id()];
    }

    public function render_cell($column, $attempt)
    {
        $status = $this->get_component()->get_user_attempt_status(
            $this->get_component()->get_user_id(),
            $attempt->get_id());

        switch ($column->get_name())
        {
            case UserAttemptStatusViewerTableColumnModel::COLUMN_STATUS :
                return $this->get_component()->render_status($status, $attempt);

            case UserAttemptStatusViewerTableColumnModel::COLUMN_TITLE :
                $title = $attempt->get_title();
                if ($status->get_closed())
                {
                    return $title;
                }
                $url = $this->get_component()->get_url(
                    array(
                        Manager::PARAM_ACTION => Manager::ACTION_TAKE_PEER_ASSESSMENT,
                        Manager::PARAM_ATTEMPT => $attempt->get_id()));
                return '<a href="' . $url . '">' . $title . '</a>';

            case UserAttemptStatusViewerTableColumnModel::COLUMN_START_DATE :
                return date('d-m-Y', $attempt->get_start_date());

            case UserAttemptStatusViewerTableColumnModel::COLUMN_END_DATE :
                return date('d-m-Y', $attempt->get_end_date());

            default :
                return parent::render_cell($column, $attempt);
        }
    }
}
