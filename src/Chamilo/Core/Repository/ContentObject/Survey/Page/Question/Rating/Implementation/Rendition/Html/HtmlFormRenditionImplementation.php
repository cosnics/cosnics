<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Rating\Implementation\Rendition\Html;

use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\File\Path;

/**
 *
 * @package repository.content_object.survey_matrix_question
 * @author Eduard Vossen
 * @author Magali Gillard
 * @author Hans De Bisschop
 */
class HtmlFormRenditionImplementation extends \Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Implementation\Rendition\Html\HtmlFormRenditionImplementation
{

    /**
     *
     * @return \Chamilo\Libraries\Format\Form\FormValidator
     */
    public function initialize()
    {
        $formValidator = parent :: initialize();
        $renderer = $formValidator->get_renderer();
        $question = $this->get_content_object();
        
        $questionId = $this->getQuestionId();
        
        $table_header = array();
        $table_header[] = '<table class="data_table take_survey">';
        $table_header[] = '<thead>';
        $table_header[] = '<tr>';
        $table_header[] = '<th class="info" >' . Translation :: get('ChooseYourRating') . '</th>';
        $table_header[] = '</tr>';
        $table_header[] = '</thead>';
        $table_header[] = '<tbody>';
        $table_header[] = '<tr>';
        $table_header[] = '<td>';
        $formValidator->addElement('html', implode(PHP_EOL, $table_header));
        
        $min = $question->get_low();
        $max = $question->get_high();
        
        for ($i = $min; $i <= $max; $i ++)
        {
            $scores[$i] = $i;
        }
        
        $element_template = array();
        $element_template[] = '<div><!-- BEGIN error --><span class="form_error">{error}</span><br /><!-- END error -->	{element}';
        $element_template[] = '<div class="clear">&nbsp;</div>';
        $element_template[] = '<div class="form_feedback"></div>';
        $element_template[] = '<div class="clear">&nbsp;</div>';
        $element_template[] = '</div>';
        $element_template = implode(PHP_EOL, $element_template);
        
        $formValidator->addElement(
            'select', 
            $questionId, 
            Translation :: get('Rating') . ': ', 
            $scores, 
            'class="rating_slider"');
        
        $renderer->setElementTemplate($element_template, $questionId);
        $namespace = ClassnameUtilities :: getInstance()->getNamespaceParent(__NAMESPACE__, 3);
        $formValidator->addElement(
            'html', 
            ResourceManager :: get_instance()->get_resource_html(
                Path :: getInstance()->getJavascriptPath($namespace, true) . 'Form.js'));
        return $formValidator;
    }
   
}