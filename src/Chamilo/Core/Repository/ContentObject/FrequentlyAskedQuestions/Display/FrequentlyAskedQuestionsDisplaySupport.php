<?php
namespace Chamilo\Core\Repository\ContentObject\FrequentlyAskedQuestions\Display;

use Chamilo\Core\Repository\Common\Path\ComplexContentObjectPathNode;

interface FrequentlyAskedQuestionsDisplaySupport
{

    /**
     * Determine the complex content object that should be displayed
     *
     * @return ContentObject
     * @todo Fix DisplaySupport conflicts
     */
    public function get_root_content_object();

    /**
     * Return a URI-template for the portfolio tree menu
     *
     * @return string
     */
    public function get_frequently_asked_questions_tree_menu_url();

    /**
     * Is the user allowed to edit the content for the given node
     *
     * @param ComplexContentObjectPathNode $node
     * @return boolean
     */
   //public function is_allowed_to_edit_content_object(ComplexContentObjectPathNode $node);

    /**
     * Is the user allowed to view the content for the given node
     *
     * @param ComplexContentObjectPathNode $node
     * @return boolean
     */
    //public function is_allowed_to_view_content_object(ComplexContentObjectPathNode $node);

    /**
     *
     * @return \libraries\format\DynamicVisualTab[]
     */
    public function get_frequently_asked_questions_additional_tabs();

    /**
     * Determine whether the portfolio being displayed is the user's own portfolio
     *
     * @return boolean
     */
    public function is_own_frequently_asked_questions();
}
