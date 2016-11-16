<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Description\Form;

use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Description\Storage\DataClass\Description;
use Chamilo\Core\Repository\Form\ContentObjectForm;

/**
 *
 * @package repository.content_object.survey_description
 * @author Eduard Vossen
 * @author Magali Gillard
 * @author Hans De Bisschop
 */
class DescriptionForm extends ContentObjectForm
{
    
    // Inherited
    function create_content_object()
    {
        $object = new Description();
        $this->set_content_object($object);
        return parent::create_content_object();
    }
}