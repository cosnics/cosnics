<?php
namespace Chamilo\Core\Repository\ContentObject\Survey;

use Chamilo\Core\Repository\ContentObject\Survey\Display\Interfaces\SurveyDisplayItem;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Description\Storage\DataClass\ComplexDescription;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Storage\DataClass\ComplexPage;
use Chamilo\Core\Repository\ContentObject\Survey\Storage\DataClass\ComplexSurvey;

/**
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @package repository\content_object\survey
 */
class ComplexContentObjectPath extends \Chamilo\Core\Repository\ContentObject\Survey\Page\ComplexContentObjectPath
{

    private $question_nr = 0;

    private $invisible_question_nr = 0;

    private $question_step_count = 0;

    private $step_count = 0;

    private $previous_page_step = 0;

    private $page_step_count = 0;

    function get_properties($parent_id, $complex_content_object_item, $content_object)
    {
        $properties = array();
        $this->step_count ++;
        
        if ($complex_content_object_item instanceof SurveyDisplayItem)
        {
            
            if (! ($complex_content_object_item instanceof ComplexDescription ||
                 $complex_content_object_item instanceof ComplexPage ||
                 $complex_content_object_item instanceof ComplexSurvey))
            {
                
                if ($complex_content_object_item->is_visible())
                {
                    $this->question_nr ++;
                    $this->invisible_question_nr = 0;
                    $nr = $this->question_nr;
                }
                else
                {
                    $this->invisible_question_nr ++;
                    $nr = $this->question_nr . '.' . $this->invisible_question_nr;
                }
                
                $properties[ComplexContentObjectPathNode :: PROPERTY_QUESTION_NR] = $nr;
                $properties[ComplexContentObjectPathNode :: PROPERTY_NODE_IN_MENU] = false;
                $properties[ComplexContentObjectPathNode :: PROPERTY_IS_QUESTION] = true;
                $this->question_step_count ++;
            }
            else
            {
                if (! ($complex_content_object_item instanceof ComplexDescription ||
                     $complex_content_object_item instanceof ComplexSurvey))
                {
                    $properties[ComplexContentObjectPathNode :: PROPERTY_NODE_IN_MENU] = true;
                    
                    $this->previous_page_step = $this->step_count - $this->question_step_count - 1;
                    $this->question_step_count = 0;
                    $properties[ComplexContentObjectPathNode :: PROPERTY_PREVIOUS_PAGE_STEP] = $this->previous_page_step;
                    $this->get_node($this->previous_page_step)->set_next_page_step($this->step_count + 1);
                    
                    $this->page_step_count ++;
                    $this->get_node(1)->set_page_step_count($this->page_step_count + 1);
                    $this->get_node(1)->set_last_page_step($this->step_count + 1);
                }
                else
                {
                    $this->question_step_count ++;
                    $properties[ComplexContentObjectPathNode :: PROPERTY_NODE_IN_MENU] = false;
                    $properties[ComplexContentObjectPathNode :: PROPERTY_IS_QUESTION] = false;
                }
            }
        }
        
        return $properties;
    }

    public function reset()
    {
        $this->question_nr = 0;
        $this->invisible_question_nr = 0;
        $this->question_step_count = 0;
        $this->step_count = 0;
        $this->previous_page_step = 0;
        $this->page_step_count = 0;
        parent :: reset();
    }
  
}
