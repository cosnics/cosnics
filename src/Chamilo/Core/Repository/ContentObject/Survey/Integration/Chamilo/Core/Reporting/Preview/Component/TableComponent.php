<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Integration\Chamilo\Core\Reporting\Preview\Component;

use Chamilo\Core\Repository\ContentObject\Survey\Builder\Manager;
use Chamilo\Core\Repository\ContentObject\Survey\Integration\Chamilo\Core\Reporting\Interfaces\TemplateSupport;
use Chamilo\Core\Repository\ContentObject\Survey\Integration\Chamilo\Core\Reporting\Template\TableTemplate;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;

class TableComponent extends Manager implements TemplateSupport
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $survey = $this->get_survey();

        if ((! $this->get_user()->is_platform_admin()) || ($this->get_user_id() != $survey->get_owner_id()))
        {
            throw new NotAllowedException();
        }

        \Chamilo\Core\Reporting\Viewer\Manager :: launch($this, TableTemplate :: class_name());
    }

    /*
     * (non-PHPdoc) @see \repository\content_object\survey\integration\reporting\TemplateSupport::get_pages()
     */
    public function get_pages($survey_id)
    {
        return $this->get_survey()->get_pages();
    }

    /*
     * (non-PHPdoc) @see \repository\content_object\survey\integration\reporting\TemplateSupport::get_survey()
     */
    public function get_survey()
    {
        return $this->get_content_object();
    }

    /*
     * (non-PHPdoc) @see
     * \repository\content_object\survey\integration\reporting\TemplateSupport::get_page_template_url()
     */
    public function get_page_template_url($page)
    {
        $title = $page->get_title();
        $title_short = $title;
        if (strlen($title_short) > 53)
        {
            $title_short = mb_substr($title_short, 0, 50) . '&hellip;';
        }

        return '<a href="' . htmlentities(
            $this->get_url(
                array(\Chamilo\Core\Repository\Preview\Manager :: PARAM_CONTENT_OBJECT_ID => $page->get_id()))) .
             '" title="' . $title . '">' . $title_short . '</a>';
        ;
    }
}