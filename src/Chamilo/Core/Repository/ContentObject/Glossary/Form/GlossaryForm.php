<?php
namespace Chamilo\Core\Repository\ContentObject\Glossary\Form;

use Chamilo\Core\Repository\ContentObject\Glossary\Storage\DataClass\Glossary;
use Chamilo\Core\Repository\Form\ContentObjectForm;

/**
 * $Id: glossary_form.class.php 200 2009-11-13 12:30:04Z kariboe $
 * 
 * @package repository.lib.content_object.glossary
 */
/**
 * This class represents a form to create or update glossarys
 */
class GlossaryForm extends ContentObjectForm
{
    
    // Inherited
    public function create_content_object()
    {
        $object = new Glossary();
        $this->set_content_object($object);
        return parent :: create_content_object();
    }
}
