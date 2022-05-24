<?php
namespace Chamilo\Core\Repository\ContentObject\Assessment\Storage\DataClass;

use Chamilo\Configuration\Configuration;
use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Interfaces\ComplexContentObjectSupport;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * This class represents an assessment
 *
 * @package repository.lib.content_object.assessment
 */
class Assessment extends ContentObject implements ComplexContentObjectSupport
{
    const PROPERTY_AVERAGE_SCORE = 'average_score';
    const PROPERTY_MAXIMUM_ATTEMPTS = 'max_attempts';
    const PROPERTY_MAXIMUM_SCORE = 'maximum_score';
    const PROPERTY_MAXIMUM_TIME = 'max_time';
    const PROPERTY_QUESTIONS_PER_PAGE = 'questions_per_page';
    const PROPERTY_RANDOM_QUESTIONS = 'random_questions';
    const PROPERTY_TIMES_TAKEN = 'times_taken';

    /**
     * The number of questions in this assessment
     *
     * @var int
     */
    private $question_count;

    /**
     * An DataClassCollection containing all ComplexContentObjectItem objects for individual questions.
     *
     * @var \Chamilo\Libraries\Storage\Iterator\DataClassCollection
     */
    private $questions;

    public function count_questions()
    {
        if (!isset($this->question_count))
        {
            $this->question_count = DataManager::count_complex_content_object_items(
                ComplexContentObjectItem::class, new DataClassCountParameters(
                    new EqualityCondition(
                        new PropertyConditionVariable(
                            ComplexContentObjectItem::class, ComplexContentObjectItem::PROPERTY_PARENT
                        ), new StaticConditionVariable($this->get_id()), ComplexContentObjectItem::getTableName()
                    )
                )
            );
        }

        return $this->question_count;
    }

    public static function getAdditionalPropertyNames(): array
    {
        return array(
            self::PROPERTY_MAXIMUM_ATTEMPTS,
            self::PROPERTY_QUESTIONS_PER_PAGE,
            self::PROPERTY_MAXIMUM_TIME,
            self::PROPERTY_RANDOM_QUESTIONS
        );
    }

    public function get_allowed_types()
    {
        $registrations = Configuration::getInstance()->getIntegrationRegistrations(
            self::package(), Manager::package() . '\ContentObject'
        );
        $types = [];

        foreach ($registrations as $registration)
        {
            $namespace = ClassnameUtilities::getInstance()->getNamespaceParent(
                $registration[Registration::PROPERTY_CONTEXT], 6
            );
            $types[] = $namespace . '\Storage\DataClass\\' .
                ClassnameUtilities::getInstance()->getPackageNameFromNamespace($namespace);
        }

        return $types;
    }

    public function get_maximum_attempts()
    {
        return $this->getAdditionalProperty(self::PROPERTY_MAXIMUM_ATTEMPTS);
    }

    /**
     * Returns the maximum score for this assessment
     */
    public function get_maximum_score()
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                ComplexContentObjectItem::class, ComplexContentObjectItem::PROPERTY_PARENT
            ), new StaticConditionVariable($this->get_id())
        );

        $clo_questions = DataManager::retrieve_complex_content_object_items(
            $this->getTypeName(), ComplexContentObjectItem::class, $condition
        );

        $maxscore = 0;

        foreach ($clo_questions as $clo_question)
        {
            $maxscore += $clo_question->get_weight();
        }

        return $maxscore;
    }

    public function get_maximum_time()
    {
        return $this->getAdditionalProperty(self::PROPERTY_MAXIMUM_TIME);
    }

    public function get_questions()
    {
        if (!isset($this->questions))
        {
            $condition = new EqualityCondition(
                new PropertyConditionVariable(
                    ComplexContentObjectItem::class, ComplexContentObjectItem::PROPERTY_PARENT
                ), new StaticConditionVariable($this->get_id()), ComplexContentObjectItem::getTableName()
            );
            $this->questions = DataManager::retrieve_complex_content_object_items(
                ComplexContentObjectItem::class, $condition
            );
        }

        return $this->questions;
    }

    public function get_questions_per_page()
    {
        return $this->getAdditionalProperty(self::PROPERTY_QUESTIONS_PER_PAGE);
    }

    public function get_random_questions()
    {
        return $this->getAdditionalProperty(self::PROPERTY_RANDOM_QUESTIONS);
    }

    public function get_table()
    {
        return self::getTypeName();
    }

    /**
     * @return string
     */
    public static function getTableName(): string
    {
        return 'repository_assessment';
    }

    public static function getTypeName(): string
    {
        return ClassnameUtilities::getInstance()->getClassNameFromNamespace(self::class, true);
    }

    public function has_unlimited_attempts()
    {
        return $this->get_maximum_attempts() == 0;
    }

    public function set_maximum_attempts($value)
    {
        $this->setAdditionalProperty(self::PROPERTY_MAXIMUM_ATTEMPTS, $value);
    }

    public function set_maximum_time($value)
    {
        $this->setAdditionalProperty(self::PROPERTY_MAXIMUM_TIME, $value);
    }

    public function set_questions_per_page($value)
    {
        $this->setAdditionalProperty(self::PROPERTY_QUESTIONS_PER_PAGE, $value);
    }

    public function set_random_questions($random_questions)
    {
        $this->setAdditionalProperty(self::PROPERTY_RANDOM_QUESTIONS, $random_questions);
    }
}
