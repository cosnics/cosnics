<?php
namespace Chamilo\Core\Repository\ContentObject\Assessment\ResultsExporter;

use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * Class that exports the results of an assessment
 *
 * @package repository\content_object\assessment
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class AssessmentResultsExportController
{
    /**
     * **************************************************************************************************************
     * COLUMN DEFINITIONS *
     * **************************************************************************************************************
     */
    const COLUMN_OFFICIAL_CODE = 'official_code';
    const COLUMN_FIRSTNAME = 'firstname';
    const COLUMN_LASTNAME = 'lastname';
    const COLUMN_ASSESSMENT_TITLE = 'assessment_title';
    const COLUMN_ASSESSMENT_DESCRIPTION = 'assessment_description';
    const COLUMN_ATTEMTP_ID = 'attempt_id';
    const COLUMN_ATTEMPT_START_TIME = 'attempt_start_time';
    const COLUMN_ATTEMPT_END_TIME = 'attempt_end_time';
    const COLUMN_ATTEMPT_TOTAL_TIME = 'attempt_total_time';
    const COLUMN_ATTEMPT_TOTAL_SCORE = 'attempt_total_score';
    const COLUMN_QUESTION_NUMBER = 'question_number';
    const COLUMN_QUESTION_ID = 'question_id';
    const COLUMN_QUESTION_TITLE = 'question_title';
    const COLUMN_QUESTION_DESCRIPTION = 'question_description';
    const COLUMN_ATTEMPT_ANSWER = 'attempt_answer';
    const COLUMN_ATTEMPT_SCORE = 'attempt_score';
    const COLUMN_QUESTION_WEIGHT = 'question_weight';

    /**
     * **************************************************************************************************************
     * Properties *
     * **************************************************************************************************************
     */

    /**
     * The assessments that need to be exported
     *
     * @var Assessment[]
     */
    private $assessments;

    /**
     * The assessment results
     *
     * @var AssessmentResult[]
     */
    private $assessment_results;

    /**
     * Additional column headers that need to be exported
     *
     * @var string[string]
     *
     * @example $additional_column_headers[column_header_id] = column_translation
     */
    private $additional_column_headers;

    /**
     * The question results ordered by question id
     *
     * @var QuestionResult[]
     */
    private $question_results_by_question;

    /**
     * The export data
     *
     * @var string[][]
     */
    private $export_data;

    /**
     * The export data of one row
     *
     * @var string[]
     */
    private $data_row;

    /**
     * **************************************************************************************************************
     * Public functionality *
     * **************************************************************************************************************
     */

    /**
     * Initializes this class
     *
     * @param Assessment[] | Hotpotatoes[] $assessments
     * @param AssessmentResult[] $assessment_results
     * @param string[] $additional_column_headers
     */
    public function __construct($assessments, $assessment_results, $additional_column_headers = array())
    {
        $this->assessments = $assessments;
        $this->assessment_results = $assessment_results;
        $this->additional_column_headers = $additional_column_headers;

        $this->data_row = array();
        $this->export_data = array();
    }

    /**
     * Runs this controller and returns the path to the assessment results csv
     *
     * @return string
     */
    public function run()
    {
        $this->order_assessment_results_by_question();

        $this->export_headers();

        foreach ($this->assessments as $assessment)
        {
            $this->export_assessment($assessment);
        }

        return $this->export_to_csv();
    }

    /**
     * Adds data to the current row
     *
     * @param string $column
     * @param string $data
     */
    public function add_data_to_current_row($column, $data)
    {
        $this->data_row[$column] = $data;
    }

    /**
     * Adds the current row to the export data
     */
    public function add_current_row_to_export_data()
    {
        if (is_array($this->data_row) && count($this->data_row) > 0)
        {
            $this->export_data[] = $this->data_row;
        }

        $this->data_row = array();
    }

    /**
     * **************************************************************************************************************
     * Getters & Setters *
     * **************************************************************************************************************
     */

    /**
     * Sets the assessment results
     *
     * @param AssessmentResult[] $assessment_results
     */
    public function set_assessment_results($assessment_results)
    {
        $this->assessment_results = $assessment_results;
    }

    /**
     * Returns the assessment results
     *
     * @return AssessmentResult[]
     */
    public function get_assessment_results()
    {
        return $this->assessment_results;
    }

    /**
     * Sets the assessments
     *
     * @param Assessment[] $assessments
     */
    public function set_assessments($assessments)
    {
        $this->assessments = $assessments;
    }

    /**
     * Returns the assessments
     *
     * @return Assessment[]
     */
    public function get_assessments()
    {
        return $this->assessments;
    }

    /**
     * **************************************************************************************************************
     * Protected functionality *
     * **************************************************************************************************************
     */

    /**
     * Exports the headers of the csv file
     */
    protected function export_headers()
    {
        $reflection_class = new \ReflectionClass(__CLASS__);

        $constants = $reflection_class->getConstants();
        foreach ($constants as $constant => $value)
        {
            if (strpos($constant, 'COLUMN_') == 0)
            {
                $this->add_data_to_current_row(
                    $constant,
                    Translation::get((string) StringUtilities::getInstance()->createString($value)->upperCamelize()));
            }
        }

        foreach ($this->additional_column_headers as $column_header_id => $column_header_translation)
        {
            $this->add_data_to_current_row($column_header_id, $column_header_translation);
        }

        $this->add_current_row_to_export_data();
    }

    /**
     * Exports a single assessment
     *
     * @param Assessment | Hotpotatoes $assessment
     */
    protected function export_assessment($assessment)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                ComplexContentObjectItem::class_name(),
                ComplexContentObjectItem::PROPERTY_PARENT),
            new StaticConditionVariable($assessment->get_id()));

        $complex_questions_resultset = \Chamilo\Core\Repository\Storage\DataManager::retrieve_complex_content_object_items(
            ComplexContentObjectItem::class_name(),
            new DataClassRetrievesParameters($condition));

        while ($complex_question = $complex_questions_resultset->next_result())
        {
            $this->export_question($complex_question, $assessment);
        }
    }

    /**
     * Exports the question of an assessment
     *
     * @param ComplexContentObjectItem $complex_question
     * @param Assessment | Hotpotatoes $assessment
     */
    protected function export_question(ComplexContentObjectItem $complex_question, $assessment)
    {
        $question_results = $this->question_results_by_question[$complex_question->get_id()];
        foreach ($question_results as $question_result)
        {
            $this->export_question_result($question_result, $complex_question, $assessment);
        }
    }

    /**
     * Exports the data for one question result
     *
     * @param QuestionResult $question_result
     * @param ComplexContentObjectItem $complex_question
     * @param Assessment | Hotpotatoes $assessment
     */
    protected function export_question_result(QuestionResult $question_result,
        ComplexContentObjectItem $complex_question, $assessment)
    {
        $assessment_result = $question_result->get_assessment_result();
        $user = \Chamilo\Core\User\Storage\DataManager::retrieve_by_id(
            \Chamilo\Core\User\Storage\DataClass\User::class_name(),
            $assessment_result->get_user_id());

        if ($user)
        {
            $first_name = $user->get_firstname();
            $last_name = $user->get_lastname();
            $official_code = $user->get_official_code();
        }
        else
        {
            $first_name = $last_name = Translation::get('UserUnknown');
            $official_code = '-';
        }

        $question = $complex_question->get_ref_object();

        $start_time = DatetimeUtilities::format_locale_date(null, $assessment_result->get_start_time());

        $end_time = is_null($assessment_result->get_end_time()) ? '-' : DateTimeUtilities::format_locale_date(
            null,
            $assessment_result->get_end_time());

        $total_time = DateTimeUtilities::convert_seconds_to_hours($assessment_result->get_total_time());

        $this->add_data_to_current_row(self::COLUMN_OFFICIAL_CODE, $official_code);
        $this->add_data_to_current_row(self::COLUMN_FIRSTNAME, $first_name);
        $this->add_data_to_current_row(self::COLUMN_LASTNAME, $last_name);
        $this->add_data_to_current_row(self::COLUMN_ASSESSMENT_TITLE, $assessment->get_title());
        $this->add_data_to_current_row(self::COLUMN_ASSESSMENT_DESCRIPTION, $assessment->get_description());
        $this->add_data_to_current_row(self::COLUMN_ATTEMTP_ID, $assessment_result->get_result_id());
        $this->add_data_to_current_row(self::COLUMN_ATTEMPT_START_TIME, $start_time);
        $this->add_data_to_current_row(self::COLUMN_ATTEMPT_END_TIME, $end_time);
        $this->add_data_to_current_row(self::COLUMN_ATTEMPT_TOTAL_TIME, $total_time);
        $this->add_data_to_current_row(self::COLUMN_ATTEMPT_TOTAL_SCORE, $assessment_result->get_total_score());
        $this->add_data_to_current_row(self::COLUMN_QUESTION_NUMBER, $complex_question->get_display_order());
        $this->add_data_to_current_row(self::COLUMN_QUESTION_ID, $question->get_id());
        $this->add_data_to_current_row(self::COLUMN_QUESTION_TITLE, $question->get_title());
        $this->add_data_to_current_row(self::COLUMN_QUESTION_DESCRIPTION, $question->get_description());

        QuestionResultExportImplementation::launch($complex_question, $this, $question_result);

        $this->add_data_to_current_row(self::COLUMN_ATTEMPT_SCORE, $question_result->get_score());
        $this->add_data_to_current_row(self::COLUMN_QUESTION_WEIGHT, $complex_question->get_weight());

        $this->add_additional_information_columns($question_result);

        $this->add_current_row_to_export_data();
    }

    /**
     * Adds the additional information columns for a single question result
     *
     * @param QuestionResult $question_result
     */
    protected function add_additional_information_columns(QuestionResult $question_result)
    {
        $additional_information = $question_result->get_additional_information();
        if (! is_array($additional_information) || count($additional_information) == 0)
        {
            return;
        }

        foreach ($this->additional_column_headers as $column_header_id => $column_translation)
        {
            if (array_key_exists($column_header_id, $additional_information))
            {
                $this->add_data_to_current_row($column_header_id, $additional_information[$column_header_id]);
            }
        }
    }

    /**
     * Orders the given assessment results by question
     */
    protected function order_assessment_results_by_question()
    {
        foreach ($this->assessment_results as $assessment_result)
        {
            foreach ($assessment_result->get_question_results() as $question_result)
            {
                $this->question_results_by_question[$question_result->get_complex_question_id()][] = $question_result;
            }
        }
    }

    /**
     * Exports the export data to a csv file
     */
    protected function export_to_csv()
    {
        $path = Path::getInstance()->getTemporaryPath();

        if (! file_exists($path))
        {
            Filesystem::create_dir($path);
        }

        $path = $path . Filesystem::create_unique_name($path, 'raw_assessment_export' . date('_Y-m-d_H-i-s') . '.csv');

        $fp = fopen($path, 'w');

        foreach ($this->export_data as $fields)
        {
            fputcsv($fp, $fields, ';');
        }

        fclose($fp);

        return $path;
    }
}