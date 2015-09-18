<?php
namespace Chamilo\Core\Repository\ContentObject\Portfolio\Form;

use Chamilo\Core\Repository\ContentObject\Portfolio\Storage\DataClass\Portfolio;
use Chamilo\Core\Repository\Form\ContentObjectForm;

/**
 * Portfolio form
 * 
 * @package repository\content_object\portfolio
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class PortfolioForm extends ContentObjectForm
{

    /**
     *
     * @see \core\repository\ContentObjectForm::create_content_object()
     */
    public function create_content_object()
    {
        $object = new Portfolio();
        $this->set_content_object($object);
        return parent :: create_content_object();
    }
}
