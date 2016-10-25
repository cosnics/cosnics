<?php
namespace Chamilo\Core\Repository\ContentObject\Page\Form;

use Chamilo\Core\Repository\ContentObject\Page\Storage\DataClass\Page;
use Chamilo\Core\Repository\Form\ContentObjectForm;

class PageForm extends ContentObjectForm
{
    
    // Inherited
    public function create_content_object()
    {
        $object = new Page();
        $this->set_content_object($object);
        return parent :: create_content_object();
    }
}
