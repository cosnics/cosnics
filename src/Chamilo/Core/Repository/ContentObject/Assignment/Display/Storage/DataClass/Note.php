<?php
namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass;

use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Note extends DataClass
{
    const PROPERTY_ENTRY_ID = 'entry_id';
    const PROPERTY_CREATED = 'created';
    const PROPERTY_MODIFIED = 'modified';
    const PROPERTY_NOTE = 'note';
    const PROPERTY_USER_ID = 'user_id';

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
                self :: PROPERTY_NOTE,
                self :: PROPERTY_USER_ID));
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
     * @return string
     */
    public function getNote()
    {
        return $this->get_default_property(self :: PROPERTY_NOTE);
    }

    /**
     *
     * @param string $note
     */
    public function setNote($note)
    {
        $this->set_default_property(self :: PROPERTY_NOTE, $note);
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
}
