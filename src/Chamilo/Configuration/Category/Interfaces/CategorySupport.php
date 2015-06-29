<?php
namespace Chamilo\Configuration\Category\Interfaces;

interface CategorySupport
{

    public function get_category();

    public function allowed_to_delete_category($category_id);

    public function allowed_to_edit_category($category_id);

    public function allowed_to_change_category_visibility($category_id);

    public function count_categories($condition);

    public function retrieve_categories($condition, $offset, $count, $order_property);

    public function get_next_category_display_order($parent_id);

    public function get_category_parameters();
}
