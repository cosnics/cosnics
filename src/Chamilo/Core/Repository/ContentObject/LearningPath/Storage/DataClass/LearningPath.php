<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass;

use Chamilo\Core\Repository\ContentObject\LearningPath\ComplexContentObjectPath;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Interfaces\ComplexContentObjectDisclosure;
use Chamilo\Libraries\Architecture\Interfaces\ComplexContentObjectSupport;
use Chamilo\Libraries\File\Path;

/**
 * $Id: learning_path.class.php 200 2009-11-13 12:30:04Z kariboe $
 * 
 * @package repository.lib.content_object.learning_path
 */
class LearningPath extends ContentObject implements ComplexContentObjectSupport, ComplexContentObjectDisclosure
{
    const CLASS_NAME = __CLASS__;
    const PROPERTY_CONTROL_MODE = 'control_mode';
    const PROPERTY_VERSION = 'version';
    const PROPERTY_PATH = 'path';

    /**
     *
     * @var ComplexContentObjectPath
     */
    private $complex_content_object_path;

    public static function get_type_name()
    {
        return ClassnameUtilities :: getInstance()->getClassNameFromNamespace(self :: CLASS_NAME, true);
    }

    public static function get_additional_property_names()
    {
        return array(self :: PROPERTY_CONTROL_MODE, self :: PROPERTY_VERSION, self :: PROPERTY_PATH);
    }

    public function get_allowed_types()
    {
        $registrations = \Chamilo\Configuration\Storage\DataManager :: get_integrating_contexts(
            __NAMESPACE__ . '\display', 
            \Chamilo\Core\Repository\Manager :: context() . '\content_object');
        $types = array();
        
        foreach ($registrations as $registration)
        {
            $namespace = ClassnameUtilities :: getInstance()->getNamespaceParent($registration->get_context(), 6);
            $types[] = $namespace . '\\' .
                 ClassnameUtilities :: getInstance()->getPackageNameFromNamespace($namespace, true);
        }
        
        return $types;
    }

    public function get_control_mode()
    {
        return unserialize($this->get_additional_property(self :: PROPERTY_CONTROL_MODE));
    }

    public function set_control_mode($control_mode)
    {
        if (! is_array($control_mode))
            $control_mode = array($control_mode);
        
        $this->set_additional_property(self :: PROPERTY_CONTROL_MODE, serialize($control_mode));
    }

    public function get_version()
    {
        return $this->get_additional_property(self :: PROPERTY_VERSION);
    }

    public function set_version($version)
    {
        $this->set_additional_property(self :: PROPERTY_VERSION, $version);
    }

    public function get_path()
    {
        return $this->get_additional_property(self :: PROPERTY_PATH);
    }

    public function set_path($path)
    {
        $this->set_additional_property(self :: PROPERTY_PATH, $path);
    }

    public function get_full_path()
    {
        return Path :: getInstance()->getStoragePath('scorm') . $this->get_owner_id() . '/' . $this->get_path() . '/';
    }
    
    // TODO: This should take variable $attempt_data into account
    /**
     *
     * @param DummyLpiAttemptTracker[] $nodes_attempt_data
     * @return ComplexContentObjectPath
     */
    public function get_complex_content_object_path($nodes_attempt_data)
    {
        if (! isset($this->complex_content_object_path))
        {
            $this->complex_content_object_path = new ComplexContentObjectPath($this, $nodes_attempt_data);
        }
        
        return $this->complex_content_object_path;
    }
}
