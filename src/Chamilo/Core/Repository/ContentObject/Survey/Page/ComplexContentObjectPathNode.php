<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page;

use Chamilo\Core\Repository\ContentObject\Survey\Service\AnswerServiceInterface;

/**
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @package repository\content_object\survey_page
 */
class ComplexContentObjectPathNode extends \Chamilo\Core\Repository\Common\Path\ComplexContentObjectPathNode
{
    const DATA_NODE_ID = 'data-node_id';
    const PROPERTY_QUESTION = 'question';
    const PROPERTY_QUESTION_NR = 'question_nr';
    const PROPERTY_IS_QUESTION = 'is_question';
    const PROPERTY_QUESTION_MAX_ANSWER_COUNT = 'max_question_answer_count';
    const PROPERTY_NODE_IN_MENU = 'node_in_menu';

    function set_next_page_step($step)
    {
        return $this->set_property(self::PROPERTY_NEXT_PAGE_STEP, $step);
    }

    function set_question($question)
    {
        return $this->set_property(self::PROPERTY_QUESTION, $question);
    }

    function get_question()
    {
        return $this->get_property(self::PROPERTY_QUESTION);
    }

    function set_question_nr($question_nr)
    {
        return $this->set_property(self::PROPERTY_QUESTION_NR, $question_nr);
    }

    function get_question_nr()
    {
        return $this->get_property(self::PROPERTY_QUESTION_NR);
    }

    function setIsQuestion($question)
    {
        return $this->set_property(self::PROPERTY_IS_QUESTION, $question);
    }

    function isQuestion()
    {
        return $this->get_property(self::PROPERTY_IS_QUESTION);
    }

    function set_question_max_answer_count($max_answer_count)
    {
        return $this->set_property(self::PROPERTY_QUESTION_MAX_ANSWER_COUNT, $max_answer_count);
    }

    function get_question_max_answer_count()
    {
        return $this->get_property(self::PROPERTY_QUESTION_MAX_ANSWER_COUNT);
    }

    /**
     *
     * @param AnswerServiceInterface $answerService
     */
    public function isVisible(AnswerServiceInterface $answerService)
    {
        $visible = $this->get_complex_content_object_item()->is_visible();
        $siblingAnswers = $this->getSiblingAnswers($answerService);
        
        if (count($siblingAnswers) > 0)
        {
            $configs = $this->get_complex_content_object_item()->get_parent_object()->getConfiguration();
            $visibleCheck = false;
            
            foreach ($siblingAnswers as $complexQuestionId => $answers)
            {
                if ($visibleCheck)
                {
                    break;
                }
                foreach ($configs as $configuration)
                {
                    if ($visibleCheck)
                    {
                        break;
                    }
                    foreach ($configuration->getToVisibleQuestionIds() as $id)
                    {
                        if ($visibleCheck)
                        {
                            break;
                        }
                        if ($this->get_complex_content_object_item()->get_id() == $id && ! $visibleCheck)
                        {
                            $fromQuestionId = $configuration->getComplexQuestionId();
                            if ($complexQuestionId == $fromQuestionId && ! $visibleCheck)
                            {
                                $answerMatches = $configuration->getAnswerMatches($answerService->getPrefix());
                                
                                if (count($answerMatches) == count($answers))
                                {
                                    foreach ($answerMatches as $key => $value)
                                    {
                                        if (array_key_exists($key, $answers))
                                        {
                                            if ($value == $answers[$key])
                                            {
                                                $visibleCheck = true;
                                            }
                                            else
                                            {
                                                $visibleCheck = false;
                                                break;
                                            }
                                        }
                                        else
                                        {
                                            $visibleCheck = false;
                                            break;
                                        }
                                    }
                                    
                                    if ($visibleCheck)
                                    {
                                        $visible = $visibleCheck;
                                        break;
                                    }
                                    else
                                    {
                                        continue;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return $visible;
    }

    public function getSiblingVisibility(AnswerServiceInterface $answerService)
    {
        $nodes = $this->get_siblings();
        
        $nodeVisibility = array();
        $nodeAnswers = array();
        
        foreach ($nodes as $node)
        {
            $complex_content_object_item = $node->get_complex_content_object_item();
            $complex_question_id = $complex_content_object_item->get_id();
            
            if ($complex_content_object_item->is_visible())
            {
                
                $nodeVisibility[$node->get_id()] = true;
            }
            else
            {
                $nodeVisibility[$node->get_id()] = false;
            }
            $nodeAnswers[] = $answerService->getAnswer($node->get_id());
        }
        
        $nodeAnswers[] = $answerService->getAnswer($this->get_id());
        
        foreach ($nodeAnswers as $nodeAnswer)
        {
            if ($nodeAnswer)
            {
                $configs = $this->get_complex_content_object_item()->get_parent_object()->getConfiguration();
                
                foreach ($configs as $configuration)
                {
                    foreach ($configuration->getToVisibleQuestionIds() as $id)
                    {
                        foreach ($nodes as $node)
                        {
                            if ($node->get_complex_content_object_item()->get_id() == $id)
                            {
                                $answerMatches = $configuration->getAnswerMatches($answerService->getPrefix());
                                
                                $visible = false;
                                if (count($answerMatches) == count($nodeAnswer))
                                {
                                    foreach ($answerMatches as $key => $value)
                                    {
                                        if (array_key_exists($key, $nodeAnswer))
                                        {
                                            if ($value == $nodeAnswer[$key])
                                            {
                                                $visible = true;
                                            }
                                            else
                                            {
                                                $visible = false;
                                                break;
                                            }
                                        }
                                        else
                                        {
                                            $visible = false;
                                            break;
                                        }
                                    }
                                }
                                
                                if ($visible)
                                {
                                    $nodeIdMapping = $this->getSiblingNodeIdMapping();
                                    foreach ($configuration->getToVisibleQuestionIds() as $id)
                                    {
                                        $nodeId = $nodeIdMapping[$id];
                                        $nodeVisibility[$nodeId] = true;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        
        return $nodeVisibility;
    }

    private function getSiblingNodeIdMapping()
    {
        $nodeIdMapping = array();
        
        $nodes = $this->get_siblings();
        
        foreach ($nodes as $node)
        {
            $complexQuestionId = $node->get_complex_content_object_item()->get_id();
            $nodeIdMapping[$complexQuestionId] = $node->get_id();
        }
        
        return $nodeIdMapping;
    }

    /**
     *
     * @param AnswerServiceInterface $answerService
     * @return mixed
     */
    private function getSiblingAnswers(AnswerServiceInterface $answerService)
    {
        $nodes = $this->get_siblings();
        
        $nodeAnswers = array();
        
        foreach ($nodes as $node)
        {
            $answer = $answerService->getAnswer($node->get_id());
            
            if ($answer)
            {
                $nodeAnswers[$node->get_complex_content_object_item()->get_id()] = $answer;
            }
        }
        
        return $nodeAnswers;
    }

    function getDataAttributes()
    {
        $attributes = array();
        $attributes[self::DATA_NODE_ID] = $this->get_id();
        $complexAttributes = $this->get_complex_content_object_item()->getDataAttributes();
        if ($complexAttributes)
        {
            $attributes = array_merge($attributes, $complexAttributes);
        }
        return $attributes;
    }
}