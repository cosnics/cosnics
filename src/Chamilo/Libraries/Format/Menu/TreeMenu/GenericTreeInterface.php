<?php
namespace Chamilo\Libraries\Format\Menu\TreeMenu;

/*
 * To change this template, choose Tools | Templates and open the template in the editor. @author Pieterjan Broekaert
 */
interface GenericTreeInterface
{

    public function get_node_url($node_id);

    public function get_current_node_id();

    public function get_node($node_id);

    public function node_has_children($node_id);

    public function get_node_children($node_id);

    public function get_search_url();

    public function get_url_format();

    public function get_root_node_class();

    public function get_node_class($node);

    public function get_root_node_title();

    public function get_node_title($node);

    public function get_node_safe_title($node);

    public function get_node_id($node);

    public function get_node_parent($node);
}
