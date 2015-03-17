<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Matching\Integration\Chamilo\Core\Reporting\Preview\Component;

use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Matching\Integration\Chamilo\Core\Reporting\Preview\Manager;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Matching\Integration\Chamilo\Core\Reporting\TemplateSupport;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Matching\Integration\Chamilo\Core\Reporting\Template\GraphTemplate;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;

class GraphComponent extends Manager implements TemplateSupport
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $question = $this->get_parent()->get_content_object();

        if ((! $this->get_user()->is_platform_admin()) || ($this->get_user_id() != $question->get_owner_id()))
        {
            throw new NotAllowedException();
        }

        \Chamilo\Core\Reporting\Viewer\Manager :: launch($this, GraphTemplate :: class_name());
    }

    // /*
    // * (non-PHPdoc) @see
    // * \repository\content_object\survey_matching_question\integration\reporting\TemplateSupport::get_answers()
    // */
    // public function get_answers($survey_matching_question_id)
    // {
    // return $this->get_answers($survey_matching_question_id);
    // }

    /*
     * (non-PHPdoc) @see
     * \repository\content_object\survey_matching_question\integration\reporting\TemplateSupport::get_question()
     */
    public function get_question()
    {
        return $this->get_content_object();
    }
}