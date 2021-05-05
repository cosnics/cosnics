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
                self::PROPERTY_CONTEXT_CLASS,
                self::PROPERTY_CONTEXT_ID
            )
        );
    }

    /**
     * @return string
     */
    public function getContextClass()
    {
        return $this->get_default_property(self::PROPERTY_CONTEXT_CLASS);
    }

    /**
     * @param string $context_class
     */
    public function setContextClass($context_class)
    {
        $this->set_default_property(self::PROPERTY_CONTEXT_CLASS, $context_class);
    }

    /**
     * @return int
     */
    public function getContextId()
    {
        return $this->get_default_property(self::PROPERTY_CONTEXT_ID);
    }

    /**
     * @param int $context_id
     */
    public function setContextId($context_id)
    {
        $this->set_default_property(self::PROPERTY_CONTEXT_ID, $context_id);
    }

    /**
     * @return string
     */
    public static function get_table_name()
    {
        return 'repository_learning_path_step_context';
    }
}