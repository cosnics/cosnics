<?php
namespace Chamilo\Core\Repository\ContentObject\Indicator\Form;

use Chamilo\Core\Repository\ContentObject\Indicator\Storage\DataClass\Indicator;
use Chamilo\Core\Repository\Form\ContentObjectForm;

/**
 * $Id: indicator_form.class.php 200 2009-11-13 12:30:04Z kariboe $
 * 
 * @package repository.lib.content_object.indicator
 * @author Sven Vanpoucke
 */
/**
 * This class represents a form to create or update indicators
 */
class IndicatorForm extends ContentObjectForm
{
    
    // Inherited
    public function create_content_object()
    {
        $object = new Indicator();
        $this->set_content_object($object);
        return parent::create_content_object();
    }
}
