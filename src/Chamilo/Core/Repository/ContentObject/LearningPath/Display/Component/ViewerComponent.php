<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Component;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\AbstractItemAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Embedder\Embedder;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\PrerequisitesTranslator;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;

class ViewerComponent extends TabComponent
{

    private $learning_path_trackers;

    private $learning_path_menu;

    private $navigation;
    const TRACKER_LEARNING_PATH = 'tracker_learning_path';
    const TRACKER_LEARNING_PATH_ITEM = 'tracker_learning_path_item';

    public function build()
    {
        $show_progress = Request :: get(self :: PARAM_SHOW_PROGRESS);
        $learning_path = $this->get_parent()->get_root_content_object();

        $trail = BreadcrumbTrail :: get_instance();

        if (! $learning_path)
        {
            $html = array();

            $html[] = $this->render_header();
            $html[] = $this->display_error_message(Translation :: get('NoObjectSelected'));
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }

        // Process some tracking
        $this->learning_path_trackers[self :: TRACKER_LEARNING_PATH] = $this->get_parent()->retrieve_learning_path_tracker();

        // // Get the currently displayed content object
        $this->set_complex_content_object_item($this->get_current_complex_content_object_item());

        // Update the main tracker
        $this->learning_path_trackers[self :: TRACKER_LEARNING_PATH]->set_progress(
            $this->get_complex_content_object_path()->get_progress());
        $this->learning_path_trackers[self :: TRACKER_LEARNING_PATH]->update();

        $translator = new PrerequisitesTranslator($this->get_current_node());

        if (! $translator->can_execute())
        {
            $html = array();

            $html[] = $this->render_header();
            $html[] = '<div class="error-message">' . Translation :: get('NotYetAllowedToView') . '</div>';
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }

        $learning_path_item_attempt = $this->get_current_node()->get_current_attempt();
        
        if (! $learning_path_item_attempt instanceof AbstractItemAttempt)
        {
            $learning_path_item_attempt = $this->get_parent()->create_learning_path_item_tracker(
                $this->learning_path_trackers[self :: TRACKER_LEARNING_PATH],
                $this->get_complex_content_object_item());
            $this->get_current_node()->set_current_attempt($learning_path_item_attempt);
        }
        else
        {
            $learning_path_item_attempt->set_start_time(time());
            $learning_path_item_attempt->update();
        }
        
        $embedder = Embedder :: factory($this, $this->get_current_node());

        $html = array();

        $html[] = $this->render_header();
        $html[] = $embedder->run();
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     * Builds the attachment url TODO: Currently moved the complex content object item to the selected complex content
     * object item because the wrong parameter was used in the viewer
     *
     * @param $attachment ContentObject
     *
     * @return string
     */
    public function get_content_object_display_attachment_url($attachment)
    {
        $selected_complex_content_object_item_id = $this->get_current_complex_content_object_item()->get_id();

        return parent :: get_content_object_display_attachment_url(
            $attachment,
            $selected_complex_content_object_item_id);
    }
}
