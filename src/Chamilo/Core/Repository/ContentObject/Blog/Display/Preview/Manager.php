<?php
namespace Chamilo\Core\Repository\ContentObject\Blog\Display\Preview;

abstract class Manager extends \Chamilo\Core\Repository\Display\Preview
{
  /**
     * Preview mode, so always return true.
     *
     * @param $right
     * @return boolean
     */
    public function is_allowed($right)
    {
        return true;
    }

    // FUNCTIONS FOR COMPLEX DISPLAY SUPPORT
    public function is_allowed_to_edit_content_object()
    {
        return true;
    }

    public function is_allowed_to_view_content_object()
    {
        return true;
    }

    public function is_allowed_to_add_child()
    {
        return true;
    }

    public function is_allowed_to_delete_child()
    {
        return true;
    }

    public function is_allowed_to_delete_feedback()
    {
        return true;
    }

    public function is_allowed_to_edit_feedback()
    {
        return true;
    }
}
