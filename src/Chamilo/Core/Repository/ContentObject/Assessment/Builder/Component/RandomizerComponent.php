<?php
namespace Chamilo\Core\Repository\ContentObject\Assessment\Builder\Component;

use Chamilo\Core\Repository\ContentObject\Assessment\Builder\Manager;
use Chamilo\Core\Repository\ContentObject\AssessmentMatchingQuestion\Storage\DataClass\ComplexAssessmentMatchingQuestion;
use Chamilo\Core\Repository\ContentObject\AssessmentMatrixQuestion\Storage\DataClass\ComplexAssessmentMatrixQuestion;
use Chamilo\Core\Repository\ContentObject\AssessmentMultipleChoiceQuestion\Storage\DataClass\ComplexAssessmentMultipleChoiceQuestion;
use Chamilo\Core\Repository\ContentObject\AssessmentSelectQuestion\Storage\DataClass\ComplexAssessmentSelectQuestion;
use Chamilo\Core\Repository\ContentObject\FillInBlanksQuestion\Storage\DataClass\ComplexFillInBlanksQuestion;
use Chamilo\Core\Repository\ContentObject\HotspotQuestion\Storage\DataClass\ComplexHotspotQuestion;
use Chamilo\Core\Repository\ContentObject\OrderingQuestion\Storage\DataClass\ComplexOrderingQuestion;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

class RandomizerComponent extends Manager
{

    public function run()
    {
        $complex_content_object_items = DataManager::retrieve_complex_content_object_items(
            ComplexContentObjectItem::class, 
            new DataClassRetrievesParameters(
                new EqualityCondition(
                    new PropertyConditionVariable(
                        ComplexContentObjectItem::class, 
                        ComplexContentObjectItem::PROPERTY_PARENT), 
                    new StaticConditionVariable($this->get_parent_content_object_id()))));
        
        $supported_types = array(
            ComplexFillInBlanksQuestion::class,
            ComplexHotspotQuestion::class,
            ComplexAssessmentMatchingQuestion::class,
            ComplexAssessmentMatrixQuestion::class,
            ComplexAssessmentMultipleChoiceQuestion::class,
            ComplexOrderingQuestion::class,
            ComplexAssessmentSelectQuestion::class);
        
        $failures = 0;
        $questions = 0;
        
        while ($complex_content_object_item = $complex_content_object_items->next_result())
        {
            if (in_array($complex_content_object_item->class_name(), $supported_types))
            {
                $questions ++;
                
                if (! $complex_content_object_item->get_random())
                {
                    $complex_content_object_item->set_random(1);
                    if (! $complex_content_object_item->update())
                    {
                        $failures ++;
                    }
                }
            }
        }
        
        if ($failures > 0)
        {
            if ($failures == $questions && $questions == 1)
            {
                $message = 'QuestionNotSuccessfullyRandomized';
            }
            elseif ($failures == $questions && $questions > 1)
            {
                $message = 'QuestionsNotSuccessfullyRandomized';
            }
            else
            {
                $message = 'SomeQuestionsNotSuccessfullyRandomized';
            }
        }
        else
        {
            if ($questions == 0)
            {
                $message = 'NoQuestions';
            }
            elseif ($questions == 1)
            {
                $message = 'QuestionSuccessfullyRandomized';
            }
            else
            {
                $message = 'QuestionsSuccessfullyRandomized';
            }
        }
        
        $this->redirect(
            Translation::get($message), 
            false, 
            array(
                self::PARAM_ACTION => self::ACTION_BROWSE, 
                self::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->get_complex_content_object_item_id()));
    }
}
