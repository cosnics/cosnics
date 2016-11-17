<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Form;

use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\Form\ContentObjectForm;

/**
 * $Id: learning_path_form.class.php 200 2009-11-13 12:30:04Z kariboe $
 * 
 * @package repository.lib.content_object.learning_path
 */
class LearningPathForm extends ContentObjectForm
{

    public function create_content_object()
    {
        $object = new LearningPath();
        $object->set_version('chamilo');
        $this->set_content_object($object);
        return parent::create_content_object();
    }
}
