<?php
namespace Chamilo\Core\Repository\Share\Table;

use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;

/**
 * a column where the cells render a right
 * 
 * @author Pieterjan Broekaert
 */
class ShareRightColumn extends StaticTableColumn
{

    private $right_id;

    public function __construct($title, $right_id)
    {
        $this->right_id = $right_id;
        parent :: __construct($title, $title);
    }

    public function get_right_id()
    {
        return $this->right_id;
    }
}
