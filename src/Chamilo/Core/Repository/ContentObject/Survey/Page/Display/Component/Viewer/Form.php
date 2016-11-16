<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Display\Component\Viewer;

use Chamilo\Core\Repository\ContentObject\Survey\Display\PageDisplay;
use Chamilo\Libraries\Format\Form\FormValidator;

class Form extends FormValidator
{
    const FORM_NAME = 'survey_page_viewer_form';

    private $parent;

    function __construct($parent)
    {
        parent::__construct(self::FORM_NAME, 'post');
        $this->parent = $parent;
        $this->buildForm();
    }

    function buildForm()
    {
        $surveyConfiguration = $this->parent->getApplicationConfiguration();
        $pageDisplay = PageDisplay::factory(
            $this, 
            $this->parent->get_current_complex_content_object_path_node(), 
            $surveyConfiguration->getAnswerService());
        $pageDisplay->run();
    }

    public function getParent()
    {
        return $this->parent;
    }
}
?>