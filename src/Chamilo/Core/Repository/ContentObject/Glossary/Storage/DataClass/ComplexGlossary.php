<?php
namespace Chamilo\Core\Repository\ContentObject\Glossary\Storage\DataClass;

use Chamilo\Core\Repository\ContentObject\GlossaryItem\Storage\DataClass\GlossaryItem;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;

/**
 * $Id: complex_glossary.class.php 200 2009-11-13 12:30:04Z kariboe $
 * 
 * @package repository.lib.content_object.glossary
 */
class ComplexGlossary extends ComplexContentObjectItem
{

    public function get_allowed_types()
    {
        return array(GlossaryItem :: class_name());
    }
}
