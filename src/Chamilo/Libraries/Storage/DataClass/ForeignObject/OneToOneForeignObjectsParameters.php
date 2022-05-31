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

    public function setForeignKey(?string $foreignKey)
    {
        if (is_null($foreignKey))
        {
            $foreignKey = DataClass::PROPERTY_ID;
        }

        parent::setForeignKey($foreignKey);
    }
}
