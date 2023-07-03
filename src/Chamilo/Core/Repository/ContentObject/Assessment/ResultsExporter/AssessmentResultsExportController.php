<?php
namespace Chamilo\Core\Repository\ContentObject\Assessment\ResultsExporter;

use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\Storage\DataManager;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\File\ConfigurablePathBuilder;
use Chamilo\Libraries\File\FilesystemTools;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\StringUtilities;
use ReflectionClass;

/**
 * Class that exports the results of an assessment
 *
 * @package repository\content_object\assessment
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
class AssessmentResultsExportController
{
    public const COLUMN_ASSESSMENT_DESCRIPTION = 'assessment_description';
    public const COLUMN_ASSESSMENT_TITLE = 'assessment_title';
    public const COLUMN_ATTEMPT_ANSWER = 'attempt_answer';
    public const COLUMN_ATTEMPT_END_TIME = 'attempt_end_time';
    public const COLUMN_ATTEMPT_SCORE = 'attempt_score';
    public const COLUMN_ATTEMPT_START_TIME = 'attempt_start_time';
    public const COLUMN_ATTEMPT_TOTAL_SCORE = 'attempt_total_score';
    public const COLUMN_ATTEMPT_TOTAL_TIME = 'attempt_total_time';
    public const COLUMN_ATTEMTP_ID = 'attempt_id';
    public const COLUMN_FIRSTNAME = 'firstname';
    public const COLUMN_LASTNAME = 'lastname';
    public const COLUMN_OFFICIAL_CODE = 'official_code';
    public const COLUMN_QUESTION_DESCRIPTION = 'question_description';
    public const COLUMN_QUESTION_ID = 'question_id';
    public const COLUMN_QUESTION_NUMBER = 'question_number';
    public const COLUMN_QUESTION_TITLE = 'question_title';
    public const COLUMN_QUESTION_WEIGHT = 'question_weight';

    /**
     * **************************************************************************************************************
     * Properties *
     * **************************************************************************************************************
     */

    /**
     * Additional column headers that need to be exported
     *
     * @var string[string]
     * @example $additional_column_headers[column_header_id] = column_translation
     */
    private $additional_column_headers;

    /**
     * The assessment results
     *
     * @var AssessmentResult[]
     */
    private $assessment_results;

    /**
     * The assessments that need to be exported
     *
     * @var Assessment[]
     */
    private $assessments;

    /**
     * The export data of one row
     *
     * @var string[]
     */
    private $data_row;

    /**
     * The export data
     *
     * @var string[][]
     */
    private $export_data;

    /**
     * The question results ordered by question id
     *
     * @var QuestionResult[]
     */
    private $question_results_by_question;

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
    public function __construct($assessments, $assessment_results, $additional_column_headers = [])
    {
        $this->assessments = $assessments;
        $this->assessment_results = $assessment_results;
        $this->additional_column_headers = $additional_column_headers;

        $this->data_row = [];
        $this->export_data = [];
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
     * Adds the additional information columns for a single question result
     *
     * @param QuestionResult $question_result
     */
    protected function add_additional_information_columns(QuestionResult $question_result)
    {
        $additional_information = $question_result->get_additional_information();
        if (!is_array($additional_information) || count($additional_information) == 0)
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
     * Adds the current row to the export data
     */
    public function add_current_row_to_export_data()
    {
        if (is_array($this->data_row) && count($this->data_row) > 0)
        {
            $this->export_data[] = $this->data_row;
        }

        $this->data_row = [];
    }

    /**
     * **************************************************************************************************************
     * Getters & Setters *
     * **************************************************************************************************************
     */

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
     * Exports a single assessment
     *
     * @param Assessment | Hotpotatoes $assessment
     */
    protected function export_assessment($assessment)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                ComplexContentObjectItem::class, ComplexContentObjectItem::PROPERTY_PARENT
            ), new StaticConditionVariable($assessment->get_id())
        );

        $complex_questions_resultset =
            \Chamilo\Core\Repository\Storage\DataManager::retrieve_complex_content_object_items(
                ComplexContentObjectItem::class, new DataClassRetrievesParameters($condition)
            );

        foreach ($complex_questions_resultset as $complex_question)
        {
            $this->export_question($complex_question, $assessment);
        }
    }

    /**
     * Exports the headers of the csv file
     */
    protected function export_headers()
    {
        $reflection_class = new ReflectionClass(__CLASS__);

        $constants = $reflection_class->getConstants();
        foreach ($constants as $constant => $value)
        {
            if (strpos($constant, 'COLUMN_') == 0)
            {
                $this->add_data_to_current_row(
                    $constant,
                    Translation::get((string) StringUtilities::getInstance()->createString($value)->upperCamelize())
                );
            }
        }

        foreach ($this->additional_column_headers as $column_header_id => $column_header_translation)
        {
            $this->add_data_to_current_row($column_header_id, $column_header_translation);
        }

        $this->add_current_row_to_export_data();
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
     * **************************************************************************************************************
     * Protected functionality *
     * **************************************************************************************************************
     */

    /**
     * Exports the data for one question result
     *
     * @param QuestionResult $question_result
     * @param ComplexContentObjectItem $complex_question
     * @param Assessment | Hotpotatoes $assessment
     */
    protected function export_question_result(
        QuestionResult $question_result, ComplexContentObjectItem $complex_question, $assessment
    )
    {
        $assessment_result = $question_result->get_assessment_result();
        $user = DataManager::retrieve_by_id(
            User::class, $assessment_result->get_user_id()
        );

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

        $start_time = DatetimeUtilities::getInstance()->formatLocaleDate(null, $assessment_result->get_start_time());

        $end_time =
            is_null($assessment_result->get_end_time()) ? '-' : DateTimeUtilities::getInstance()->formatLocaleDate(
                null, $assessment_result->get_end_time()
            );

        $total_time = DateTimeUtilities::getInstance()->convertSecondsToHours($assessment_result->get_total_time());

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
     * Exports the export data to a csv file
     */
    protected function export_to_csv()
    {

        $path = $this->getConfigurablePathBuilder()->getTemporaryPath();

        if (!file_exists($path))
        {
            $this->getFilesystem()->mkdir($path);
        }

        $path = $path . $this->getFilesystemTools()->createUniqueName(
                $path, 'raw_assessment_export' . date('_Y-m-d_H-i-s') . '.csv'
            );

        $fp = fopen($path, 'w');

        foreach ($this->export_data as $fields)
        {
            fputcsv($fp, $fields, ';');
        }

        fclose($fp);

        return $path;
    }

    public function getConfigurablePathBuilder(): ConfigurablePathBuilder
    {
        return DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(
            ConfigurablePathBuilder::class
        );
    }

    public function getFilesystem(): \Symfony\Component\Filesystem\Filesystem
    {
        return DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(
            \Symfony\Component\Filesystem\Filesystem::class
        );
    }

    public function getFilesystemTools(): FilesystemTools
    {
        return DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(FilesystemTools::class);
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
     * Returns the assessments
     *
     * @return Assessment[]
     */
    public function get_assessments()
    {
        return $this->assessments;
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
     * Sets the assessment results
     *
     * @param AssessmentResult[] $assessment_results
     */
    public function set_assessment_results($assessment_results)
    {
        $this->assessment_results = $assessment_results;
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
}