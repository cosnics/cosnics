<?php
namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Feedback extends DataClass
{
    const PROPERTY_ENTRY_ID = 'entry_id';
    const PROPERTY_CREATED = 'created';
    const PROPERTY_MODIFIED = 'modified';
    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_CONTENT_OBJECT_ID = 'content_object_id';

    /**
     *
     * @param string[] $extendedPropertyNames
     * @return string[]
     */
    public static function get_default_property_names($extendedPropertyNames = array())
    {
        return parent :: get_default_property_names(
            array(
                self :: PROPERTY_ENTRY_ID,
                self :: PROPERTY_CREATED,
                self :: PROPERTY_MODIFIED,
                self :: PROPERTY_USER_ID,
                self :: PROPERTY_CONTENT_OBJECT_ID));
    }

    /**
     *
     * @return integer
     */
    public function getEntryId()
    {
        return $this->get_default_property(self :: PROPERTY_ENTRY_ID);
    }

    /**
     *
     * @param integer $entryId
     */
    public function setEntryId($entryId)
    {
        $this->set_default_property(self :: PROPERTY_ENTRY_ID, $entryId);
    }

    /**
     *
     * @return integer
     */
    public function getCreated()
    {
        return $this->get_default_property(self :: PROPERTY_CREATED);
    }

    /**
     *
     * @param integer $created
     */
    public function setCreated($created)
    {
        $this->set_default_property(self :: PROPERTY_CREATED, $created);
    }

    /**
     *
     * @return integer
     */
    public function getModified()
    {
        return $this->get_default_property(self :: PROPERTY_MODIFIED);
    }

    /**
     *
     * @param integer $modified
     */
    public function setModified($modified)
    {
        $this->set_default_property(self :: PROPERTY_MODIFIED, $modified);
    }

    /**
     *
     * @return integer
     */
    public function getContentObjectId()
    {
        return $this->get_default_property(self :: PROPERTY_CONTENT_OBJECT_ID);
    }

    /**
     *
     * @param integer $contentObjectId
     */
    public function setContentObjectId($contentObjectId)
    {
        $this->set_default_property(self :: PROPERTY_CONTENT_OBJECT_ID, $contentObjectId);
    }

    /**
     *
     * @return integer
     */
    public function getUserId()
    {
        return $this->get_default_property(self :: PROPERTY_USER_ID);
    }

    /**
     *
     * @param integer $userId
     */
    public function setUserId($userId)
    {
        $this->set_default_property(self :: PROPERTY_USER_ID, $userId);
    }

    /**
     *
     * @return \Chamilo\Core\Repository\Storage\DataClass\ContentObject
     */
    public function getContentObject()
    {
        try
        {
            return \Chamilo\Core\Repository\Storage\DataManager :: retrieve_by_id(
                ContentObject :: class_name(),
                $this->getContentObjectId());
        }
        catch (\Exception $ex)
        {
            return null;
        }
    }
}
