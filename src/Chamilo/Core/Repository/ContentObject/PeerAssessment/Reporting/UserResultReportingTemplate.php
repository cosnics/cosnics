<?php
namespace Chamilo\Core\Repository\ContentObject\PeerAssessment\Reporting;

use Chamilo\Core\Reporting\ReportingTemplate;
use Chamilo\Core\Repository\ContentObject\PeerAssessment\Storage\DataClass\PeerAssessment;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;

class UserResultReportingTemplate extends ReportingTemplate
{

    private $user;

    private $attempt;

    private $peer_assessment;

    public function __construct($parent)
    {
        parent :: __construct($parent);

        $user_id = Request :: get(\Chamilo\Core\Repository\ContentObject\PeerAssessment\Display\Manager :: PARAM_USER);
        $attempt_id = Request :: get(
            \Chamilo\Core\Repository\ContentObject\PeerAssessment\Builder\Manager :: PARAM_ATTEMPT);
        $this->user = \Chamilo\Core\User\Storage\DataManager :: retrieve_by_id(
            \Chamilo\Core\User\Storage\DataClass\User :: class_name(),
            (int) $user_id);

        $this->peer_assessment = $this->get_parent()->get_root_content_object();
        $this->attempt = $this->get_parent()->get_attempt($attempt_id);

        $type = $this->peer_assessment->get_assessment_type();

        if ($type == PeerAssessment :: TYPE_BOTH || $type == PeerAssessment :: TYPE_FEEDBACK)
        {
            $this->add_reporting_block(UserResultReportingBlock :: factory($this, $this->user, $this->attempt));
        }

        if ($type == PeerAssessment :: TYPE_BOTH || $type == PeerAssessment :: TYPE_FEEDBACK)
        {
            $this->add_reporting_block(new UserFeedbackReportingBlock($this, $this->user, $this->attempt));
        }
    }

    public function get_name()
    {
        $title = str_replace(
            " ",
            "_",
            Translation :: get('Result') . ' ' . $this->peer_assessment->get_title() . ' ' . $this->user->get_lastname() .
                 ' ' . $this->user->get_firstname() . ' ' . $this->attempt->get_title());

        $safe_name = Filesystem :: create_safe_name($title);

        return $safe_name;
    }

    public function get_user()
    {
        return $this->user;
    }

    public function get_attempt()
    {
        return $this->attempt;
    }

    public function get_peer_assessment()
    {
        return $this->peer_assessment;
    }

    public function display_context()
    {
    }

    public function get_application()
    {
        return $this->parent->get_application();
    }
}
