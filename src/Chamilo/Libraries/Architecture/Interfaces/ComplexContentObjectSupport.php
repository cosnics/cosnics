<?php
namespace Chamilo\Libraries\Architecture\Interfaces;

/**
 * A class implements the <code>ComplexContentObjectSupport</code>
 * interface to indicate that it is a complex content object
 * 
 * @author Hans De Bisschop
 */
interface ComplexContentObjectSupport
{

    public function get_allowed_types();
}
