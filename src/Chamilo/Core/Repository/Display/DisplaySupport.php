<?php
namespace Chamilo\Core\Repository\Display;

/**
 * A class implements the <code>DisplaySupport</code> interface to indicate that it will serve as a launch base for a
 * complex content object display.
 * Typically this interface will never be used directly, but will be extended by content
 * object type specific interfaces.
 * 
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface DisplaySupport
{

    /**
     * Determine the complex content object that should be displayed
     * 
     * @return ContentObject
     */
    public function get_root_content_object();

    /**
     * Determine whether a user has the necessary permissions
     * 
     * @param int $right
     * @return boolean
     */
    public function is_allowed($right);

    public function is_allowed_to_view_content_object();

    public function is_allowed_to_edit_content_object();

    public function is_allowed_to_add_child();

    public function is_allowed_to_delete_child();

    public function is_allowed_to_delete_feedback();

    public function is_allowed_to_edit_feedback();
}
