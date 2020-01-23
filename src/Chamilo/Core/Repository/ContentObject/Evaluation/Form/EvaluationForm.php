<?php
namespace Chamilo\Core\Repository\ContentObject\Evaluation\Form;

use Chamilo\Core\Repository\ContentObject\Evaluation\Storage\DataClass\Evaluation;
use Chamilo\Core\Repository\Form\ContentObjectForm;

/**
 *
 * @package repository.lib.content_object.evaluation
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
/**
 * A form to create/update a evaluation
 */
class EvaluationForm extends ContentObjectForm
{

    // Inherited
    public function create_content_object()
    {
        $object = new Evaluation();
        $this->set_content_object($object);
        return parent::create_content_object();
    }
}
