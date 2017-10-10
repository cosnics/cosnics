<?php
namespace Chamilo\Core\Repository\ContentObject\GlossaryItem\Form;

use Chamilo\Core\Repository\ContentObject\GlossaryItem\Storage\DataClass\GlossaryItem;
use Chamilo\Core\Repository\Form\ContentObjectForm;

/**
 *
 * @package repository.lib.content_object.glossary_item
 */
/**
 * This class represents a form to create or update glossary_items
 */
class GlossaryItemForm extends ContentObjectForm
{

    // Inherited
    public function create_content_object()
    {
        $object = new GlossaryItem();
        $this->set_content_object($object);
        return parent::create_content_object();
    }
}
