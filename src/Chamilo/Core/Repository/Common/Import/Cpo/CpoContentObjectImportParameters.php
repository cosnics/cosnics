<?php
namespace Chamilo\Core\Repository\Common\Import\Cpo;

use Chamilo\Core\Repository\Common\Import\ContentObjectImportParameters;

class CpoContentObjectImportParameters implements ContentObjectImportParameters
{

    private $content_object_node;

    public function __construct($content_object_node)
    {
        $this->content_object_node = $content_object_node;
    }

    public function get_content_object_node()
    {
        return $this->content_object_node;
    }
}
