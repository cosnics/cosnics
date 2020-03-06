<?php
namespace Chamilo\Libraries\Format\Menu\TreeMenu;

/**
 *
 * @package Chamilo\Libraries\Format\Menu\TreeMenu
 * @author Pieterjan Broekaert
 */
interface GenericTreeInterface
{

    /**
     *
     * @return integer
     */
    public function get_current_node_id();

    /**
     *
     * @param unknown $nodeId
     *
     * @return \Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function get_node($nodeId);

    /**
     *
     * @param integer $nodeId
     *
     * @return \Chamilo\Libraries\Storage\ResultSet\ResultSet
     */
    public function get_node_children($nodeId);

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass $node
     *
     * @return string
     */
    public function get_node_class($node);

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass $node
     *
     * @return string
     */
    public function get_node_id($node);

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass $node
     *
     * @return \Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function get_node_parent($node);

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass $node
     *
     * @return string
     */
    public function get_node_safe_title($node);

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass $node
     *
     * @return string
     */
    public function get_node_title($node);

    /**
     *
     * @param integer $nodeId
     *
     * @return string
     */
    public function get_node_url($nodeId);

    /**
     *
     * @return string
     */
    public function get_root_node_class();

    /**
     *
     * @return string
     */
    public function get_root_node_title();

    /**
     *
     * @return string
     */
    public function get_search_url();

    /**
     *
     * @return string
     */
    public function get_url_format();

    /**
     *
     * @param unknown $nodeId
     *
     * @return boolean
     */
    public function node_has_children($nodeId);
}
