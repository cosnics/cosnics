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
    public static function get_default_property_names($extended_property_names = array()): array
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
    public function getEvaluationId(): int
    {
        return $this->get_default_property(self::PROPERTY_EVALUATION_ID);
    }

    /**
     * @param int $evaluationId
     */
    public function setEvaluationId(int $evaluationId)
    {
        $this->set_default_property(self::PROPERTY_EVALUATION_ID, $evaluationId);
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
     *
     * @return int
     */
    public function getEntityType(): int
    {
        return $this->get_default_property(self::PROPERTY_ENTITY_TYPE);
    }

    /**
     *
     * @param int $entityType
     */
    public function setEntityType(int $entityType)
    {
        $this->set_default_property(self::PROPERTY_ENTITY_TYPE, $entityType);
    }

    /**
     * @return int
     */
    public function getEntityId(): int
    {
        return $this->get_default_property(self::PROPERTY_ENTITY_ID);
    }

    /**
     * @param int $entity_id
     */
    public function setEntityId(int $entity_id)
    {
        $this->set_default_property(self::PROPERTY_ENTITY_ID, $entity_id);
    }

    public static function get_table_name(): string
    {
        return 'repository_evaluation_entry';
    }
}

