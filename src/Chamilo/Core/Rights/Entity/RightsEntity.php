<?php
namespace Chamilo\Core\Rights\Entity;

/**
 * @package Chamilo\Core\Rights\Entity
 *
 * @deprecated Use \Chamilo\Libraries\Rights\Interfaces\RightsEntityProvider now
 */
interface RightsEntity
{

    public function count_entity_items($condition = null);

    public function getElementFinderType();

    public static function getElementFinderTypeInstance();

    public function get_element_finder_element($id);

    public function get_entity_icon();

    public function get_entity_name();

    public function get_entity_translated_name();

    public function get_search_properties();

    public function retrieve_entity_items($condition = null, $offset = null, $count = null, $order_property = null);
}
