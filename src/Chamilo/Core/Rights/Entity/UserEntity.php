<?php
namespace Chamilo\Core\Rights\Entity;

use Chamilo\Core\Rights\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElement;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElementType;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\RetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * Class that describes the users for the rights editor
 *
 * @author Sven Vanpoucke
 * @deprecated Use the \Chamilo\Core\User\Integration\Chamilo\Libraries\Rights\Service\UserEntityProvider service now
 */
class UserEntity implements RightsEntity
{
    public const ENTITY_NAME = 'user';
    public const ENTITY_TYPE = 1;

    private static $instance;

    private $user_cache;

    /**
     * Get the fully qualified class name of the object
     *
     * @return string
     */
    public static function class_name()
    {
        return get_called_class();
    }

    /**
     * Counts the items for this entity
     *
     * @param $condition Condition
     *
     * @return int
     */
    public function count_entity_items($condition = null)
    {
        $condition = $this->get_condition($condition);

        return DataManager::count(
            User::class, new DataClassCountParameters(condition: $condition)
        );
    }

    /**
     * Returns the class name of the data class that is used for this entity
     *
     * @return string
     */
    public static function data_class_class_name()
    {
        return User::class;
    }

    public function getElementFinderType()
    {
        return static::getElementFinderTypeInstance();
    }

    /**
     * Retrieves the type for the advanced element finder for the simple rights editor
     */
    public static function getElementFinderTypeInstance()
    {
        return new AdvancedElementFinderElementType(
            'users', Translation::get('Users'), Manager::CONTEXT . '\Ajax', 'user_entity_feed', []
        );
    }

    public static function getInstance()
    {
        if (!isset(self::$instance))
        {
            self::$instance = new static();
        }

        return self::$instance;
    }

    /**
     * Function that can be filled in extensions of this class to limit the users
     *
     * @param ?\Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return Condition
     */
    public function get_condition($condition = null)
    {
        return $condition;
    }

    /**
     * Retrieves the type for the advanced element finder for the simple rights editor
     */
    public function get_element_finder_element($id)
    {
        $user = DataManager::retrieve_by_id(User::class, (int) $id);

        if (!$user)
        {
            return null;
        }

        $glyph = new FontAwesomeGlyph('user', [], null, 'fas');

        return new AdvancedElementFinderElement(
            static::ENTITY_TYPE . '_' . $id, $glyph->getClassNamesString(), $user->get_fullname(),
            $user->get_official_code()
        );
    }

    /**
     * Returns the path to the icon of the entity
     *
     * @return String
     */
    public function get_entity_icon()
    {
        return new FontAwesomeGlyph('user', [], null, 'fas');
    }

    /**
     * Returns the name of this entity
     *
     * @return String
     */
    public function get_entity_name()
    {
        return self::ENTITY_NAME;
    }

    /**
     * Returns the translated name of this entiry for displaying purposes only!
     *
     * @return String Translated name of the entity
     */
    public function get_entity_translated_name()
    {
        return Translation::get(
            (string) StringUtilities::getInstance()->createString(self::ENTITY_NAME)->upperCamelize()
        );
    }

    /**
     * Returns the type of this entity
     *
     * @return int
     */
    public function get_entity_type()
    {
        return static::ENTITY_TYPE;
    }

    /**
     * Returns the properties on which can be searched
     *
     * @return Array
     */
    public function get_search_properties()
    {
        return [
            User::PROPERTY_USERNAME,
            User::PROPERTY_FIRSTNAME,
            User::PROPERTY_LASTNAME,
            User::PROPERTY_OFFICIAL_CODE
        ];
    }

    /**
     * Retrieves the entity item ids relevant for a given user
     *
     * @param $user_id int
     *
     * @return array
     */
    public function retrieve_entity_item_ids_linked_to_user($user_id)
    {
        if (is_null($this->user_cache[$user_id]))
        {

            $this->user_cache[$user_id] = [$user_id];
        }

        return $this->user_cache[$user_id];
    }

    /**
     * Retrieves the items for this entity
     *
     * @param $condition Condition
     * @param $offset int
     * @param $count int
     * @param $order_property
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function retrieve_entity_items($condition = null, $offset = null, $count = null, $order_property = null)
    {
        $condition = $this->get_condition($condition);
        $parameters = new RetrievesParameters(
            condition: $condition, count: $count, offset: $offset, orderBy: $order_property
        );

        return DataManager::retrieves(User::class, $parameters);
    }
}
