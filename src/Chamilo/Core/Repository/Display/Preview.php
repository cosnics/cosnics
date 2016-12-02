<?php
namespace Chamilo\Core\Repository\Display;

use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\Display;

/**
 *
 * @package core\repository\display
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Preview extends Application
{
    const PARAM_ACTION = 'preview_action';
    const ACTION_VIEW = 'Viewer';
    const DEFAULT_ACTION = self::ACTION_VIEW;

    public static function get_default_action()
    {
        return self::DEFAULT_ACTION;
    }

    /**
     * Method always has to be implemented for a class implementing the Display
     * 
     * @return \core\repository\ContentObject
     */
    public function get_root_content_object()
    {
        return $this->get_parent()->get_root_content_object();
    }

    /**
     * Inform the user that the requested functionality is not available in preview mode
     * 
     * @param string $message
     */
    public function not_available($message)
    {
        $html = array();
        
        $html[] = $this->render_header();
        $html[] = Display::normal_message($message);
        $html[] = $this->render_footer();
        
        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @param int $right
     * @return boolean
     */
    public function is_allowed($right)
    {
        return true;
    }

    /**
     *
     * @return boolean
     */
    public function is_allowed_to_add_child()
    {
        return true;
    }

    /**
     *
     * @return boolean
     */
    public function is_allowed_to_delete_child()
    {
        return true;
    }

    /**
     *
     * @return boolean
     */
    public function is_allowed_to_delete_feedback()
    {
        return true;
    }

    /**
     *
     * @return boolean
     */
    public function is_allowed_to_edit_content_object()
    {
        return true;
    }

    /**
     *
     * @return boolean
     */
    public function is_allowed_to_edit_feedback()
    {
        return true;
    }

    /**
     *
     * @return boolean
     */
    public function is_allowed_to_view_content_object()
    {
        return true;
    }

    /**
     *
     * @return boolean
     */
    public function supports_reset()
    {
        return $this instanceof PreviewResetSupport;
    }
}
