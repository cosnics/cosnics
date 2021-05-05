<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass;

use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 *
 * @package repository.lib.content_object.learning_path
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class LearningPathStepContext extends DataClass
{
    const PROPERTY_LEARNING_PATH_STEP_ID = 'learning_path_step_id';
    const PROPERTY_CONTEXT_CLASS = 'context_class';
    const PROPERTY_CONTEXT_ID = 'context_id';

    /**
     * Get the default properties
     *
     * @return array The property names.
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        return parent::get_default_property_names(
            array(
                self::PROPERTY_LEARNING_PATH_STEP_ID,
                self::PROPERTY_CONTEXT_CLASS,
                self::PROPERTY_CONTEXT_ID
            )
        );
    }

    /**
     * @return int
     */
    public function getLearningPathStepId(): int
    {
        return $this->get_default_property(self::PROPERTY_LEARNING_PATH_STEP_ID);
    }

    /**
     * @param int $step_id
     */
    public function setLearningPathStepId(int $step_id)
    {
        $this->set_default_property(self::PROPERTY_LEARNING_PATH_STEP_ID, $step_id);
    }

    /**
     * @return string
     */
    public function getContextClass(): string
    {
        return $this->get_default_property(self::PROPERTY_CONTEXT_CLASS);
    }

    /**
     * @param string $context_class
     */
    public function setContextClass(string $context_class)
    {
        $this->set_default_property(self::PROPERTY_CONTEXT_CLASS, $context_class);
    }

    /**
     * @return int
     */
    public function getContextId(): int
    {
        return $this->get_default_property(self::PROPERTY_CONTEXT_ID);
    }

    /**
     * @param int $context_id
     */
    public function setContextId(int $context_id)
    {
        $this->set_default_property(self::PROPERTY_CONTEXT_ID, $context_id);
    }

    /**
     * @return string
     */
    public static function get_table_name(): string
    {
        return 'repository_learning_path_step_context';
    }
}