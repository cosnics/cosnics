<?php
namespace Chamilo\Libraries\Storage\DataClass\ForeignObject;

use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * This class represents the parameters to retrieve (a) foreign object(s) in a one to one relation
 *
 * @package Chamilo\Libraries\Storage\DataClass\ForeignObject
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class OneToOneForeignObjectsParameters extends ForeignObjectsParameters
{

    /**
     *
     * @see \Chamilo\Libraries\Storage\DataClass\ForeignObject\ForeignObjectsParameters::set_foreign_key()
     */
    public function set_foreign_key($foreignKey)
    {
        if (is_null($foreignKey))
        {
            $foreignKey = DataClass::PROPERTY_ID;
        }

        parent::set_foreign_key($foreignKey);
    }
}
