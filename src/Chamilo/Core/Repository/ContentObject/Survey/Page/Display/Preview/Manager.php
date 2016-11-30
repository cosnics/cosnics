<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Display\Preview;

use Chamilo\Libraries\Platform\Session\Request;

/**
 *
 * @package repository.content_object.survey_page
 * @author Eduard Vossen
 * @author Magali Gillard
 */
abstract class Manager extends \Chamilo\Core\Repository\Display\Preview
{

    /**
     *
     * @see \core\repository\display\Preview::get_root_content_object()
     */
    function get_root_content_object()
    {
        $this->set_parameter(
            \Chamilo\Core\Repository\ContentObject\Survey\Page\Display\Manager::PARAM_PAGE_ITEM_ID, 
            Request::get(\Chamilo\Core\Repository\ContentObject\Survey\Page\Display\Manager::PARAM_PAGE_ITEM_ID));
        $this->set_parameter(
            \Chamilo\Core\Repository\Display\Manager::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID, 
            Request::get(\Chamilo\Core\Repository\Display\Manager::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID));
        return parent::get_root_content_object();
    }

    public function is_allowed_to_edit_content_object()
    {
        return true;
    }

    public function is_allowed_to_view_content_object()
    {
        return true;
    }
}
?>