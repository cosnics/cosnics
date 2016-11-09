<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Preview;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\AbstractAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\AbstractItemAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\AbstractQuestionAttempt;
use Chamilo\Libraries\Platform\Session\Session;

/**
 *
 * @package core\repository\content_object\learning_path\display
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class PreviewStorage
{
    const PROPERTY_QUESTION_ATTEMPTS = 'question_attempts';
    const PROPERTY_LEARNING_PATH_ATTEMPT = 'learning_path_attempt';
    const PROPERTY_LEARNING_PATH_ITEM_ATTEMPTS = 'learning_path_item_attempts';

    /**
     *
     * @var \core\repository\content_object\learning_path\display\PreviewStorage
     */
    private static $instance;

    /**
     *
     * @return \core\repository\content_object\learning_path\display\PreviewStorage
     */
    public static function getInstance()
    {
        if (! isset(self :: $instance))
        {
            self :: $instance = new PreviewStorage();
        }
        return self :: $instance;
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
     * @return boolean
     */
    public function reset()
    {
        return $this->set_storage(array());
    }

    /**
     *
     * @return mixed
     */
    public function get_storage()
    {
        return unserialize(Session :: retrieve(__NAMESPACE__));
    }

    /**
     *
     * @param mixed $data
     * @return boolean
     */
    public function set_storage($data)
    {
        Session :: register(__NAMESPACE__, serialize($data));
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
     * @return \core\repository\content_object\learning_path\display\AbstractItemAttempt[]
     */
    public function get_item_attempts()
    {
        $attempts = $this->get_property(self :: PROPERTY_LEARNING_PATH_ITEM_ATTEMPTS);
        if (! isset($attempts))
        {
            $attempts = array();
            $this->set_property(self :: PROPERTY_LEARNING_PATH_ITEM_ATTEMPTS, $attempts);
        }
        return $attempts;
    }

    /**
     *
     * @param AbstractItemAttempt $item_attempt
     * @return boolean
     */
    public function create_learning_path_item_attempt(AbstractItemAttempt $item_attempt)
    {
        $attempts = $this->get_item_attempts();
        $attempts[$item_attempt->get_learning_path_attempt_id()][$item_attempt->get_learning_path_item_id()][$item_attempt->get_id()] = $item_attempt;
        return $this->set_property(self :: PROPERTY_LEARNING_PATH_ITEM_ATTEMPTS, $attempts);
    }

    /**
     *
     * @param AbstractItemAttempt $item_attempt
     * @return boolean
     */
    public function update_learning_path_item_attempt(AbstractItemAttempt $item_attempt)
    {
        return $this->create_learning_path_item_attempt($item_attempt);
    }

    /**
     *
     * @param AbstractItemAttempt $item_attempt
     * @return boolean
     */
    public function delete_learning_path_item_attempt(AbstractItemAttempt $item_attempt)
    {
        $attempts = $this->get_item_attempts();
        unset(
            $attempts[$item_attempt->get_learning_path_attempt_id()][$item_attempt->get_learning_path_item_id()][$item_attempt->get_id()]);
        return $this->set_property(self :: PROPERTY_LEARNING_PATH_ITEM_ATTEMPTS, $attempts);
    }

    /**
     *
     * @param string $item_attempt_id
     * @return \core\repository\content_object\learning_path\display\AbstractItemAttempt
     */
    public function retrieve_learning_path_item_attempt($item_attempt_id)
    {
        $attempts = $this->get_item_attempts();
        foreach ($attempts as $item_attempts)
        {
            foreach ($item_attempts as $learning_path_item_attempts)
            {
                foreach ($learning_path_item_attempts as $item_attempt)
                {
                    if ($item_attempt->get_id() == $item_attempt_id)
                    {
                        return $item_attempt;
                    }
                }
            }
        }
        return null;
    }

    /**
     *
     * @param AbstractAttempt $attempt
     * @return \core\repository\content_object\learning_path\display\AbstractItemAttempt[]
     */
    public function retrieve_learning_path_item_attempts(AbstractAttempt $attempt)
    {
        $attempts = $this->get_item_attempts();
        return isset($attempts[$attempt->get_id()]) ? $attempts[$attempt->get_id()] : null;
    }

    /**
     *
     * @return \core\repository\content_object\learning_path\display\DummyAttempt[]
     */
    public function get_learning_path_attempts()
    {
        $attempts = $this->get_property(self :: PROPERTY_LEARNING_PATH_ATTEMPT);
        if (! isset($attempts))
        {
            $attempts = array();
            $this->set_property(self :: PROPERTY_LEARNING_PATH_ATTEMPT, $attempts);
        }
        return $attempts;
    }

    /**
     *
     * @param AbstractAttempt $attempt
     * @return boolean
     */
    public function create_learning_path_attempt(AbstractAttempt $attempt)
    {
        $attempts = $this->get_learning_path_attempts();
        $attempts[$attempt->get_content_object_id()] = $attempt;
        return $this->set_property(self :: PROPERTY_LEARNING_PATH_ATTEMPT, $attempts);
    }

    /**
     *
     * @param AbstractAttempt $attempt
     * @return boolean
     */
    public function update_learning_path_attempt(AbstractAttempt $attempt)
    {
        return $this->create_learning_path_attempt($attempt);
    }

    /**
     *
     * @param AbstractAttempt $item_attempt
     * @return boolean
     */
    public function delete_learning_path_attempt(AbstractAttempt $attempt)
    {
        $attempts = $this->get_learning_path_attempts();
        unset($attempts[$attempt->get_content_object_id()]);
        return $this->set_property(self :: PROPERTY_LEARNING_PATH_ATTEMPT, $attempts);
    }

    /**
     *
     * @param int $content_object_id
     */
    public function retrieve_learning_path_attempt($content_object_id)
    {
        $attempts = $this->get_learning_path_attempts();
        return isset($attempts[$content_object_id]) ? $attempts[$content_object_id] : null;
    }

    /**
     *
     * @return \core\repository\content_object\learning_path\display\AbstractQuestionAttempt[]
     */
    public function get_question_attempts()
    {
        $attempts = $this->get_property(self :: PROPERTY_QUESTION_ATTEMPTS);
        if (! isset($attempts))
        {
            $attempts = array();
            $this->set_property(self :: PROPERTY_QUESTION_ATTEMPTS, $attempts);
        }
        return $attempts;
    }

    /**
     *
     * @param AbstractQuestionAttempt $question_attempt
     * @return boolean
     */
    public function create_learning_path_question_attempt(AbstractQuestionAttempt $question_attempt)
    {
        $attempts = $this->get_question_attempts();
        $attempts[$question_attempt->get_item_attempt_id()][$question_attempt->get_question_complex_id()] = $question_attempt;
        return $this->set_property(self :: PROPERTY_QUESTION_ATTEMPTS, $attempts);
    }

    /**
     *
     * @param AbstractQuestionAttempt $question_attempt
     * @return boolean
     */
    public function update_learning_path_question_attempt(AbstractQuestionAttempt $question_attempt)
    {
        return $this->create_learning_path_question_attempt($question_attempt);
    }

    /**
     *
     * @param AbstractQuestionAttempt $item_attempt
     * @return boolean
     */
    public function delete_learning_path_question_attempt(AbstractQuestionAttempt $question_attempt)
    {
        $attempts = $this->get_question_attempts();
        unset($attempts[$question_attempt->get_item_attempt_id()][$question_attempt->get_question_complex_id()]);
        return $this->set_property(self :: PROPERTY_QUESTION_ATTEMPTS, $attempts);
    }

    /**
     *
     * @param AbstractItemAttempt $attempt
     * @return \core\repository\content_object\learning_path\display\DummyQuestionAttempt[]
     */
    public function retrieve_learning_path_question_attempts(AbstractItemAttempt $attempt)
    {
        $attempts = $this->get_question_attempts();
        return isset($attempts[$attempt->get_id()]) ? $attempts[$attempt->get_id()] : null;
    }

    /**
     *
     * @param AbstractItemAttempt $attempt
     * @param int $complex_question_id
     * @return \core\repository\content_object\learning_path\display\DummyQuestionAttempt
     */
    public function retrieve_learning_path_question_attempt(AbstractItemAttempt $attempt, $complex_question_id)
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