<?php
namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Feedback extends \Chamilo\Core\Repository\Feedback\Storage\DataClass\Feedback
{
    const PROPERTY_ENTRY_ID = 'entry_id';

    /**
     *
     * @param string[] $extendedPropertyNames
     * @return string[]
     */
    public static function get_default_property_names($extendedPropertyNames = array())
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_ENTRY_ID));
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
}
