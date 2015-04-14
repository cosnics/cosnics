<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Matrix\Integration\Chamilo\Core\Reporting\Preview\Component;

use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Matrix\Integration\Chamilo\Core\Reporting\Preview\Manager;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Matrix\Integration\Chamilo\Core\Reporting\Interfaces\TemplateSupport;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Matrix\Integration\Chamilo\Core\Reporting\Template\GraphTemplate;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;

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

        $factory = new ApplicationFactory($this->getRequest(), '\Chamilo\Core\Reporting\Viewer', $this->get_user(), $this);
        $viewer = $factory->getComponent();
        $viewer->set_template_by_name(GraphTemplate :: class_name());
        
        return $viewer->run();

    }

    /*
     * (non-PHPdoc) @see
     * \repository\content_object\survey_matrix_question\integration\reporting\TemplateSupport::get_answers()
     */
    public function get_answers($survey_matrix_question_id)
    {
        $answers = array();

        $question = $this->get_question();
        $options = $question->get_options()->as_array();
        $matches = $question->get_matches()->as_array();

        $answer_count = rand(0, 50);

        for ($i = 0; $i <= $answer_count; $i ++)
        {
            foreach ($options as $option)
            {
                $id = $i . '_' . $option->get_id();
                $random_match = rand(0, (count($matches) - 1));
                $answers[] = array($id => $matches[$random_match]->get_id());
            }
        }

        return $answers;
    }

    /*
     * (non-PHPdoc) @see
     * \repository\content_object\survey_matrix_question\integration\reporting\TemplateSupport::get_question()
     */
    public function get_question()
    {
        return $this->get_content_object();
    }
}