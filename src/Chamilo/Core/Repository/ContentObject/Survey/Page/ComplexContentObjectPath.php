<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page;

use Chamilo\Core\Repository\ContentObject\Survey\Page\Display\Interfaces\PageDisplayItem;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Description\Storage\DataClass\ComplexDescription;
use Chamilo\Core\Repository\ContentObject\Survey\Service\AnswerServiceInterface;

/**
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @package repository\content_object\survey_page
 */
class ComplexContentObjectPath extends \Chamilo\Core\Repository\Common\Path\ComplexContentObjectPath
{

    private $question_nr = 0;

    private $invisible_question_nr = 0;

    function get_properties($parent_id, $complex_content_object_item, $content_object)
    {
        $properties = array();
        
        if ($complex_content_object_item instanceof PageDisplayItem &&
             ! ($complex_content_object_item instanceof ComplexDescription))
        {
            
            $properties[ComplexContentObjectPathNode::PROPERTY_QUESTION] = $content_object->get_question();
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
            
            $properties[ComplexContentObjectPathNode::PROPERTY_QUESTION_NR] = $nr;
            $properties[ComplexContentObjectPathNode::PROPERTY_IS_QUESTION] = true;
        }
        else
        {
            $properties[ComplexContentObjectPathNode::PROPERTY_IS_QUESTION] = false;
        }
        
        return $properties;
    }

    /**
     *
     * @param AnswerServiceInterface $answerService
     * @return number
     */
    public function getProgress(AnswerServiceInterface $answerService)
    {
        $nodes = $this->get_nodes();
        
        $questionCount = 0;
        $answerCount = 0;
        foreach ($nodes as $node)
        {
            if ($node->isQuestion())
            {
                if ($node->isVisible($answerService))
                {
                    $questionCount ++;
                    $answer = $answerService->getAnswer($node->get_id());
                    if ($answer)
                    {
                        $answerCount ++;
                    }
                }
            }
        }
        
        $progress = 0;
        if ($questionCount > 0)
        {
            $progress = round($answerCount / $questionCount * 100, 2);
        }
        return $progress;
    }
}