<?php
namespace Chamilo\Core\Rights\Entity;

interface NestedRightsEntity extends RightsEntity
{

    public function get_id_property();

    public function get_parent_property();

    public function get_title_property();

    public function get_root_ids();

    public function get_xml_feed();
}
