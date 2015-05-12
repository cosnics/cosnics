<?php
namespace Chamilo\Core\Repository\ContentObject\PeerAssessment\Storage\DataClass;

use Chamilo\Core\Repository\ContentObject\Indicator\Storage\DataClass\Indicator;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Interfaces\ComplexContentObjectSupport;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use DirectoryIterator;
use RegexIterator;

/**
 * This class represents a peer_assessment
 */
class PeerAssessment extends ContentObject implements ComplexContentObjectSupport
{
    const CLASS_NAME = __CLASS__;
    const PROPERTY_ASSESSMENT_TYPE = 'assessment_type';
    const PROPERTY_SCALE = 'scale';
    const TYPE_SCORES = 1;
    const TYPE_FEEDBACK = 2;
    const TYPE_BOTH = 3;

    public static function get_type_name()
    {
        return ClassnameUtilities :: getInstance()->getClassNameFromNamespace(self :: CLASS_NAME, true);
    }

    public static function get_additional_property_names()
    {
        return array(self :: PROPERTY_ASSESSMENT_TYPE, self :: PROPERTY_SCALE);
    }

    public function get_allowed_types()
    {
        return array(Indicator :: class_name());
    }

    public function get_assessment_type()
    {
        return $this->get_additional_property(self :: PROPERTY_ASSESSMENT_TYPE);
    }

    public function set_assessment_type($assessment_type)
    {
        return $this->set_additional_property(self :: PROPERTY_ASSESSMENT_TYPE, $assessment_type);
    }

    public function get_scale()
    {
        return $this->get_additional_property(self :: PROPERTY_SCALE);
    }

    public function set_scale($scale)
    {
        return $this->set_additional_property(self :: PROPERTY_SCALE, $scale);
    }

    public function get_scale_types()
    {
        // following code didn't resolve on test server
        // $iterator = new RegexIterator(new DirectoryIterator($this->get_scales_path()),
        // '/(\w+)_result_processor\\.class\\.php/', RegexIterator :: REPLACE);
        // $iterator->replacement = '$1';
        // return iterator_to_array($iterator, false);
        $iterator = new DirectoryIterator($this->get_scales_path());
        
        foreach ($iterator as $file)
        {
            $name = $file->getFilename();
            $pattern = '/(\w+)_result_processor\\.class\\.php/';
            $res = preg_match($pattern, $name);
            if ($res == 1)
            {
                $scales[] = str_replace('_result_processor.class.php', '', $name);
            }
        }
        return $scales;
    }

    public function get_result_processor()
    {
        // load the appropriate result processor class
        $filename = $this->get_scales_path() . $this->get_scale() . '_result_processor.class.php';
        require_once $filename;
        // return a new instance of the result processor class
        $classname = ClassnameUtilities :: getInstance()->getNamespaceFromObject($this) . '\\' .
             (string) StringUtilities :: getInstance()->createString($this->get_scale())->upperCamelize() .
             'ResultProcessor';
        return new $classname();
    }

    public function get_scales_path()
    {
        return __DIR__ . '/result_processors/';
    }

    public function get_factor_title()
    {
        $scale = $this->get_scale();
        
        if ($scale == 'van_achter')
        {
            return Translation :: get('Addition');
        }
        
        return Translation :: get('Factor');
    }
}
