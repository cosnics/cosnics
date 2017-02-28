<?php
namespace Chamilo\Core\Repository\ContentObject\PeerAssessment\Reporting;

use Chamilo\Core\Reporting\ReportingBlock;
use Chamilo\Core\Reporting\ReportingData;
use Chamilo\Libraries\Platform\Translation;

class UserFeedbackReportingBlock extends ReportingBlock
{

    protected $user;

    protected $attempt;

    public function __construct($parent, $user, $attempt)
    {
        parent::__construct($parent);

        $this->user = $user;
        $this->attempt = $attempt;
    }

    public function count_data()
    {
        return null;
    }

    public function get_title()
    {
        $peer_assessment = $this->get_parent()->get_peer_assessment();
        return Translation::get('Feedback') . ' ' . $peer_assessment->get_title() . ' ' . $this->user->get_lastname() .
             ' ' . $this->user->get_firstname() . ' ' . $this->attempt->get_title();
    }

    public function retrieve_data()
    {
        return $this->compose_data();
    }

    public function get_available_displaymodes()
    {
        return array(

        \Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html::VIEW_TABLE);
    }

    public function get_data_manager()
    {
        return \Chamilo\Core\Repository\Storage\DataManager::getInstance();
    }

    public function get_application()
    {
        return 'content_object_peer_assessment';
    }

    public function compose_data()
    {
        $reporting_data = new ReportingData();

        $rows = array(Translation::get('Feedback'));
        $reporting_data->set_rows($rows);

        $feedback = $this->get_parent()->get_parent()->get_user_feedback_received(
            $this->user->get_id(),
            $this->attempt->get_id());

        foreach ($feedback as $user_id => $feedback_row)
        {
            if ($user_id == $this->user->get_id())
            {
                $giver = $this->user;
            }
            else
            {
                $giver = \Chamilo\Core\User\Storage\DataManager::retrieve_by_id(
                    \Chamilo\Core\User\Storage\DataClass\User::class_name(),
                    (int) $user_id);
            }

            $user_title = $giver->get_firstname() . ' ' . $giver->get_lastname();

            $reporting_data->add_category($user_title);
            $reporting_data->add_data_category_row($user_title, Translation::get('Feedback'), $feedback_row);
        }

//         $reporting_data->hide_categories();
        return $reporting_data;
    }

    function get_views()
    {
    }
}
