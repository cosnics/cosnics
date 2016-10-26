<?php
namespace Chamilo\Libraries\Storage\DataClass\ForeignObject;

use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * This class represents the parameters to retrieve (a) foreign object(s) in a one to one relation
 *
 * @package common\libraries @authro Sven Vanpoucke - Hogeschool Gent
 */
class OneToOneForeignObjectsParameters extends ForeignObjectsParameters
{

    /**
     * **************************************************************************************************************
     * Inherited Functionality
     * **************************************************************************************************************
     */

    /**
     * Sets the foreign key If no foreign key is given, the id property will be used
     *
     * @param int $foreign_key
     */
    public function set_foreign_key($foreign_key)
    {
        if (is_null($foreign_key))
        {
            $foreign_key = DataClass :: PROPERTY_ID;
        }

        parent :: set_foreign_key($foreign_key);
    }
}
