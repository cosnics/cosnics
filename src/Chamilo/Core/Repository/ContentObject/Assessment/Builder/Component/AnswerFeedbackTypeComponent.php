<?php
namespace Chamilo\Core\Repository\ContentObject\Assessment\Builder\Component;

use Chamilo\Core\Repository\ContentObject\Assessment\Builder\Manager;
use Chamilo\Core\Repository\ContentObject\Assessment\Builder\Table\AnswerFeedbackTypeTableRenderer;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Format\Table\RequestTableParameterValuesCompiler;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\OrderProperty;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;

/**
 * @package core\repository\content_object\assessment\builder
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class AnswerFeedbackTypeComponent extends Manager implements TableSupport
{

    public function run()
    {
        $answer_feedback_type = Request::get(self::PARAM_ANSWER_FEEDBACK_TYPE);
        $complex_question_id = $this->getRequest()->get(self::PARAM_COMPLEX_QUESTION_ID);

        if (is_null($complex_question_id) || is_null($answer_feedback_type))
        {
            $html = [];

            $html[] = $this->renderHeader();
            $html[] = $this->renderTable();
            $html[] = $this->renderFooter();

            return implode(PHP_EOL, $html);
        }
        else
        {
            if (!is_array($complex_question_id))
            {
                $complex_question_id = [$complex_question_id];
            }

            $conditions = [];
            $conditions[] = $this->getAnswerFeedbackTypeCondition();
            $conditions[] = new InCondition(
                new PropertyConditionVariable(
                    ComplexContentObjectItem::class, DataClass::PROPERTY_ID
                ), $complex_question_id
            );
            $condition = new AndCondition($conditions);

            $complex_content_object_items = DataManager::retrieve_complex_content_object_items(
                ComplexContentObjectItem::class, new DataClassRetrievesParameters($condition)
            );

            $failures = 0;
            $question_count = $complex_content_object_items->count();

            foreach ($complex_content_object_items as $complex_content_object_item)
            {
                $complex_content_object_item->set_show_answer_feedback($answer_feedback_type);
                if (!$complex_content_object_item->update())
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

            $this->redirectWithMessage(
                Translation::get($message), false, [self::PARAM_ACTION => self::ACTION_ANSWER_FEEDBACK_TYPE]
            );
        }
    }

    public function getAnswerFeedbackTypeCondition(): EqualityCondition
    {
        return new EqualityCondition(
            new PropertyConditionVariable(
                ComplexContentObjectItem::class, ComplexContentObjectItem::PROPERTY_PARENT
            ), new StaticConditionVariable($this->get_parent_content_object_id())
        );
    }

    public function getAnswerFeedbackTypeTableRenderer(): AnswerFeedbackTypeTableRenderer
    {
        return $this->getService(AnswerFeedbackTypeTableRenderer::class);
    }

    public function getRequestTableParameterValuesCompiler(): RequestTableParameterValuesCompiler
    {
        return $this->getService(RequestTableParameterValuesCompiler::class);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \ReflectionException
     * @throws \TableException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     */
    protected function renderTable(): string
    {
        $totalNumberOfItems = DataManager::count(
            ComplexContentObjectItem::class, new DataClassCountParameters($this->getAnswerFeedbackTypeCondition())
        );

        $answerFeedbackTypeTableRenderer = $this->getAnswerFeedbackTypeTableRenderer();

        $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
            $answerFeedbackTypeTableRenderer->getParameterNames(),
            $answerFeedbackTypeTableRenderer->getDefaultParameterValues(), $totalNumberOfItems
        );

        $orderBy = $answerFeedbackTypeTableRenderer->determineOrderBy($tableParameterValues);

        $orderBy->add(
            new OrderProperty(
                new PropertyConditionVariable(
                    ComplexContentObjectItem::class, ComplexContentObjectItem::PROPERTY_DISPLAY_ORDER
                )
            )
        );

        $parameters = new DataClassRetrievesParameters(
            $this->getAnswerFeedbackTypeCondition(), $tableParameterValues->getNumberOfItemsPerPage(),
            $tableParameterValues->getOffset(), $orderBy
        );

        $complexContentObjectItems = DataManager::retrieves(
            ComplexContentObjectItem::class, $parameters
        );

        return $answerFeedbackTypeTableRenderer->render($tableParameterValues, $complexContentObjectItems);
    }
}
