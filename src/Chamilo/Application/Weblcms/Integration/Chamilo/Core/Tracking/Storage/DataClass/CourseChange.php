<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass;

use Chamilo\Core\Tracking\Storage\DataClass\ChangesTracker;

class CourseChange extends ChangesTracker
{
    
    // Can be used for subscribsion of users / classes
    const PROPERTY_TARGET_REFERENCE_ID = 'target_reference_id';

    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames(array(self::PROPERTY_TARGET_REFERENCE_ID));
    }

    public function validate_parameters(array $parameters = [])
    {
        parent::validate_parameters($parameters);
        
        if ($parameters[self::PROPERTY_TARGET_REFERENCE_ID])
        {
            $this->set_target_reference_id($parameters[self::PROPERTY_TARGET_REFERENCE_ID]);
        }
        else
        {
            $this->set_target_reference_id(0);
        }
    }

    public function get_target_reference_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_TARGET_REFERENCE_ID);
    }

    public function set_target_reference_id($target_reference_id)
    {
        $this->setDefaultProperty(self::PROPERTY_TARGET_REFERENCE_ID, $target_reference_id);
    }

    /**
     * @return string
     */
    public static function getTableName(): string
    {
        return 'tracking_weblcms_course_change';
    }
}
