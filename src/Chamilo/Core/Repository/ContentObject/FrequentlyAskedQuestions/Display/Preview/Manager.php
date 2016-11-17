<?php
namespace Chamilo\Core\Repository\ContentObject\FrequentlyAskedQuestions\Display\Preview;

use Chamilo\Core\Repository\Common\Path\ComplexContentObjectPathNode;
use Chamilo\Libraries\Platform\Session\Request;

abstract class Manager extends \Chamilo\Core\Repository\Display\Preview
{

    /**
     *
     * @see \core\repository\display\Preview::get_root_content_object()
     */
    function get_root_content_object()
    {
        $this->set_parameter(
            \Chamilo\Core\Repository\ContentObject\FrequentlyAskedQuestions\Display\Manager::PARAM_FAQ_ITEM_ID, 
            Request::get(
                \Chamilo\Core\Repository\ContentObject\FrequentlyAskedQuestions\Display\Manager::PARAM_FAQ_ITEM_ID));
        $this->set_parameter(
            \Chamilo\Core\Repository\Display\Manager::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID, 
            Request::get(\Chamilo\Core\Repository\Display\Manager::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID));
        return parent::get_root_content_object();
    }

    /*
     * (non-PHPdoc) @see \core\repository\content_object\portfolio\PortfolioDisplaySupport::is_allowed()
     */
    public function is_allowed($right)
    {
        return true;
    }

    /*
     * (non-PHPdoc) @see
     * \core\repository\content_object\portfolio\PortfolioDisplaySupport::is_allowed_to_edit_content_object()
     */
    public function is_allowed_to_edit_content_object(ComplexContentObjectPathNode $node)
    {
        return true;
    }

    /*
     * (non-PHPdoc) @see
     * \core\repository\content_object\portfolio\PortfolioDisplaySupport::is_allowed_to_view_content_object()
     */
    public function is_allowed_to_view_content_object(ComplexContentObjectPathNode $node)
    {
        return true;
    }

    /*
     * (non-PHPdoc) @see \core\repository\DisplaySupport::is_allowed_to_add_child()
     */
    public function is_allowed_to_add_child()
    {
        return true;
    }

    /*
     * (non-PHPdoc) @see \core\repository\DisplaySupport::is_allowed_to_delete_child()
     */
    public function is_allowed_to_delete_child()
    {
        return true;
    }
}
