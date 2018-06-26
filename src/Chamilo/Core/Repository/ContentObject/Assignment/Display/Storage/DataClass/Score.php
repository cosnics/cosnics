<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass;

use Chamilo\Libraries\Storage\DataClass\DataClass;
use Symfony\Component\Validator\Constraints as Assert;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Score extends DataClass
{
    const PROPERTY_SCORE = 'score';
    const PROPERTY_ENTRY_ID = 'entry_id';
    const PROPERTY_CREATED = 'created';
    const PROPERTY_MODIFIED = 'modified';
    const PROPERTY_USER_ID = 'user_id';

    /**
     *
     * @param string[] $extendedPropertyNames
     *
     * @return string[]
     */
    public static function get_default_property_names($extendedPropertyNames = array())
    {
        return parent::get_default_property_names(
            array(
                self::PROPERTY_SCORE,
                self::PROPERTY_ENTRY_ID,
                self::PROPERTY_CREATED,
                self::PROPERTY_MODIFIED,
                self::PROPERTY_USER_ID
            )
        );
    }

    /**
     * @Assert\Type(type="integer", message="NotANumber", payload = {"context": "Chamilo\Libraries"})
     * @Assert\LessThanOrEqual(value=100, message="LessThanOrEqual", payload = {"context": "Chamilo\Libraries"})
     * @return integer
     */
    public function getScore()
    {
        return $this->get_default_property(self::PROPERTY_SCORE);
    }

    /**
     *
     * @param integer $score
     */
    public function setScore($score)
    {
        $this->set_default_property(self::PROPERTY_SCORE, $score);
    }

    /**
     *
     * @return integer
     */
    public function getEntryId()
    {
        return $this->get_default_property(self::PROPERTY_ENTRY_ID);
    }

    /**
     *
     * @param integer $entryId
     */
    public function setEntryId($entryId)
    {
        $this->set_default_property(self::PROPERTY_ENTRY_ID, $entryId);
    }

    /**
     *
     * @return integer
     */
    public function getCreated()
    {
        return $this->get_default_property(self::PROPERTY_CREATED);
    }

    /**
     *
     * @param integer $created
     */
    public function setCreated($created)
    {
        $this->set_default_property(self::PROPERTY_CREATED, $created);
    }

    /**
     *
     * @return integer
     */
    public function getModified()
    {
        return $this->get_default_property(self::PROPERTY_MODIFIED);
    }

    /**
     *
     * @param integer $modified
     */
    public function setModified($modified)
    {
        $this->set_default_property(self::PROPERTY_MODIFIED, $modified);
    }

    /**
     *
     * @return integer
     */
    public function getUserId()
    {
        return $this->get_default_property(self::PROPERTY_USER_ID);
    }

    /**
     *
     * @param integer $userId
     */
    public function setUserId($userId)
    {
        $this->set_default_property(self::PROPERTY_USER_ID, $userId);
    }
}
