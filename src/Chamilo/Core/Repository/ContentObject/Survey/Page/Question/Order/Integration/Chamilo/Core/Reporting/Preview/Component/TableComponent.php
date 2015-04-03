<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Order\Integration\Chamilo\Core\Reporting\Preview\Component;

use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Order\Integration\Chamilo\Core\Reporting\Interfaces\TemplateSupport;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Order\Integration\Chamilo\Core\Reporting\Preview\Manager;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Order\Integration\Chamilo\Core\Reporting\Template\TableTemplate;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;

class TableComponent extends Manager implements TemplateSupport
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
        $viewer->set_template_by_name(TableTemplate :: class_name());
         
        return $viewer->run();
    }

    /*
     * (non-PHPdoc) @see
     * \repository\content_object\survey_order_question\integration\reporting\TemplateSupport::get_answers()
     */
    public function get_answers($survey_order_question_id)
    {
        $answers = array();

        $question = $this->get_question();
        $options = $question->get_options();

        $answer_count = rand(0, 50);

        for ($i = 0; $i <= $answer_count; $i ++)
        {
            $random_match = rand(0, (count($options) - 1));
            $answers[][] = $options[$random_match]->get_id();
        }

        return $answers;
    }

    /*
     * (non-PHPdoc) @see
     * \repository\content_object\survey_order_question\integration\reporting\TemplateSupport::get_question()
     */
    public function get_question()
    {
        return $this->get_content_object();
    }
}