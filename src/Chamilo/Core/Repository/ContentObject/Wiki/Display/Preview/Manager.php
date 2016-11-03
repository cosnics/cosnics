<?php
namespace Chamilo\Core\Repository\ContentObject\Wiki\Display\Preview;

use Chamilo\Libraries\Platform\Translation;

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

    /**
     * Functionality is publication dependent, so not available in preview mode.
     */
    public function get_publication()
    {
        $this->not_available(Translation :: get('ImpossibleInPreviewMode'));
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
