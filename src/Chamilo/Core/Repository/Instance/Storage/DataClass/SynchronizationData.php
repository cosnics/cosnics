<?php
namespace Chamilo\Core\Repository\Instance\Storage\DataClass;

use Chamilo\Core\Repository\External\DataConnector;
use Chamilo\Core\Repository\Instance\Storage\DataManager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\StringUtilities;
use Exception;

/**
 *
 * @author Hans De Bisschop
 */
class SynchronizationData extends DataClass
{
    
    // Properties
    const PROPERTY_CREATED = 'created';
    const PROPERTY_MODIFIED = 'modified';
    const PROPERTY_CONTENT_OBJECT_ID = 'content_object_id';
    const PROPERTY_CONTENT_OBJECT_TIMESTAMP = 'content_object_timestamp';
    const PROPERTY_EXTERNAL_ID = 'external_id';
    const PROPERTY_EXTERNAL_OBJECT_ID = 'external_object_id';
    const PROPERTY_EXTERNAL_OBJECT_TIMESTAMP = 'external_object_timestamp';
    const PROPERTY_EXTERNAL_USER_ID = 'external_user_id';
    const PROPERTY_STATE = 'state';
    
    // Synchronization statuses
    const SYNC_STATUS_ERROR = 0;
    const SYNC_STATUS_EXTERNAL = 1;
    const SYNC_STATUS_INTERNAL = 2;
    const SYNC_STATUS_IDENTICAL = 3;
    const SYNC_STATUS_CONFLICT = 4;
    
    // Synchrnization link statuses
    const STATE_ACTIVE = 1;
    const STATE_INACTIVE = 0;

    /**
     *
     * @var ContentObject
     */
    private $content_object;

    /**
     *
     * @var ExternalObject
     */
    private $external_object;

    /**
     *
     * @var int
     */
    private $synchronization_status;

    /**
     *
     * @var ExternalRepository
     */
    private $external;

    /**
     *
     * @param $content_object_id int
     */
    public function set_content_object_id($content_object_id)
    {
        if (isset($content_object_id) && is_numeric($content_object_id))
        {
            $this->set_default_property(self::PROPERTY_CONTENT_OBJECT_ID, $content_object_id);
        }
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
     * @param $external_object_id string
     */
    public function set_external_object_id($external_object_id)
    {
        if (StringUtilities::getInstance()->hasValue($external_object_id))
        {
            $this->set_default_property(self::PROPERTY_EXTERNAL_OBJECT_ID, $external_object_id);
        }
    }

    /**
     *
     * @return string
     */
    public function get_external_object_id()
    {
        return $this->get_default_property(self::PROPERTY_EXTERNAL_OBJECT_ID);
    }

    /**
     *
     * @param $datetime int
     */
    public function set_content_object_timestamp($datetime)
    {
        if (isset($datetime) && is_numeric($datetime))
        {
            $this->set_default_property(self::PROPERTY_CONTENT_OBJECT_TIMESTAMP, $datetime);
        }
    }

    /**
     *
     * @return int
     */
    public function get_content_object_timestamp()
    {
        return $this->get_default_property(self::PROPERTY_CONTENT_OBJECT_TIMESTAMP);
    }

    /**
     *
     * @param $datetime int
     */
    public function set_external_object_timestamp($datetime)
    {
        if (isset($datetime) && is_numeric($datetime))
        {
            $this->set_default_property(self::PROPERTY_EXTERNAL_OBJECT_TIMESTAMP, $datetime);
        }
    }

    /**
     *
     * @return int
     */
    public function get_external_object_timestamp()
    {
        return $this->get_default_property(self::PROPERTY_EXTERNAL_OBJECT_TIMESTAMP);
    }

    /**
     *
     * @param $external_id int
     */
    public function set_external_id($external_id)
    {
        if (isset($external_id) && is_numeric($external_id))
        {
            $this->set_default_property(self::PROPERTY_EXTERNAL_ID, $external_id);
        }
    }

    /**
     *
     * @return int
     */
    public function get_external_id()
    {
        return $this->get_default_property(self::PROPERTY_EXTERNAL_ID);
    }

    /**
     *
     * @param $external_user_id string
     */
    public function set_external_user_id($external_user_id)
    {
        if (StringUtilities::getInstance()->hasValue($external_user_id))
        {
            $this->set_default_property(self::PROPERTY_EXTERNAL_USER_ID, $external_user_id);
        }
    }

    /**
     *
     * @return string
     */
    public function get_external_user_id()
    {
        return $this->get_default_property(self::PROPERTY_EXTERNAL_USER_ID);
    }

    /**
     *
     * @param $state int
     */
    public function set_state($state)
    {
        $this->set_default_property(self::PROPERTY_STATE, $state);
    }

    /**
     *
     * @return int
     */
    public function get_state()
    {
        return $this->get_default_property(self::PROPERTY_STATE);
    }

    public function set_creation_date($created)
    {
        if (isset($created))
        {
            $this->set_default_property(self::PROPERTY_CREATED, $created);
        }
    }

    public function get_creation_date()
    {
        return $this->get_default_property(self::PROPERTY_CREATED);
    }

    public function set_modification_date($modified)
    {
        if (isset($modified))
        {
            $this->set_default_property(self::PROPERTY_MODIFIED, $modified);
        }
    }

    public function get_modification_date()
    {
        return $this->get_default_property(self::PROPERTY_MODIFIED);
    }

    /**
     *
     * @param $property_names array
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        $extended_property_names[] = self::PROPERTY_CREATED;
        $extended_property_names[] = self::PROPERTY_MODIFIED;
        $extended_property_names[] = self::PROPERTY_CONTENT_OBJECT_ID;
        $extended_property_names[] = self::PROPERTY_CONTENT_OBJECT_TIMESTAMP;
        $extended_property_names[] = self::PROPERTY_EXTERNAL_ID;
        $extended_property_names[] = self::PROPERTY_EXTERNAL_OBJECT_ID;
        $extended_property_names[] = self::PROPERTY_EXTERNAL_OBJECT_TIMESTAMP;
        $extended_property_names[] = self::PROPERTY_EXTERNAL_USER_ID;
        $extended_property_names[] = self::PROPERTY_STATE;
        
        return parent::get_default_property_names($extended_property_names);
    }

    /**
     *
     * @return boolean
     */
    public function create()
    {
        $now = time();
        $this->set_creation_date($now);
        $this->set_modification_date($now);
        return parent::create();
    }

    /**
     *
     * @return boolean
     */
    public function update()
    {
        if (! $this->is_identified())
        {
            throw new Exception('ExternalSync object could not be saved as its identity is not set');
        }
        
        $this->set_modification_date(time());
        return parent::update();
    }

    /**
     *
     * @return ContentObject
     */
    public function get_content_object()
    {
        if (! isset($this->content_object))
        {
            $this->content_object = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
                ContentObject::class_name(), 
                $this->get_content_object_id());
        }
        return $this->content_object;
    }

    /**
     *
     * @return ExternalObject
     */
    public function get_external_object()
    {
        if (! isset($this->external_object))
        {
            if (! $this->get_external())
            {
                throw new Exception(Translation::get('NoExternalnstanceFound'));
            }
            
            $this->external_object = DataConnector::getInstance($this->get_external())->retrieve_external_object(
                $this);
        }
        return $this->external_object;
    }

    /**
     *
     * @return ExternalRepository
     */
    public function get_external()
    {
        if (! isset($this->external))
        {
            $this->external = DataManager::retrieve_by_id(Instance::class_name(), $this->get_external_id());
        }
        return $this->external;
    }

    public function get_synchronization_status($content_object_date = null, $external_object_date = null)
    {
        if (! isset($this->synchronization_status))
        {
            if (is_null($content_object_date))
            {
                $content_object_date = $this->get_content_object()->get_modification_date();
            }
            if (is_null($external_object_date))
            {
                $external_object_date = $this->get_external_object()->get_created();
            }
            
            if ($content_object_date > $this->get_content_object_timestamp())
            {
                if ($external_object_date > $this->get_external_object_timestamp())
                {
                    $this->synchronization_status = self::SYNC_STATUS_CONFLICT;
                }
                elseif ($external_object_date == $this->get_external_object_timestamp())
                {
                    $this->synchronization_status = self::SYNC_STATUS_EXTERNAL;
                }
                else
                {
                    $this->synchronization_status = self::SYNC_STATUS_ERROR;
                }
            }
            elseif ($content_object_date == $this->get_content_object_timestamp())
            {
                if ($external_object_date > $this->get_external_object_timestamp())
                {
                    $this->synchronization_status = self::SYNC_STATUS_INTERNAL;
                }
                elseif ($external_object_date == $this->get_external_object_timestamp())
                {
                    $this->synchronization_status = self::SYNC_STATUS_IDENTICAL;
                }
                else
                {
                    $this->synchronization_status = self::SYNC_STATUS_ERROR;
                }
            }
            else
            {
                $this->synchronization_status = self::SYNC_STATUS_ERROR;
            }
        }
        
        return $this->synchronization_status;
    }

    /**
     * *********************************************************************** Fat model methods
     * ***********************************************************************
     */
    
    /**
     *
     * @param $content_object_id int
     * @return ExternalSync
     */
    public static function get_by_content_object_id($content_object_id)
    {
        $conditions = new EqualityCondition(
            new PropertyConditionVariable(self::class_name(), self::PROPERTY_CONTENT_OBJECT_ID), 
            new StaticConditionVariable($content_object_id));
        return DataManager::retrieve_synchronization_data($conditions);
    }

    /**
     *
     * @param $content_object_id int
     * @param $external_id int
     * @return ExternalSync
     */
    public static function get_by_content_object_id_and_external_id($content_object_id, $external_id)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(self::class_name(), self::PROPERTY_CONTENT_OBJECT_ID), 
            new StaticConditionVariable($content_object_id));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(self::class_name(), self::PROPERTY_EXTERNAL_ID), 
            new StaticConditionVariable($external_id));
        $condition = new AndCondition($conditions);
        return DataManager::retrieve_synchronization_data($condition);
    }

    /**
     *
     * @param $external_object_id int
     * @param $external_id int
     * @return ExternalSync
     */
    public static function get_by_external_object_id_and_external_id($external_object_id, $external_id)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(self::class_name(), self::PROPERTY_EXTERNAL_OBJECT_ID), 
            new StaticConditionVariable($external_object_id));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(self::class_name(), self::PROPERTY_EXTERNAL_ID), 
            new StaticConditionVariable($external_id));
        $condition = new AndCondition($conditions);
        
        return DataManager::retrieve_synchronization_data($conditions);
    }

    /**
     *
     * @param $content_object ContentObject
     * @param $external_object ExternalObject
     * @param $external_id int
     * @return boolean
     */
    public static function quicksave(ContentObject $content_object, $external_object, $external_id)
    {
        $sync = new self();
        $sync->set_content_object_id($content_object->get_id());
        $sync->set_content_object_timestamp($content_object->get_modification_date());
        $sync->set_external_id((int) $external_id);
        $sync->set_external_object_id((string) $external_object->get_id());
        $sync->set_external_object_timestamp($external_object->get_modified());
        $sync->set_external_user_id($external_object->get_owner_id());
        $sync->set_state(self::STATE_ACTIVE);
        return $sync->create();
    }
}
