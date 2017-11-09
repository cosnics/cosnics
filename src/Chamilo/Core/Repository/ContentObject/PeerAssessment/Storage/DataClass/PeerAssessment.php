<?php
namespace Chamilo\Core\Repository\ContentObject\PeerAssessment\Storage\DataClass;

use Chamilo\Core\Repository\ContentObject\Indicator\Storage\DataClass\Indicator;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Interfaces\ComplexContentObjectSupport;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * This class represents a peer_assessment
 */
class PeerAssessment extends ContentObject implements ComplexContentObjectSupport
{
    const PROPERTY_ASSESSMENT_TYPE = 'assessment_type';
    const PROPERTY_SCALE = 'scale';
    const TYPE_SCORES = 1;
    const TYPE_FEEDBACK = 2;
    const TYPE_BOTH = 3;

    public static function get_type_name()
    {
        return ClassnameUtilities::getInstance()->getClassNameFromNamespace(self::class_name(), true);
    }

    public static function get_additional_property_names()
    {
        return array(self::PROPERTY_ASSESSMENT_TYPE, self::PROPERTY_SCALE);
    }

    public function get_allowed_types()
    {
        return array(Indicator::class_name());
    }

    public function get_assessment_type()
    {
        return $this->get_additional_property(self::PROPERTY_ASSESSMENT_TYPE);
    }

    public function set_assessment_type($assessment_type)
    {
        return $this->set_additional_property(self::PROPERTY_ASSESSMENT_TYPE, $assessment_type);
    }

    public function get_scale()
    {
        return $this->get_additional_property(self::PROPERTY_SCALE);
    }

    public function set_scale($scale)
    {
        return $this->set_additional_property(self::PROPERTY_SCALE, $scale);
    }

    public function get_scale_types()
    {
        return array('dochy', 'van_achter');
    }

    public function get_result_processor()
    {
        // return a new instance of the result processor class
        $classname = self::package() . '\ResultProcessors\\' .
             (string) StringUtilities::getInstance()->createString($this->get_scale())->upperCamelize() .
             'ResultProcessor';
        return new $classname();
    }

    public function get_factor_title()
    {
        $scale = $this->get_scale();
        
        if ($scale == 'van_achter')
        {
            return Translation::get('Addition');
        }
        
        return Translation::get('Factor');
    }
}
