<?php
namespace Chamilo\Core\Repository\ContentObject\HotspotQuestion\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Interfaces\Versionable;

/**
 * $Id: hotspot_question.class.php 200 2009-11-13 12:30:04Z kariboe $
 * 
 * @package repository.lib.content_object.hotspot_question
 */
/**
 * This class represents a hotspot question
 */
class HotspotQuestion extends ContentObject implements Versionable
{
    const PROPERTY_ANSWERS = 'answers';
    const PROPERTY_IMAGE = 'image';

    public static function get_type_name()
    {
        return ClassnameUtilities::getInstance()->getClassNameFromNamespace(self::class_name(), true);
    }

    public function add_answer($answer)
    {
        $answers = $this->get_answers();
        $answers[] = $answer;
        return $this->set_additional_property(self::PROPERTY_ANSWERS, serialize($answers));
    }

    public function set_answers($answers)
    {
        return $this->set_additional_property(self::PROPERTY_ANSWERS, serialize($answers));
    }

    /**
     * @return HotspotQuestionAnswer[]
     */
    public function get_answers()
    {
        if ($result = unserialize($this->get_additional_property(self::PROPERTY_ANSWERS)))
        {
            return $result;
        }
        return array();
    }

    public function get_number_of_answers()
    {
        return count($this->get_answers());
    }

    public function get_image()
    {
        return $this->get_additional_property(self::PROPERTY_IMAGE);
    }

    public function set_image($image)
    {
        $this->set_additional_property(self::PROPERTY_IMAGE, $image);
    }

    public static function get_additional_property_names()
    {
        return array(self::PROPERTY_ANSWERS, self::PROPERTY_IMAGE);
    }

    public function get_image_object()
    {
        $image = $this->get_image();
        
        if (isset($image) && $image != 0)
        {
            return \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(ContentObject::class_name(), $image);
        }
        else
        {
            return null;
        }
    }

    /**
     * Returns the maximum weight/score a user can receive.
     */
    public function get_maximum_score()
    {
        $max = 0;
        $answers = $this->get_answers();
        foreach ($answers as $answer)
        {
            $max += $answer->get_weight();
        }
        return $max;
    }
    
    // TODO: should be moved to an additional parent layer "question" which offers a default implementation.
    public function get_default_weight()
    {
        return $this->get_maximum_score();
    }
}
