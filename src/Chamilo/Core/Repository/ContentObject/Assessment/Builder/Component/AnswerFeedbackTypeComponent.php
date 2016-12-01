<?php
namespace Chamilo\Core\Repository\ContentObject\Assessment\Builder\Component;

use Chamilo\Core\Repository\ContentObject\Assessment\Builder\Manager;
use Chamilo\Core\Repository\ContentObject\Assessment\Builder\Table\AnswerFeedbackType\AnswerFeedbackTypeTable;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package core\repository\content_object\assessment\builder
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class AnswerFeedbackTypeComponent extends Manager implements TableSupport
{

    public function run()
    {
        $answer_feedback_type = Request::get(self::PARAM_ANSWER_FEEDBACK_TYPE);
        $complex_question_id = $this->getRequest()->get(self::PARAM_COMPLEX_QUESTION_ID);
        
        if (is_null($complex_question_id) || is_null($answer_feedback_type))
        {
            $table = new AnswerFeedbackTypeTable($this);
            
            $html = array();
            
            $html[] = $this->render_header();
            $html[] = $table->as_html();
            $html[] = $this->render_footer();
            
            return implode(PHP_EOL, $html);
        }
        else
        {
            if (! is_array($complex_question_id))
            {
                $complex_question_id = array($complex_question_id);
            }
            
            $conditions = array();
            $conditions[] = $this->get_table_condition();
            $conditions[] = new InCondition(
                new PropertyConditionVariable(
                    ComplexContentObjectItem::class_name(), 
                    ComplexContentObjectItem::PROPERTY_ID), 
                $complex_question_id);
            $condition = new AndCondition($conditions);
            
            $complex_content_object_items = \Chamilo\Core\Repository\Storage\DataManager::retrieve_complex_content_object_items(
                ComplexContentObjectItem::class_name(), 
                new DataClassRetrievesParameters($condition));
            
            $failures = 0;
            $question_count = $complex_content_object_items->size();
            
            while ($complex_content_object_item = $complex_content_object_items->next_result())
            {
                $complex_content_object_item->set_show_answer_feedback($answer_feedback_type);
                if (! $complex_content_object_item->update())
                {
                    $failures ++;
                }
            }
            
            if ($failures > 0)
            {
                if ($failures == $question_count && $question_count == 1)
                {
                    $message = 'FeedbackOptionNotSuccessfullySet';
                }
                elseif ($failures == $question_count && $question_count > 1)
                {
                    $message = 'FeedbackOptionsNotSuccessfullySet';
                }
                else
                {
                    $message = 'SomeFeedbackOptionsNotSuccessfullySet';
                }
            }
            else
            {
                if ($question_count == 0)
                {
                    $message = 'NoFeedbackOptions';
                }
                elseif ($question_count == 1)
                {
                    $message = 'FeedbackOptionSuccessfullySet';
                }
                else
                {
                    $message = 'FeedbackOptionsSuccessfullySet';
                }
            }
            
            $this->redirect(
                Translation::get($message), 
                false, 
                array(self::PARAM_ACTION => self::ACTION_ANSWER_FEEDBACK_TYPE));
        }
    }

    /**
     *
     * @see \core\repository\builder\Manager::get_table_condition()
     */
    public function get_table_condition($table_class_name)
    {
        return new EqualityCondition(
            new PropertyConditionVariable(
                ComplexContentObjectItem::class_name(), 
                ComplexContentObjectItem::PROPERTY_PARENT), 
            new StaticConditionVariable($this->get_parent_content_object_id()));
    }
}
