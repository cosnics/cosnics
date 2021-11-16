<?php
namespace Chamilo\Core\Repository\ContentObject\Evaluation\Storage\DataClass;

use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * @package Chamilo\Core\Repository\ContentObject\Evaluation\Storage\DataClass
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class EvaluationEntryScoreTargetUser extends DataClass
{
    const PROPERTY_SCORE_ID = 'score_id';
    const PROPERTY_TARGET_USER_ID = 'target_user_id';

    /**
     * Get the default properties
     *
     * @return array The property names.
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        return parent::get_default_property_names(
            array(
                self::PROPERTY_SCORE_ID,
                self::PROPERTY_TARGET_USER_ID
            )
        );
    }

    /**
     * @return int
     */
    public function getScoreId()
    {
        return $this->get_default_property(self::PROPERTY_SCORE_ID);
    }

    /**
     * @param int $scoreId
     */
    public function setScoreId($scoreId)
    {
        $this->set_default_property(self::PROPERTY_SCORE_ID, $scoreId);
    }

    /**
     * @return int
     */
    public function getTargetUserId()
    {
        return $this->get_default_property(self::PROPERTY_TARGET_USER_ID);
    }

    /**
     * @param int $target_user_id
     */
    public function setTargetUserId($target_user_id)
    {
        $this->set_default_property(self::PROPERTY_TARGET_USER_ID, $target_user_id);
    }

    public static function get_table_name()
    {
        return 'repository_evaluation_entry_score_target_user';
    }
}

