<?php
namespace Chamilo\Core\Repository\ContentObject\PeerAssessment\Builder\Php\Lib\Manager;

/**
 * A class implements the <code>PeerAssessmentDisplaySupport</code> interface to indicate that it will serve as a launch
 * base for a repository\content_object\peer_assessment\builder.
 * 
 * @author Renaat De Muynck
 */
interface PeerAssessmentBuilderSupport
{
    
    // region user
    
    /**
     * Get all the users
     * 
     * @return array The users
     */
    // public function get_all_users($publication_id);
    
    // endregion user
    
    // region group
    
    /**
     * Get a group by id
     * 
     * @return The group
     */
    public function get_group($id);

    /**
     * Get all the groups
     * 
     * @return array The groups
     */
    public function get_groups($publication_id);

    public function delete_group($id);

    /**
     * Adds user to peer assessment group
     * 
     * @param int $user_id
     * @param int $group_id
     * @return bool
     */
    public function add_user_to_group($user_id, $group_id);

    /**
     * Removes user from peer assessment group
     * 
     * @param int $user_id
     * @param int $group_id
     * @return bool
     */
    public function remove_user_from_group($user_id, $group_id);

    /**
     * Get the group in which the current user is subscribed
     * 
     * @return array The group
     */
    public function get_user_group($user_id = null);

    /**
     * Get the users in the group
     * 
     * @return array The users
     */
    public function get_group_users($group_id);

    /**
     * Count the users in the group
     * 
     * @return array The number of users
     */
    public function count_group_users($group_id);
    
    // endregion group
    
    // region attempt
    
    /**
     * Get an attempt by id
     * 
     * @param int $id The id of the attempt
     */
    public function get_attempt($id = null);

    /**
     * Get all the attempts for this peer assessment
     * 
     * @param int $publication_id The id of the publication
     * @return array An array of attempts
     */
    public function get_attempts($publication_id);

    /**
     * Delete an attempt
     * 
     * @param int $id The id of the attempt
     */
    public function delete_attempt($id);

/**
 * Closes the attempt for all the users
 * 
 * @param int $id The id of the attempt
 */
    // public function close_attempt($id);

/**
 * Make an attempt visible/invisible for the users
 * 
 * @param int $id The id of the attempt
 */
    // public function toggle_attempt_visibility($id);
    
    // endregion attempt
    
    // region attempt_status

/**
 * Gets a user's current attempt status If no record exists in the database this should return a new empty object
 * 
 * @param int $user_id
 * @param int $attempt_id
 * @return \application\weblcms\WeblcmsPeerAssessmentAttemptStatusTracker
 */
    // public function get_user_attempt_status($user_id, $attempt_id);

/**
 * Update the stored value of the user's factorscore WARNING: this value should be updated in the following cases: *
 * every time another user in the same group submits new scores * a user is added or removed from the same group
 * 
 * @param int $user_id
 * @param int $attempt_id
 * @return float The updated value
 */
    // public function update_user_attempt_factor($user_id, $attempt_id);

/**
 * Update the stored value of the user's progress (in percent) WARNING: this value should be updated in the following
 * cases: * every time another the user submits new scores * a user is added or removed from the same group
 * 
 * @param int $user_id
 * @param int $attempt_id
 * @return float The updated value
 */
    // public function update_user_attempt_progress($user_id, $attempt_id);

/**
 * Closes a user's attempt
 * 
 * @param int $user_id
 * @param int $attempt_id
 */
    // public function close_user_attempt($user_id, $attempt_id);
    
    // public function open_user_attempt($user_id, $attempt_id);
    
    // endregion attempt_status
    
    // region scores

/**
 * Gets the scores the user received from his peers
 * 
 * @param int $user_id
 * @param int $attempt_id
 * @return int[][] Returns a wto dimensional array (matrix) with the scores
 */
    // public function get_user_scores_received($user_id, $attempt_id);

/**
 * Gets the scores the user gave to his peers
 * 
 * @param int $user_id
 * @param int $attempt_id
 * @return int[][] Returns a two dimensional array (matrix) with the scores
 */
    // public function get_user_scores_given($user_id, $attempt_id);

/**
 * Saves the scores of the current attempt
 * 
 * @param int $user_id
 * @param int $attempt_id The id of the current attempt
 * @param int[][] $scores A two dimensional array (matrix) in the form of scores[user][indicator]
 */
    // public function save_scores($user_id, $attempt_id, array $scores);
    
    // endregion scores
    
    // region indicator

/**
 * Get the indicators of this peer assessment
 * 
 * @return array The indicators
 */
    // public function get_indicators();

/**
 * Count the indicators of this peer assessment
 * 
 * @return array The number of indicators
 */
    // public function count_indicators();
    
    // endregion indicator
    
    // public function get_context_group($context_group_id);

/**
 *
 * @param int $context_group_id
 * @return array
 */
    // public function get_context_group_users($context_group_id);
    
    // public function save_feedback($user_id, $attempt_id, array $feedback);
    
    // public function get_user_feedback_given($user_id, $attempt_id);
    // public function get_user_feedback_received($user_id, $attempt_id);
}
