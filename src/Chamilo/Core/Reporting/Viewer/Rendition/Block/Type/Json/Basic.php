<?php
namespace Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Json;

use Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Json;

/**
 *
 * @author Hans De Bisschop & Magali Gillard
 * @package reporting.viewer
 */
class JsonDefaultBlockRendition extends Json
{

    public function render()
    {
        return json_encode(array());
    }
}
