<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Display\Interfaces;

use Chamilo\Core\Repository\Common\Path\ComplexContentObjectPathNode;

/**
 * A class implements the <code>PageComplexDisplaySupport</code> interface to indicate that it will serve as a
 * launch
 * base for a PageComplexDisplay.
 * 
 * @author Hans De Bisschop
 */
interface PageDisplaySupport extends 
    \Chamilo\Core\Repository\ContentObject\Survey\Display\Interfaces\SurveyDisplaySupport
{

    /**
     * Determine the complex content object that should be displayed
     * 
     * @return ContentObject
     * @todo Fix DisplaySupport conflicts
     */
    public function get_root_content_object();

    /**
     * Return a URI-template for the page tree menu
     * 
     * @return string
     */
    public function get_tree_menu_url();

    /**
     * Is the user allowed to edit the content for the given node
     * 
     * @param ComplexContentObjectPathNode $node
     * @return boolean
     */
    public function is_allowed_to_edit_content_object();

    /**
     * Is the user allowed to view the content for the given node
     * 
     * @param ComplexContentObjectPathNode $node
     * @return boolean
     */
    public function is_allowed_to_view_content_object();

    /**
     *
     * @return \libraries\format\DynamicVisualTab[]
     */
    public function get_additional_tabs();

    /**
     * Determine whether the page being displayed is the user's own page
     * 
     * @return boolean
     */
    public function is_own_page();
}
?>