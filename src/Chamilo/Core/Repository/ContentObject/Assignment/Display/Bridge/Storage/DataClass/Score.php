<?php
namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Score extends DataClass
{
    public const CONTEXT = Manager::CONTEXT;

    public const PROPERTY_CREATED = 'created';
    public const PROPERTY_ENTRY_ID = 'entry_id';
    public const PROPERTY_MODIFIED = 'modified';
    public const PROPERTY_SCORE = 'score';
    public const PROPERTY_USER_ID = 'user_id';

    /**
     * @return int
     */
    public function getCreated()
    {
        return $this->getDefaultProperty(self::PROPERTY_CREATED);
    }

    /**
     * @param string[] $extendedPropertyNames
     *
     * @return string[]
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames(
            [
                self::PROPERTY_SCORE,
                self::PROPERTY_ENTRY_ID,
                self::PROPERTY_CREATED,
                self::PROPERTY_MODIFIED,
                self::PROPERTY_USER_ID
            ]
        );
    }

    /**
     * @return int
     */
    public function getEntryId()
    {
        return $this->getDefaultProperty(self::PROPERTY_ENTRY_ID);
    }

    /**
     * @return int
     */
    public function getModified()
    {
        return $this->getDefaultProperty(self::PROPERTY_MODIFIED);
    }

    /**
     * @Assert\Type(type="integer", message="NotANumber", payload = {"context": "Chamilo\Libraries"})
     * @Assert\LessThanOrEqual(value=100, message="LessThanOrEqual", payload = {"context": "Chamilo\Libraries"})
     * @return int
     */
    public function getScore()
    {
        return $this->getDefaultProperty(self::PROPERTY_SCORE);
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->getDefaultProperty(self::PROPERTY_USER_ID);
    }

    /**
     * @param int $created
     */
    public function setCreated($created)
    {
        $this->setDefaultProperty(self::PROPERTY_CREATED, $created);
    }

    /**
     * @param int $entryId
     */
    public function setEntryId($entryId)
    {
        $this->setDefaultProperty(self::PROPERTY_ENTRY_ID, $entryId);
    }

    /**
     * @param int $modified
     */
    public function setModified($modified)
    {
        $this->setDefaultProperty(self::PROPERTY_MODIFIED, $modified);
    }

    /**
     * @param int $score
     */
    public function setScore($score)
    {
        $this->setDefaultProperty(self::PROPERTY_SCORE, $score);
    }

    /**
     * @param int $userId
     */
    public function setUserId($userId)
    {
        $this->setDefaultProperty(self::PROPERTY_USER_ID, $userId);
    }
}
