<?php
namespace Chamilo\Core\Repository\ContentObject\Evaluation\Storage\DataClass;

use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * @package Chamilo\Core\Repository\ContentObject\Evaluation\Storage\DataClass
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class EvaluationEntry extends DataClass
{
    const PROPERTY_EVALUATION_ID = 'evaluation_id';
    const PROPERTY_CONTEXT_CLASS = 'context_class';
    const PROPERTY_CONTEXT_ID = 'context_id';
    const PROPERTY_ENTITY_TYPE = 'entity_type';
    const PROPERTY_ENTITY_ID = 'entity_id';

    /**
     * Get the default properties
     *
     * @return array The property names.
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        return parent::get_default_property_names(
            array(
                self::PROPERTY_EVALUATION_ID,
                self::PROPERTY_CONTEXT_CLASS,
                self::PROPERTY_CONTEXT_ID,
                self::PROPERTY_ENTITY_TYPE,
                self::PROPERTY_ENTITY_ID
            )
        );
    }

    /**
     * @return int
     */
    public function getEvaluationId()
    {
        return $this->get_default_property(self::PROPERTY_EVALUATION_ID);
    }

    /**
     * @param int $evaluationId
     */
    public function setEvaluationId($evaluationId)
    {
        $this->set_default_property(self::PROPERTY_EVALUATION_ID, $evaluationId);
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
     *
     * @return int
     */
    public function getEntityType()
    {
        return $this->get_default_property(self::PROPERTY_ENTITY_TYPE);
    }

    /**
     *
     * @param int $entityType
     */
    public function setEntityType($entityType)
    {
        $this->set_default_property(self::PROPERTY_ENTITY_TYPE, $entityType);
    }

    /**
     * @return int
     */
    public function getEntityId()
    {
        return $this->get_default_property(self::PROPERTY_ENTITY_ID);
    }

    /**
     * @param int $entity_id
     */
    public function setEntityId($entity_id)
    {
        $this->set_default_property(self::PROPERTY_ENTITY_ID, $entity_id);
    }

    public static function get_table_name()
    {
        return 'repository_evaluation_entry';
    }
}

