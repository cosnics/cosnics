<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Storage\DataClass;

use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Choice\Storage\DataClass\Choice;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\DateTime\Storage\DataClass\DateTime;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Description\Storage\DataClass\Description;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Gender\Storage\DataClass\Gender;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Matching\Storage\DataClass\Matching;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Matrix\Storage\DataClass\Matrix;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\MultipleChoice\Storage\DataClass\MultipleChoice;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Open\Storage\DataClass\Open;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Order\Storage\DataClass\Order;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Rating\Storage\DataClass\Rating;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Select\Storage\DataClass\Select;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Interfaces\ComplexContentObjectDisclosure;
use Chamilo\Libraries\Architecture\Interfaces\ComplexContentObjectSupport;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package repository.content_object.survey_page
 * @author Eduard Vossen
 * @author Magali Gillard
 */
/**
 * This class represents an survey
 */
class Page extends ContentObject implements ComplexContentObjectSupport, ComplexContentObjectDisclosure
{
    const PROPERTY_CONFIG = 'config';
    const CLASS_NAME = __CLASS__;

    private $complex_content_objects_cache;

    private $questions_cache;

    static function get_type_name()
    {
        return ClassnameUtilities :: getInstance()->getClassNameFromNamespace(self :: CLASS_NAME, true);
    }

    static function get_additional_property_names()
    {
        return array(self :: PROPERTY_CONFIG);
    }

    function get_config()
    {
        if ($result = unserialize($this->get_additional_property(self :: PROPERTY_CONFIG)))
        {
            return $result;
        }
        return array();
    }

    function set_config($value)
    {
        $this->set_additional_property(self :: PROPERTY_CONFIG, serialize($value));
    }

    function get_allowed_types()
    {
        $allowed_types = array();
        $allowed_types[] = Rating :: class_name();
        $allowed_types[] = Open :: class_name();
        $allowed_types[] = MultipleChoice :: class_name();
        $allowed_types[] = Matching :: class_name();
        $allowed_types[] = Select :: class_name();
        $allowed_types[] = Matrix :: class_name();
        $allowed_types[] = Description :: class_name();
        $allowed_types[] = DateTime :: class_name();
        $allowed_types[] = Choice :: class_name();
        $allowed_types[] = Gender :: class_name();
        $allowed_types[] = Order :: class_name();
        
        return $allowed_types;
    }

    function get_questions($complex_items = false)
    {
        $order = array(
            new OrderBy(
                new PropertyConditionVariable(
                    ComplexContentObjectItem :: class_name(), 
                    ComplexContentObjectItem :: PROPERTY_DISPLAY_ORDER, 
                    SORT_ASC)));
        
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                ComplexContentObjectItem :: class_name(), 
                ComplexContentObjectItem :: PROPERTY_PARENT), 
            new StaticConditionVariable($this->get_id()));
        $complex_content_objects = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_complex_content_object_items(
            ComplexContentObjectItem :: class_name(), 
            new DataClassRetrievesParameters($condition, null, null, $order))->as_array();
        
        if ($complex_items)
        {
            return $complex_content_objects;
        }
        
        $survey_questions = array();
        
        foreach ($complex_content_objects as $complex_content_object)
        {
            $survey_questions[] = $complex_content_object->get_ref_object();
        }
        
        return $survey_questions;
    }

    function get_table()
    {
        return ClassnameUtilities :: getInstance()->getClassNameFromNamespace(self :: class_name(), true);
        ;
    }
}
?>