<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Storage\DataClass;

use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Storage\DataManager;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Interfaces\ComplexContentObjectDisclosure;
use Chamilo\Libraries\Architecture\Interfaces\ComplexContentObjectSupport;
use Chamilo\Libraries\Architecture\Interfaces\Versionable;
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
class Page extends ContentObject implements ComplexContentObjectSupport, ComplexContentObjectDisclosure, Versionable
{
    const PROPERTY_CONFIGURATION = 'configuration';

    private $complex_content_objects_cache;

    private $questions_cache;

    static function get_type_name()
    {
        return ClassnameUtilities::getInstance()->getClassNameFromNamespace(self::class_name(), true);
    }

    static function get_additional_property_names()
    {
        return array(self::PROPERTY_CONFIGURATION);
    }

    function getConfiguration()
    {
        $order = array(
            new OrderBy(
                new PropertyConditionVariable(
                    Configuration::class_name(), 
                    Configuration::PROPERTY_DISPLAY_ORDER, 
                    SORT_ASC)));
        
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Configuration::class_name(), Configuration::PROPERTY_PAGE_ID), 
            new StaticConditionVariable($this->get_id()));
        $configurations = DataManager::retrieves(
            Configuration::class_name(), 
            new DataClassRetrievesParameters($condition, null, null, $order))->as_array();
        
        return $configurations;
    }

    function get_allowed_types()
    {
        $registrations = Configuration::getInstance()->getIntegrationRegistrations(
            'Chamilo\Core\Repository\ContentObject\Survey', 
            \Chamilo\Core\Repository\Manager::package() . '\ContentObject');
        $types = array();
        
        foreach ($registrations as $registration)
        {
            $namespace = ClassnameUtilities::getInstance()->getNamespaceParent(
                $registration[Registration::PROPERTY_CONTEXT], 
                6);
            $classname = ClassnameUtilities::getInstance()->getPackageNameFromNamespace($namespace);
            $types[] = $namespace . '\Storage\DataClass\\' . $classname;
        }
        
        return $types;
    }

    function get_questions($complex_items = false)
    {
        $order = array(
            new OrderBy(
                new PropertyConditionVariable(
                    ComplexContentObjectItem::class_name(), 
                    ComplexContentObjectItem::PROPERTY_DISPLAY_ORDER, 
                    SORT_ASC)));
        
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                ComplexContentObjectItem::class_name(), 
                ComplexContentObjectItem::PROPERTY_PARENT), 
            new StaticConditionVariable($this->get_id()));
        $complex_content_objects = \Chamilo\Core\Repository\Storage\DataManager::retrieve_complex_content_object_items(
            ComplexContentObjectItem::class_name(), 
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
        return ClassnameUtilities::getInstance()->getClassNameFromNamespace(self::class_name(), true);
    }
}
?>