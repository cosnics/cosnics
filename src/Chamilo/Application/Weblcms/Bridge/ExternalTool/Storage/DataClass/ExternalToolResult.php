<?php

namespace Chamilo\Application\Weblcms\Bridge\ExternalTool\Storage\DataClass;

use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * @package Chamilo\Application\Weblcms\Bridge\ExternalTool\Storage\DataClass
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ExternalToolResult extends DataClass
{
    const PROPERTY_CONTENT_OBJECT_PUBLICATION_ID = 'content_object_publication_id';
    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_RESULT = 'result';

    /**
     * @param array $extendedPropertyNames
     *
     * @return string[]
     */
    public static function get_default_property_names($extendedPropertyNames = array())
    {
        $extendedPropertyNames[] = self::PROPERTY_CONTENT_OBJECT_PUBLICATION_ID;
        $extendedPropertyNames[] = self::PROPERTY_USER_ID;
        $extendedPropertyNames[] = self::PROPERTY_RESULT;

        return parent::get_default_property_names($extendedPropertyNames);
    }

    /**
     * @return int
     */
    public function getContentObjectPublicationId()
    {
        return $this->get_default_property(self::PROPERTY_CONTENT_OBJECT_PUBLICATION_ID);
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->get_default_property(self::PROPERTY_USER_ID);
    }

    /**
     * @return int
     */
    public function getResult()
    {
        return $this->get_default_property(self::PROPERTY_RESULT);
    }

    /**
     * @param int $contentObjectPublicationId
     *
     * @return \Chamilo\Application\Weblcms\Bridge\ExternalTool\Storage\DataClass\ExternalToolResult
     */
    public function setContentObjectPublicationId(int $contentObjectPublicationId)
    {
        $this->set_default_property(self::PROPERTY_CONTENT_OBJECT_PUBLICATION_ID, $contentObjectPublicationId);

        return $this;
    }

    /**
     * @param int $userId
     *
     * @return \Chamilo\Application\Weblcms\Bridge\ExternalTool\Storage\DataClass\ExternalToolResult
     */
    public function setUserId(int $userId)
    {
        $this->set_default_property(self::PROPERTY_USER_ID, $userId);

        return $this;
    }

    /**
     * @param int $result
     *
     * @return \Chamilo\Application\Weblcms\Bridge\ExternalTool\Storage\DataClass\ExternalToolResult
     */
    public function setResult(int $result)
    {
        $this->set_default_property(self::PROPERTY_RESULT, $result);

        return $this;
    }

    /**
     * @param float $ltiResult
     *
     * @return \Chamilo\Application\Weblcms\Bridge\ExternalTool\Storage\DataClass\ExternalToolResult
     */
    public function fromLTIResult(float $ltiResult)
    {
        $this->setResult(intval($ltiResult * 100));

        return $this;
    }

    /**
     * @return float
     */
    public function toLTIResult()
    {
        return floatval($this->getResult() / 100);
    }
}