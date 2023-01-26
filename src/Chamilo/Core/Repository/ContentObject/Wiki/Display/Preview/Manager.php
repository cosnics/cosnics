<?php
namespace Chamilo\Core\Repository\ContentObject\Wiki\Display\Preview;

use Chamilo\Core\Repository\Common\Path\ComplexContentObjectPathNode;
use Chamilo\Core\Repository\Display\Preview;
use Chamilo\Libraries\Translation\Translation;

abstract class Manager extends Preview
{

    public const CONTEXT = __NAMESPACE__;

    /**
     * Functionality is publication dependent, so not available in preview mode.
     */
    public function get_publication()
    {
        $this->not_available(Translation::get('ImpossibleInPreviewMode'));
    }

    /**
     * Preview mode, so always return true.
     *
     * @param $right
     *
     * @return bool
     */
    public function is_allowed($right)
    {
        return true;
    }

    // FUNCTIONS FOR COMPLEX DISPLAY SUPPORT

    public function is_allowed_to_add_child()
    {
        return true;
    }

    public function is_allowed_to_delete_child()
    {
        return true;
    }

    public function is_allowed_to_delete_feedback($feedback)
    {
        return true;
    }

    public function is_allowed_to_edit_content_object(ComplexContentObjectPathNode $node)
    {
        return true;
    }

    public function is_allowed_to_edit_feedback()
    {
        return true;
    }

    public function is_allowed_to_view_content_object(ComplexContentObjectPathNode $node)
    {
        return true;
    }
}
