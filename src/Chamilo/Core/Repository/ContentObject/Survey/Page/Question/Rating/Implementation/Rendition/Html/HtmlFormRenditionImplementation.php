<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Rating\Common\Rendition\Html;

use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package repository.content_object.survey_matrix_question
 * @author Eduard Vossen
 * @author Magali Gillard
 * @author Hans De Bisschop
 */
class HtmlFormRenditionImplementation extends \Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Common\Rendition\Html\HtmlFormRenditionImplementation
{

    /**
     *
     * @return \Chamilo\Libraries\Format\Form\FormValidator
     */
    public function initialize()
    {
        $formValidator = parent::initialize();
        $renderer = $formValidator->get_renderer();
        $question = $this->get_content_object();
        
        $questionId = $this->getQuestionId();
        
        if ($this->getPrefix())
        {
            $questionName = $this->getPrefix() . '_' . $questionId;
        }
        else
        {
            $questionName = $questionId;
        }
        
        $tableHeader = array();
        $tableHeader[] = '<table class="table table-striped table-bordered table-hover table-data take_survey">';
        $tableHeader[] = '<thead>';
        $tableHeader[] = '<tr>';
        $tableHeader[] = '<th class="info" >' . Translation::get('ChooseYourRating') . '</th>';
        $tableHeader[] = '</tr>';
        $tableHeader[] = '</thead>';
        $tableHeader[] = '<tbody>';
        $tableHeader[] = '<tr>';
        $tableHeader[] = '<td>';
        $formValidator->addElement('html', implode(PHP_EOL, $tableHeader));
        
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
            $questionName, 
            Translation::get('Rating') . ': ', 
            $scores, 
            'class="rating_slider"');
        
        $tableFooter = array();
        $tableFooter[] = '</td>';
        $tableFooter[] = '</tr>';
        $tableFooter[] = '</tbody>';
        $tableFooter[] = '</table>';
        $formValidator->addElement('html', implode(PHP_EOL, $tableFooter));
        
        $renderer->setElementTemplate($element_template, $questionName);
        $namespace = ClassnameUtilities::getInstance()->getNamespaceParent(__NAMESPACE__, 3);
        $formValidator->addElement(
            'html', 
            ResourceManager::getInstance()->get_resource_html(
                Path::getInstance()->getJavascriptPath($namespace, true) . 'Form.js'));
        return $formValidator;
    }
}