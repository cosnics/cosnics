<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\Tracking\Storage\DataClass;

use Chamilo\Core\Tracking\Storage\DataClass\Tracker;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Translation\Translation;

class Activity extends Tracker
{
    public const ACTIVITY_ADD_ITEM = 6;
    public const ACTIVITY_CREATED = 1;
    public const ACTIVITY_DELETED = 2;
    public const ACTIVITY_DELETE_ITEM = 7;
    public const ACTIVITY_MOVE_ITEM = 8;
    public const ACTIVITY_RECYCLE = 4;
    public const ACTIVITY_RESTORE = 5;
    public const ACTIVITY_UPDATED = 3;
    public const ACTIVITY_UPDATE_ITEM = 9;

    public const CONTEXT = 'Chamilo\Core\Repository\Integration\Chamilo\Core\Tracking';

    public const PROPERTY_CONTENT = 'content';
    public const PROPERTY_CONTENT_OBJECT_ID = 'content_object_id';
    public const PROPERTY_DATE = 'date';
    public const PROPERTY_TYPE = 'type';
    public const PROPERTY_USER_ID = 'user_id';

    /**
     * @var User
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

    /**
     * Get the default properties of all activity
     *
     * @return array The property names.
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames(
            [
                self::PROPERTY_TYPE,
                self::PROPERTY_USER_ID,
                self::PROPERTY_DATE,
                self::PROPERTY_CONTENT,
                self::PROPERTY_CONTENT_OBJECT_ID
            ]
        );
    }

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'tracking_repository_activity';
    }

    public function getType()
    {
        return $this->getDefaultProperty(self::PROPERTY_TYPE);
    }

    public function get_content()
    {
        return $this->getDefaultProperty(self::PROPERTY_CONTENT);
    }

    public function get_content_object_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_CONTENT_OBJECT_ID);
    }

    public function get_date()
    {
        return $this->getDefaultProperty(self::PROPERTY_DATE);
    }

    /**
     * @deprecated Use Activity::getType() now
     */
    public function get_type()
    {
        return $this->getType();
    }

    /**
     * @return string
     */
    public function get_type_image()
    {
        return self::type_image($this->getType());
    }

    /**
     * @return string
     */
    public function get_type_string(): string
    {
        return self::type_string($this->getType());
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
        return $this->getDefaultProperty(self::PROPERTY_USER_ID);
    }

    public function setType($type)
    {
        $this->setDefaultProperty(self::PROPERTY_TYPE, $type);
    }

    public function set_content($content)
    {
        $this->setDefaultProperty(self::PROPERTY_CONTENT, $content);
    }

    /*
     * (non-PHPdoc) @see \tracking\Tracker::validate_parameters()
     */

    public function set_content_object_id($content_object_id)
    {
        $this->setDefaultProperty(self::PROPERTY_CONTENT_OBJECT_ID, $content_object_id);
    }

    public function set_date($date)
    {
        $this->setDefaultProperty(self::PROPERTY_DATE, $date);
    }

    /**
     * @deprecated Use Activity::setType() now
     */
    public function set_type($type)
    {
        $this->setType($type);
    }

    public function set_user_id($user_id)
    {
        $this->setDefaultProperty(self::PROPERTY_USER_ID, $user_id);
    }

    /**
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
        $this->setType($parameters[self::PROPERTY_TYPE]);
        $this->set_user_id((int) $parameters[self::PROPERTY_USER_ID]);
        $this->set_date($parameters[self::PROPERTY_DATE]);
        $this->set_content($parameters[self::PROPERTY_CONTENT]);
        $this->set_content_object_id((int) $parameters[self::PROPERTY_CONTENT_OBJECT_ID]);
    }
}