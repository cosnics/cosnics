<?php
namespace Chamilo\Core\Reporting\Viewer\Rendition\Template\Type\Json;

use Chamilo\Core\Reporting\Viewer\Rendition\Template\Type\Json;

/**
 *
 * @author Hans De Bisschop & Magali Gillard
 * @package reporting.viewer
 */
class Basic extends Json
{

    public function render()
    {
        return json_encode(array());
    }
}
