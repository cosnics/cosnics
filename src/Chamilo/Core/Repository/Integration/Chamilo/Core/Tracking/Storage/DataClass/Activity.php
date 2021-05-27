<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\Tracking\Storage\DataClass;

use Chamilo\Core\Tracking\Storage\DataClass\Tracker;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\Storage\DataManager;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Translation\Translation;

class Activity extends Tracker
{

    const ACTIVITY_ADD_ITEM = 6;
    const ACTIVITY_CREATED = 1;
    const ACTIVITY_DELETED = 2;
    const ACTIVITY_DELETE_ITEM = 7;
    const ACTIVITY_MOVE_ITEM = 8;
    const ACTIVITY_RECYCLE = 4;
    const ACTIVITY_RESTORE = 5;
    const ACTIVITY_UPDATED = 3;
    const ACTIVITY_UPDATE_ITEM = 9;

    const PROPERTY_CONTENT = 'content';
    const PROPERTY_CONTENT_OBJECT_ID = 'content_object_id';
    const PROPERTY_DATE = 'date';
    const PROPERTY_TYPE = 'type';
    const PROPERTY_USER_ID = 'user_id';

    /**
     *
     * @var \core\user\User
     */
    private $user;

    /**
     * Runs this tracker
     *
     * @param array $parameters
     *
     * @return bool
     */
    public function run(array $parameters = [])
    {
        $this->validate_parameters($parameters);

        return $this->save();
    }

    public function get_content()
    {
        return $this->get_default_property(self::PROPERTY_CONTENT);
    }

    public function get_content_object_id()
    {
        return $this->get_default_property(self::PROPERTY_CONTENT_OBJECT_ID);
    }

    public function get_date()
    {
        return $this->get_default_property(self::PROPERTY_DATE);
    }

    /**
     * Get the default properties of all activity
     *
     * @return array The property names.
     */
    public static function get_default_property_names($extended_property_names = [])
    {
        return parent::get_default_property_names(
            array(
                self::PROPERTY_TYPE,
                self::PROPERTY_USER_ID,
                self::PROPERTY_DATE,
                self::PROPERTY_CONTENT,
                self::PROPERTY_CONTENT_OBJECT_ID
            )
        );
    }

    /**
     * @return string
     */
    public static function get_table_name()
    {
        return 'tracking_repository_activity';
    }

    public function get_type()
    {
        return $this->get_default_property(self::PROPERTY_TYPE);
    }

    /**
     *
     * @return string
     */
    public function get_type_image()
    {
        return self::type_image($this->get_type());
    }

    /**
     *
     * @return string
     */
    public function get_type_string()
    {
        return self::type_string($this->get_type());
    }

    public function get_user()
    {
        if (!isset($this->user))
        {
            $this->user = DataManager::retrieve_by_id(
                User::class, $this->get_user_id()
            );
        }

        return $this->user;
    }

    public function get_user_id()
    {
        return $this->get_default_property(self::PROPERTY_USER_ID);
    }

    public function set_content($content)
    {
        $this->set_default_property(self::PROPERTY_CONTENT, $content);
    }

    public function set_content_object_id($content_object_id)
    {
        $this->set_default_property(self::PROPERTY_CONTENT_OBJECT_ID, $content_object_id);
    }

    /*
     * (non-PHPdoc) @see \tracking\Tracker::validate_parameters()
     */

    public function set_date($date)
    {
        $this->set_default_property(self::PROPERTY_DATE, $date);
    }

    public function set_type($type)
    {
        $this->set_default_property(self::PROPERTY_TYPE, $type);
    }

    public function set_user_id($user_id)
    {
        $this->set_default_property(self::PROPERTY_USER_ID, $user_id);
    }

    /**
     *
     * @param int $type_id
     *
     * @return string
     */
    public static function type_image($type_id)
    {
        switch ($type_id)
        {
            case self::ACTIVITY_CREATED :
                $glyphName = 'circle-plus';
                break;
            case self::ACTIVITY_DELETED :
                $glyphName = 'minus-circle';
                break;
            case self::ACTIVITY_UPDATED :
                $glyphName = 'edit';
                break;
            case self::ACTIVITY_RECYCLE :
                $glyphName = 'trash-alt';
                break;
            case self::ACTIVITY_RESTORE :
                $glyphName = 'undo';
                break;
            case self::ACTIVITY_ADD_ITEM :
                $glyphName = 'plus-square';
                break;
            case self::ACTIVITY_DELETE_ITEM :
                $glyphName = 'minus-square';
                break;
            case self::ACTIVITY_MOVE_ITEM :
                $glyphName = 'caret-square-right';
                break;
            case self::ACTIVITY_UPDATE_ITEM :
                $glyphName = 'pen-square';
                break;
            default :
                $glyphName = 'circle';
                break;
        }

        $glyph = new FontAwesomeGlyph($glyphName, [], null, 'fas');

        return $glyph->render();
    }

    /**
     *
     * @param int $type_id
     *
     * @return string
     */
    public static function type_string($type_id)
    {
        switch ($type_id)
        {
            case self::ACTIVITY_CREATED :
                $activity = 'ObjectCreated';
                break;
            case self::ACTIVITY_DELETED :
                $activity = 'ObjectDeleted';
                break;
            case self::ACTIVITY_UPDATED :
                $activity = 'ObjectUpdated';
                break;
            case self::ACTIVITY_RECYCLE :
                $activity = 'ObjectRecycled';
                break;
            case self::ACTIVITY_RESTORE :
                $activity = 'ObjectRestored';
                break;
            case self::ACTIVITY_ADD_ITEM :
                $activity = 'ItemAddedToObject';
                break;
            case self::ACTIVITY_DELETE_ITEM :
                $activity = 'ItemDeletedFromObject';
                break;
            case self::ACTIVITY_MOVE_ITEM :
                $activity = 'ItemMovedInObject';
                break;
            case self::ACTIVITY_UPDATE_ITEM :
                $activity = 'ItemUpdatedInObject';
                break;
            default :
                $activity = 'Unknown';
                break;
        }

        return Translation::get($activity);
    }

    public function validate_parameters(array $parameters = [])
    {
        $this->set_type($parameters[self::PROPERTY_TYPE]);
        $this->set_user_id((int) $parameters[self::PROPERTY_USER_ID]);
        $this->set_date($parameters[self::PROPERTY_DATE]);
        $this->set_content($parameters[self::PROPERTY_CONTENT]);
        $this->set_content_object_id((int) $parameters[self::PROPERTY_CONTENT_OBJECT_ID]);
    }
}