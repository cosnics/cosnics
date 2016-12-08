<?php
namespace Chamilo\Core\Repository\ContentObject\Assessment\Display\Preview;

use Chamilo\Core\Repository\ContentObject\Assessment\Display\Attempt\AbstractAttempt;
use Chamilo\Libraries\Platform\Session\Session;

/**
 *
 * @package core\repository\content_object\assessment\display
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class PreviewStorage
{
    const PROPERTY_ASSESSMENT_QUESTION_ATTEMPT = 'assessment_question_attempt';
    const PROPERTY_ASSESSMENT_ATTEMPT = 'assessment_attempt';

    /**
     *
     * @var \core\repository\content_object\assessment\display\PreviewStorage
     */
    private static $instance;

    /**
     *
     * @return \core\repository\content_object\assessment\display\PreviewStorage
     */
    public static function getInstance()
    {
        if (! isset(self::$instance))
        {
            self::$instance = new PreviewStorage();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $storage = $this->get_storage();
        
        if (! isset($storage))
        {
            $this->set_storage(array());
        }
    }

    /**
     * Empty the storage
     * 
     * @param int $content_object_id
     * @return boolean
     */
    public function reset($content_object_id = null)
    {
        if (is_null($content_object_id))
        {
            return $this->set_storage(array());
        }
        else
        {
            $attempt = $this->retrieve_assessment_attempt($content_object_id);
            
            if ($attempt instanceof AbstractAttempt)
            {
                $question_attempts = $this->retrieve_assessment_question_attempts($attempt);
                
                foreach ($question_attempts as $question_attempt)
                {
                    if (! $question_attempt->delete())
                    {
                        return false;
                    }
                }
                
                return $attempt->delete();
            }
            else
            {
                return true;
            }
        }
    }

    /**
     *
     * @return mixed
     */
    public function get_storage()
    {
        return unserialize(Session::retrieve(__NAMESPACE__));
    }

    /**
     *
     * @param mixed $data
     * @return boolean
     */
    public function set_storage($data)
    {
        Session::register(__NAMESPACE__, serialize($data));
        return true;
    }

    /**
     *
     * @param string $property
     * @param mixed $value
     */
    public function set_property($property, $value)
    {
        $data = $this->get_storage();
        $data[$property] = $value;
        return $this->set_storage($data);
    }

    /**
     *
     * @param string $property
     * @return mixed
     */
    public function get_property($property)
    {
        $data = $this->get_storage();
        return $data[$property];
    }

    /**
     *
     * @return \core\repository\content_object\assessment\display\DummyAttempt[]
     */
    public function get_assessment_attempts()
    {
        $attempts = $this->get_property(self::PROPERTY_ASSESSMENT_ATTEMPT);
        if (! isset($attempts))
        {
            $attempts = array();
            $this->set_property(self::PROPERTY_ASSESSMENT_ATTEMPT, $attempts);
        }
        return $attempts;
    }

    /**
     *
     * @param AbstractAttempt $attempt
     * @return boolean
     */
    public function create_assessment_attempt(DummyAttempt $attempt)
    {
        $attempts = $this->get_assessment_attempts();
        $attempts[$attempt->get_content_object_id()] = $attempt;
        return $this->set_property(self::PROPERTY_ASSESSMENT_ATTEMPT, $attempts);
    }

    /**
     *
     * @param DummyAttempt $attempt
     * @return boolean
     */
    public function update_assessment_attempt(DummyAttempt $attempt)
    {
        return $this->create_assessment_attempt($attempt);
    }

    /**
     *
     * @param DummyAttempt $item_attempt
     * @return boolean
     */
    public function delete_assessment_attempt(DummyAttempt $attempt)
    {
        $attempts = $this->get_assessment_attempts();
        unset($attempts[$attempt->get_content_object_id()]);
        return $this->set_property(self::PROPERTY_ASSESSMENT_ATTEMPT, $attempts);
    }

    /**
     *
     * @param int $content_object_id
     */
    public function retrieve_assessment_attempt($content_object_id)
    {
        $attempts = $this->get_assessment_attempts();
        return isset($attempts[$content_object_id]) ? $attempts[$content_object_id] : null;
    }

    /**
     *
     * @return \core\repository\content_object\assessment\display\DummyQuestionAttempt[]
     */
    public function get_question_attempts()
    {
        $attempts = $this->get_property(self::PROPERTY_ASSESSMENT_QUESTION_ATTEMPT);
        if (! isset($attempts))
        {
            $attempts = array();
            $this->set_property(self::PROPERTY_ASSESSMENT_QUESTION_ATTEMPT, $attempts);
        }
        return $attempts;
    }

    /**
     *
     * @param DummyQuestionAttempt $question_attempt
     * @return boolean
     */
    public function create_assessment_question_attempt(DummyQuestionAttempt $question_attempt)
    {
        $attempts = $this->get_question_attempts();
        $attempts[$question_attempt->get_attempt_id()][$question_attempt->get_question_complex_id()] = $question_attempt;
        return $this->set_property(self::PROPERTY_ASSESSMENT_QUESTION_ATTEMPT, $attempts);
    }

    /**
     *
     * @param DummyQuestionAttempt $question_attempt
     * @return boolean
     */
    public function update_assessment_question_attempt(DummyQuestionAttempt $question_attempt)
    {
        return $this->create_assessment_question_attempt($question_attempt);
    }

    /**
     *
     * @param DummyQuestionAttempt $item_attempt
     * @return boolean
     */
    public function delete_assessment_question_attempt(DummyQuestionAttempt $question_attempt)
    {
        $attempts = $this->get_question_attempts();
        unset($attempts[$question_attempt->get_attempt_id()][$question_attempt->get_question_complex_id()]);
        return $this->set_property(self::PROPERTY_ASSESSMENT_QUESTION_ATTEMPT, $attempts);
    }

    /**
     *
     * @param DummyAttempt $attempt
     * @return \core\repository\content_object\assessment\display\DummyQuestionAttempt[]
     */
    public function retrieve_assessment_question_attempts(DummyAttempt $attempt)
    {
        $attempts = $this->get_question_attempts();
        return isset($attempts[$attempt->get_id()]) ? $attempts[$attempt->get_id()] : null;
    }

    /**
     *
     * @param DummyAttempt $attempt
     * @param int $complex_question_id
     * @return \core\repository\content_object\assessment\display\DummyQuestionAttempt
     */
    public function retrieve_assessment_question_attempt(DummyAttempt $attempt, $complex_question_id)
    {
        $attempts = $this->get_question_attempts();
        if (isset($attempts[$attempt->get_id()]))
        {
            $item_attempts = $attempts[$attempt->get_id()];
            return isset($item_attempts[$complex_question_id]) ? $item_attempts[$complex_question_id] : null;
        }
        else
        {
            return null;
        }
    }
}