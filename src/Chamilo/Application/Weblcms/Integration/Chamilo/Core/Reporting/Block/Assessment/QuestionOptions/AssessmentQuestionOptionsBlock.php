<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Assessment\QuestionOptions;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Assessment\AssessmentBlock;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssessmentAttempt;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\QuestionAttempt;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * Abstract class to show the options of an assessment question
 *
 * @package application.weblcms.integration.reporting
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class AssessmentQuestionOptionsBlock extends AssessmentBlock
{

    /**
     * The complex content object item for the question
     *
     * @var ComplexContentObjectItem
     */
    private $question_complex_content_object_item;

    /**
     * The Question
     *
     * @var mixed
     */
    private $question;

    /**
     * The attempts on the specific question
     *
     * @var ResultSet
     */
    private $question_attempts;

    /**
     * Constructor
     *
     * @param mixed $question
     * @param ComplexContentObjectItem $complex_content_object_item
     * @param $parent
     * @param bool $vertical
     *
     * @return \application\weblcms\integration\core\reporting\AssessmentQuestionOptionsBlock
     */
    public function __construct($question, $complex_content_object_item, $parent, $vertical = false)
    {
        parent :: __construct($parent, $vertical);

        $this->set_question($question);
        $this->set_question_complex_content_object_item($complex_content_object_item);

        $this->question_attempts = $this->get_question_attempts_from_publication_and_question(
            $this->get_publication_id(),
            $complex_content_object_item->get_id());
    }

    /**
     * Retrieves the question from the parameters and instantiates the correct subclass
     *
     * @param mixed $parent
     *
     * @return self
     */
    public static function factory($parent)
    {
        $question_complex_content_object_item_id = Request :: get(
            \Chamilo\Application\Weblcms\Tool\Implementation\Reporting\Manager :: PARAM_QUESTION);
        $question_complex_content_object_item = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_by_id(
            ComplexContentObjectItem :: class_name(),
            $question_complex_content_object_item_id);

        $question = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_by_id(
            ContentObject :: class_name(),
            $question_complex_content_object_item->get_ref());

        $type = (string) StringUtilities :: getInstance()->createString($question->get_type_name())->upperCamelize();
        $class = __NAMESPACE__ . '\\' . $type . 'OptionsBlock';

        return new $class($question, $question_complex_content_object_item, $parent);
    }

    /**
     * Retrieves the data
     *
     * @return ReportingData
     */
    public function retrieve_data()
    {
        return $this->count_data();
    }

    /**
     * Returns the view of the block
     *
     * @return array
     */
    public function get_views()
    {
        return array(\Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html :: VIEW_TABLE);
    }

    /**
     * Returns the question
     *
     * @return mixed
     */
    protected function get_question()
    {
        return $this->question;
    }

    /**
     * Sets the question
     *
     * @param mixed $question
     */
    protected function set_question($question)
    {
        $this->question = $question;
    }

    /**
     * Returns the complex content object item for the question
     *
     * @return ComplexContentObjectItem
     */
    protected function get_question_complex_content_object_item()
    {
        return $this->question_complex_content_object_item;
    }

    /**
     * Sets the complex content object item for the question
     *
     * @param ComplexContentObjectItem $question_complex_content_object_item
     */
    protected function set_question_complex_content_object_item($question_complex_content_object_item)
    {
        $this->question_complex_content_object_item = $question_complex_content_object_item;
    }

    /**
     * Adds the option headers
     *
     * @param ReportingData $reporting_data
     */
    protected function add_option_headers($reporting_data)
    {
        $reporting_data->set_rows(
            array(
                Translation :: get('Answer'),
                Translation :: get('Correct'),
                Translation :: get('TimesChosen'),
                Translation :: get('DifficultyIndex')));
    }

    /**
     * Adds the data for an option
     *
     * @param ReportingData $reporting_data
     * @param int $row_count
     * @param string $option
     * @param bool $correct
     * @param int $times_chosen
     * @param int $total_attempts
     */
    protected function add_option_data($reporting_data, $row_count, $option, $correct, $times_chosen, $total_attempts)
    {
        $reporting_data->add_category($row_count);

        $reporting_data->add_data_category_row($row_count, Translation :: get('Answer'), $option);

        $reporting_data->add_data_category_row(
            $row_count,
            Translation :: get('Correct'),
            $correct ? Theme :: getInstance()->getCommonImage('Status/ConfirmMini') : '');

        $reporting_data->add_data_category_row(
            $row_count,
            Translation :: get('TimesChosen'),
            $times_chosen ? $times_chosen : 0);

        if ($correct)
        {
            $reporting_data->add_data_category_row(
                $row_count,
                Translation :: get('DifficultyIndex'),
                $this->render_difficulty_index($times_chosen, $total_attempts));
        }
    }

    /**
     * Returns a list of the answers that are extracted from the question attempts
     *
     * @return mixed[]
     */
    protected function get_answers_count_from_attempts()
    {
        $answers = array();

        while ($question_attempt = $this->question_attempts->next_result())
        {
            $assessment_attempt = $question_attempt->get_optional_property(self :: PROPERTY_ASSESSMENT_ATTEMPT);

            if ($assessment_attempt->get_status() == AssessmentAttempt :: STATUS_NOT_COMPLETED)
            {
                continue;
            }

            $this->get_answers_count_from_attempt($question_attempt, $answers);
        }

        $this->question_attempts->reset();

        return $answers;
    }

    /**
     * Returns the answer from the attempt
     *
     * @param QuestionAttempt $attempt
     * @param mixed[] $answers
     */
    protected function get_answers_count_from_attempt(QuestionAttempt $attempt, &$answers)
    {
        $answer_array = unserialize($attempt->get_answer());
        foreach ($answer_array as $answer)
        {
            $answers[$answer] ++;
        }
    }

    /**
     * Counts the number of attempts
     *
     * @return int
     */
    protected function get_total_attempts()
    {
        return $this->question_attempts->size();
    }

    /**
     * Returns the attempts
     *
     * @return ResultSet
     */
    protected function get_attempts()
    {
        return $this->question_attempts;
    }
}