<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Integration\Chamilo\Core\Reporting\Preview\Component;

use Chamilo\Core\Repository\ContentObject\Survey\Page\Integration\Chamilo\Core\Reporting\Interfaces\TemplateSupport;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Integration\Chamilo\Core\Reporting\Preview\Manager;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Integration\Chamilo\Core\Reporting\Template\TableTemplate;
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
        
        $factory = new ApplicationFactory('\Chamilo\Core\Reporting\Viewer', new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this));
        $viewer = $factory->getComponent();
        $viewer->set_template_by_name(TableTemplate :: class_name());
        
        return $viewer->run();
      
    }

    /*
     * (non-PHPdoc) @see \repository\content_object\survey_page\integration\reporting\TemplateSupport::get_questions()
     */
    public function get_questions($survey_page_id)
    {
        return $this->get_page()->get_questions();
    }

    /*
     * (non-PHPdoc) @see \repository\content_object\survey_page\integration\reporting\TemplateSupport::get_page()
     */
    public function get_page()
    {
        return $this->get_content_object();
    }

    /*
     * (non-PHPdoc) @see
     * \repository\content_object\survey_page\integration\reporting\TemplateSupport::get_question_template_url()
     */
    public function get_question_template_url($question)
    {
        $title = $question->get_title();
        $title_short = $title;
        if (strlen($title_short) > 53)
        {
            $title_short = mb_substr($title_short, 0, 50) . '&hellip;';
        }

        return '<a href="' .
             htmlentities(
                $this->get_url(
                    array(\Chamilo\Core\Repository\Preview\Manager :: PARAM_CONTENT_OBJECT_ID => $question->get_id()))) .
             '" title="' . $title . '">' . $title_short . '</a>';
        ;
    }
}