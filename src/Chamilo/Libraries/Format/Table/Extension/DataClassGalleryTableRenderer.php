<?php
namespace Chamilo\Libraries\Format\Table\Extension;

use Chamilo\Libraries\Format\Table\GalleryTableRenderer;

/**
 * @package Chamilo\Libraries\Format\Table\Extension
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 * @author  Hans De Bisschop <hans.de.bisschop>
 */
abstract class DataClassGalleryTableRenderer extends GalleryTableRenderer
{
    /**
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass $dataClass
     */
    public function renderIdentifierCell($dataClass): string
    {
        return $dataClass->get_id();
    }
}
