<?php
namespace Chamilo\Core\Repository\ContentObject\Description\Form;

use Chamilo\Core\Repository\ContentObject\Description\Storage\DataClass\Description;
use Chamilo\Core\Repository\Form\ContentObjectForm;

/**
 * $Id: description_form.class.php 200 2009-11-13 12:30:04Z kariboe $
 * 
 * @package repository.lib.content_object.description
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
/**
 * A form to create/update a description
 */
class DescriptionForm extends ContentObjectForm
{
    
    // Inherited
    public function create_content_object()
    {
        $object = new Description();
        $this->set_content_object($object);
        return parent :: create_content_object();
    }
}
