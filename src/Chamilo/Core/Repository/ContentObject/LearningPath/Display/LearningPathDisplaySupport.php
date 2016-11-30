<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display;

use Chamilo\Core\Repository\Display\DisplaySupport;

/**
 * A class implements the <code>LearningPathDisplaySupport</code> interface to indicate that it will serve as a launch
 * base for a repository\content_object\learning_path\display.
 * 
 * @author Hans De Bisschop
 */
interface LearningPathDisplaySupport extends DisplaySupport
{

    /**
     * Is the current user allowed to edit / delete attempt data for the currently displayed learning path
     * 
     * @return boolean
     */
    public function is_allowed_to_edit_learning_path_attempt_data();

    /**
     *
     * @return \core\repository\content_object\learning_path\display\AbstractAttempt
     */
    public function retrieve_learning_path_tracker();

    /**
     *
     * @param \core\repository\content_object\learning_path\display\AbstractAttempt $learning_path_tracker
     * @return \core\repository\content_object\learning_path\display\AbstractItemAttempt[]
     */
    public function retrieve_learning_path_tracker_items($learning_path_tracker);

    /**
     *
     * @param int $learning_path_item_attempt_id
     * @return \core\repository\content_object\learning_path\display\AbstractItemAttempt
     */
    public function retrieve_learning_path_item_attempt($learning_path_item_attempt_id);

    /**
     *
     * @return string
     */
    public function get_learning_path_tree_menu_url();

    /**
     * Creates a learning path item tracker
     * 
     * @param $learning_path_tracker AbstractAttemptTracker
     * @param $current_complex_content_object_item ComplexContentObjectItem
     * @return AbstractItemAttemptTracker[]
     */
    public function create_learning_path_item_tracker($learning_path_attempt, $current_complex_content_object_item);
}
