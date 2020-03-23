<?php
namespace Chamilo\Core\Repository\ContentObject\Portfolio\Display\Dummy;

use Chamilo\Core\Repository\ContentObject\Portfolio\Display\Preview\Manager;
use Chamilo\Core\Repository\ContentObject\Portfolio\Storage\DataClass\Feedback;
use Chamilo\Libraries\Platform\Session\Session;
use Exception;

/**
 * A dummy Feedback class which allows the preview to emulate the Feedback functionality
 * 
 * @package repository\content_object\portfolio\display
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class FeedbackDummy extends Feedback
{
    const SAMPLE_SOURCE = 'http://www.lipsum.com/feed/xml?amount=50&what=words&start=0';
    
    // Properties
    const PROPERTY_CONTENT_OBJECT_ID = 'content_object_id';

    /**
     * Get the default properties of all feedback
     * 
     * @return array The property names.
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        return parent::get_default_property_names(array(self::PROPERTY_CONTENT_OBJECT_ID));
    }

    /**
     *
     * @return int
     */
    public function get_content_object_id()
    {
        return $this->get_default_property(self::PROPERTY_CONTENT_OBJECT_ID);
    }

    /**
     *
     * @param int $content_object_id
     */
    public function set_content_object_id($content_object_id)
    {
        $this->set_default_property(self::PROPERTY_CONTENT_OBJECT_ID, $content_object_id);
    }

    /**
     *
     * @see \libraries\storage\DataClass::create()
     */
    public function create()
    {
        $feedbacks = self::get_feedbacks($this->get_content_object_id(), $this->get_complex_content_object_id());
        $this->set_id(uniqid());
        $feedbacks[] = $this;
        
        return $this->set_feedbacks($this->get_content_object_id(), $this->get_complex_content_object_id(), $feedbacks);
    }

    /**
     *
     * @see \libraries\storage\DataClass::update()
     */
    public function update()
    {
        $feedbacks = self::get_feedbacks($this->get_content_object_id(), $this->get_complex_content_object_id());
        
        foreach ($feedbacks as $key => $feedback)
        {
            if ($feedback->get_id() == $this->get_id())
            {
                $feedbacks[$key] = $this;
                return $this->set_feedbacks(
                    $this->get_content_object_id(), 
                    $this->get_complex_content_object_id(), 
                    $feedbacks);
            }
        }
        
        return false;
    }

    /**
     *
     * @see \libraries\storage\DataClass::delete()
     */
    public function delete()
    {
        $feedbacks = self::get_feedbacks($this->get_content_object_id(), $this->get_complex_content_object_id());
        
        foreach ($feedbacks as $key => $feedback)
        {
            if ($feedback->get_id() == $this->get_id())
            {
                unset($feedbacks[$key]);
                return $this->set_feedbacks(
                    $this->get_content_object_id(), 
                    $this->get_complex_content_object_id(), 
                    array_values($feedbacks));
            }
        }
        
        return false;
    }

    /**
     * Get the feedback for the given content object id and/or complex conten object id
     * 
     * @param int $content_object_id
     * @param int $complex_content_object_id
     * @return \core\repository\content_object\portfolio\display\FeedbackDummy[]
     */
    public static function get_feedbacks($content_object_id, $complex_content_object_id)
    {
        $feedbacks = unserialize(Session::get(Manager::TEMPORARY_STORAGE));
        
        if (! isset($feedbacks[$content_object_id]) ||
             ! isset($feedbacks[$content_object_id][$complex_content_object_id]))
        {
            $sample_feedbacks = self::generate_sample_feedback($content_object_id, $complex_content_object_id);
            
            self::set_feedbacks($content_object_id, $complex_content_object_id, $sample_feedbacks);
            
            $feedbacks = unserialize(Session::get(Manager::TEMPORARY_STORAGE));
        }
        
        return array_reverse($feedbacks[$content_object_id][$complex_content_object_id]);
    }

    /**
     * Set the feedback for a given content object id and/or complex conten object id
     * 
     * @param int $content_object_id
     * @param int $complex_content_object_id
     * @param \core\repository\content_object\portfolio\display\FeedbackDummy[] $feedbacks
     */
    public static function set_feedbacks($content_object_id, $complex_content_object_id, array $feedbacks)
    {
        $existing_feedbacks = unserialize(Session::get(Manager::TEMPORARY_STORAGE));
        $existing_feedbacks[$content_object_id][$complex_content_object_id] = $feedbacks;
        
        Session::register(Manager::TEMPORARY_STORAGE, serialize($existing_feedbacks));
        return true;
    }

    /**
     * Generate a few sample feedback instances for the given content object id and/or complex conten object id
     * 
     * @param int $content_object_id
     * @param int $complex_content_object_id
     * @return \core\repository\content_object\portfolio\display\FeedbackDummy[]
     */
    public static function generate_sample_feedback($content_object_id, $complex_content_object_id)
    {
        $current_time = time();
        
        $feedbacks = array();
        
        $feedback = new FeedbackDummy();
        $feedback->set_content_object_id($content_object_id);
        $feedback->set_complex_content_object_id($complex_content_object_id);
        $feedback->set_creation_date($current_time - 604800);
        $feedback->set_modification_date($current_time - 604800);
        $feedback->set_user_id(Session::get_user_id());
        $feedback->set_comment(simplexml_load_file(self::SAMPLE_SOURCE)->lipsum->__toString());
        $feedback->set_id(uniqid());
        
        $feedbacks[] = $feedback;
        
        $feedback = new FeedbackDummy();
        $feedback->set_content_object_id($content_object_id);
        $feedback->set_complex_content_object_id($complex_content_object_id);
        $feedback->set_creation_date($current_time - 302400);
        $feedback->set_modification_date($current_time - 302400);
        $feedback->set_user_id(Session::get_user_id());
        $feedback->set_comment(simplexml_load_file(self::SAMPLE_SOURCE)->lipsum->__toString());
        $feedback->set_id(uniqid());
        
        $feedbacks[] = $feedback;
        
        $feedback = new FeedbackDummy();
        $feedback->set_content_object_id($content_object_id);
        $feedback->set_complex_content_object_id($complex_content_object_id);
        $feedback->set_creation_date($current_time);
        $feedback->set_modification_date($current_time);
        $feedback->set_user_id(Session::get_user_id());
        $feedback->set_comment(simplexml_load_file(self::SAMPLE_SOURCE)->lipsum->__toString());
        $feedback->set_id(uniqid());
        
        $feedbacks[] = $feedback;
        
        return $feedbacks;
    }

    /**
     * Get a specific feedback instance by it's id
     * 
     * @param int $feedback_id
     * @throws \Exception
     * @return \core\repository\content_object\portfolio\display\FeedbackDummy
     */
    public static function get_feedback($feedback_id)
    {
        $feedbacks = unserialize(Session::get(Manager::TEMPORARY_STORAGE));
        
        foreach ($feedbacks as $content_object_id => $complex_content_object_feedbacks)
        {
            foreach ($complex_content_object_feedbacks as $complex_content_object__id => $feedbacks)
            {
                foreach ($feedbacks as $feedback)
                {
                    if ($feedback->get_id() == $feedback_id)
                    {
                        return $feedback;
                    }
                }
            }
        }
        
        throw new Exception('NoSuchFeedbackId');
    }
}