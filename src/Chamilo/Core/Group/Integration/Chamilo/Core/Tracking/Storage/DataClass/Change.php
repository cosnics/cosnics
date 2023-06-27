<?php
namespace Chamilo\Core\Group\Integration\Chamilo\Core\Tracking\Storage\DataClass;

use Chamilo\Core\Tracking\Storage\DataClass\ChangesTracker;

/**
 * @package Chamilo\Core\Group\Integration\Chamilo\Core\Tracking\Storage\DataClass
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Change extends ChangesTracker
{
    public const CONTEXT = 'Chamilo\Core\Group\Integration\Chamilo\Core\Tracking';

    public const PROPERTY_TARGET_USER_ID = 'target_user_id';

    /**
     * @return string[]
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames([self::PROPERTY_TARGET_USER_ID]);
    }

    public static function getStorageUnitName(): string
    {
        return 'tracking_group_change';
    }

    public function get_target_user_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_TARGET_USER_ID);
    }

    public function set_target_user_id($target_user_id)
    {
        $this->setDefaultProperty(self::PROPERTY_TARGET_USER_ID, $target_user_id);
    }

    public function is_summary_tracker()
    {
        return false;
    }



    public function validate_parameters(array $parameters = [])
    {
        parent::validate_parameters($parameters);

        if ($parameters[self::PROPERTY_TARGET_USER_ID])
        {
            $this->set_target_user_id($parameters[self::PROPERTY_TARGET_USER_ID]);
        }
        else
        {
            $this->set_target_user_id(0);
        }
    }
}
