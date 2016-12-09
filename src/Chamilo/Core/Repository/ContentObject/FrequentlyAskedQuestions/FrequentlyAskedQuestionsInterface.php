<?php
namespace Chamilo\Core\Repository\ContentObject\FrequentlyAskedQuestions;

/**
 * Interface that indicates the implementer supports actions on portfolio items of which it should be notified
 * 
 * @package repository\content_object\portfolio
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface FrequentlyAskedQuestionsInterface
{

    /**
     * Update references to a given set of node ids
     * 
     * @param int[] $old_node_ids
     * @param int[] $new_node_ids
     * @return boolean
     */
    public static function update_node_ids($old_node_ids, $new_node_ids);

    /**
     * Delete references to the given node ids
     * 
     * @param int[] $node_ids
     * @return boolean
     */
    public static function delete_node_ids($node_ids);
}
