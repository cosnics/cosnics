<?php
namespace Chamilo\Core\Repository\ContentObject\Note\Form;

use Chamilo\Core\Repository\ContentObject\Note\Storage\DataClass\Note;
use Chamilo\Core\Repository\Form\ContentObjectForm;

/**
 * $Id: note_form.class.php 200 2009-11-13 12:30:04Z kariboe $
 * 
 * @package repository.lib.content_object.note
 */
/**
 * This class represents a form to create or update notes
 */
class NoteForm extends ContentObjectForm
{
    
    // Inherited
    public function create_content_object()
    {
        $object = new Note();
        $this->set_content_object($object);
        return parent :: create_content_object();
    }
}
