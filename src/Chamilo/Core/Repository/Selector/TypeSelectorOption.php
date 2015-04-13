<?php
namespace Chamilo\Core\Repository\Selector;

/**
 * An option in a TypeSelector
 * 
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface TypeSelectorOption
{

    /**
     *
     * @return string
     */
    public function get_image_path();

    /**
     *
     * @return string
     */
    public function get_label();
}