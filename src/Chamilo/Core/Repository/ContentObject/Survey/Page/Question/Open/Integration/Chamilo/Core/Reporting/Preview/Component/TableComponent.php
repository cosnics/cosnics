<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Open\Integration\Chamilo\Core\Reporting\Preview\Component;

use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Open\Integration\Chamilo\Core\Reporting\Preview\Manager;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Open\Integration\Chamilo\Core\Reporting\Interfaces\TemplateSupport;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Open\Integration\Chamilo\Core\Reporting\Template\TableTemplate;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;

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
        
        $factory = new ApplicationFactory(
            '\Chamilo\Core\Reporting\Viewer', 
            new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this));
        $viewer = $factory->getComponent();
        $viewer->set_template_by_name(TableTemplate::class_name());
        
        return $viewer->run();
    }

    /*
     * (non-PHPdoc) @see
     * \repository\content_object\survey_open_question\integration\reporting\TemplateSupport::get_answers()
     */
    public function get_answers($survey_open_question_id)
    {
        $answers = array();
        $answer_count = rand(0, 50);
        
        for ($i = 0; $i <= $answer_count; $i ++)
        {
            $answers[] = $this->generateRandomString(rand(20, 100));
        }
        
        return $answers;
    }

    private function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $length; $i ++)
        {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }

    /*
     * (non-PHPdoc) @see
     * \repository\content_object\survey_open_question\integration\reporting\TemplateSupport::get_question()
     */
    public function get_question()
    {
        return $this->get_content_object();
    }
}