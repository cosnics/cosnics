<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Storage\DataClass;

use Chamilo\Configuration\Configuration;
use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Storage\DataClass\Page;
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
use Chamilo\Libraries\Utilities\StringUtilities;

class Survey extends \Chamilo\Core\Repository\Storage\DataClass\ContentObject implements ComplexContentObjectSupport, 
    ComplexContentObjectDisclosure, Versionable
{
    const PROPERTY_FINISH_TEXT = 'finish_text';
    const PROPERTY_MENU = 'menu';
    const PROPERTY_PROGRESS_BAR = 'progress_bar';

    private $survey_pages;

    static function get_type_name()
    {
        return ClassnameUtilities :: getInstance()->getClassNameFromNamespace(self :: class_name(), true);
    }

    static function get_additional_property_names()
    {
        return array(self :: PROPERTY_FINISH_TEXT, self :: PROPERTY_MENU, self :: PROPERTY_PROGRESS_BAR);
    }

    function get_finish_text()
    {
        return $this->get_additional_property(self :: PROPERTY_FINISH_TEXT);
    }

    function set_finish_text($value)
    {
        $this->set_additional_property(self :: PROPERTY_FINISH_TEXT, $value);
    }

    function get_menu()
    {
        return $this->get_additional_property(self :: PROPERTY_MENU);
    }

    function set_menu($menu)
    {
        $this->set_additional_property(self :: PROPERTY_MENU, $menu);
    }

    function with_menu()
    {
        return $this->get_additional_property(self :: PROPERTY_MENU) == 1;
    }

    function get_progress_bar()
    {
        return $this->get_additional_property(self :: PROPERTY_PROGRESS_BAR);
    }

    function set_progress_bar($progress_bar)
    {
        $this->set_additional_property(self :: PROPERTY_PROGRESS_BAR, $progress_bar);
    }

    function with_progress_bar()
    {
        return $this->get_additional_property(self :: PROPERTY_PROGRESS_BAR) == 1;
    }

    function get_allowed_types()
    {
        $registrations = Configuration :: getInstance()->getIntegrationRegistrations(
            'Chamilo\Core\Repository\ContentObject\Survey', 
            \Chamilo\Core\Repository\Manager :: package() . '\ContentObject');
        $types = array();
        
        foreach ($registrations as $registration)
        {
            $namespace = ClassnameUtilities :: getInstance()->getNamespaceParent(
                $registration[Registration :: PROPERTY_CONTEXT], 
                6);
            $classname = ClassnameUtilities :: getInstance()->getPackageNameFromNamespace($namespace);
            $types[] = $namespace . '\Storage\DataClass\\' . $classname;
        }
        
        $types[] = self :: class_name();
        
        return $types;
    }

    function get_table()
    {
        return (string) StringUtilities :: getInstance()->createString(self :: class_name())->underscored();
    }

    function getPages($complex_items = false)
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
        
        $survey_pages = array();
        
        foreach ($complex_content_objects as $complex_content_object)
        {
            $survey_pages[] = $complex_content_object->get_ref_object();
        }
        
        return $survey_pages;
    }

    /**
     *
     * @param int $page_id
     * @return Page
     */
    function get_page_by_id($page_id)
    {
        if (! isset($this->survey_page_cache) || ! isset($this->survey_page_cache[$page_id]))
        {
            $this->survey_page_cache[$page_id] = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_by_id(
                ContentObject :: class_name(), 
                $page_id);
        }
        return $this->survey_page_cache[$page_id];
    }
}
?>