<?php
namespace Chamilo\Core\Repository\ContentObject\PeerAssessment\Reporting;

use Chamilo\Core\Reporting\ReportingTemplate;
use Chamilo\Core\Repository\ContentObject\PeerAssessment\Storage\DataClass\PeerAssessment;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\Platform\Translation;

class ResultReportingTemplate extends ReportingTemplate
{

    private $user;

    private $attempt;

    private $peer_assessment;

    public function __construct($parent)
    {
        parent::__construct($parent);

        $this->peer_assessment = $this->get_parent()->get_root_content_object();

        $type = $this->peer_assessment->get_assessment_type();

        $feedback = false;
        if ($type == PeerAssessment::TYPE_BOTH || $type == PeerAssessment::TYPE_FEEDBACK)
            $feedback = true;

        $score = false;
        if ($type == PeerAssessment::TYPE_BOTH || $type == PeerAssessment::TYPE_SCORES)
            $score = true;

        $publication_id = $parent->get_publication_id();

        $attempts = $parent->get_attempts($publication_id);
        $groups = $parent->get_groups($publication_id);

        // one report per group > attempt > user
        foreach ($groups as $group)
        {
            $users = $parent->get_group_users($group->get_id());

            foreach ($attempts as $attempt)
            {
                foreach ($users as $user)
                {
                    if ($score)
                    {
                        $this->add_reporting_block(UserResultReportingBlock::factory($this, $user, $attempt));
                    }

                    if ($feedback)
                    {
                        $this->add_reporting_block(new UserFeedbackReportingBlock($this, $user, $attempt));
                    }
                }
            }
        }
    }

    public function get_name()
    {
        $title = str_replace(" ", "_", Translation::get('Result') . ' ' . $this->peer_assessment->get_title());

        $safe_name = Filesystem::create_safe_name($title);

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
