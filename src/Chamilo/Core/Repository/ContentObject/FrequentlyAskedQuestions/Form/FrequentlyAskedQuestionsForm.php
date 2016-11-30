<?php
namespace Chamilo\Core\Repository\ContentObject\FrequentlyAskedQuestions\Form;

use Chamilo\Core\Repository\ContentObject\FrequentlyAskedQuestions\Storage\DataClass\FrequentlyAskedQuestions;
use Chamilo\Core\Repository\Form\ContentObjectForm;

/**
 * Portfolio form
 * 
 * @package repository\content_object\portfolio
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class FrequentlyAskedQuestionsForm extends ContentObjectForm
{

    /**
     *
     * @see \core\repository\ContentObjectForm::create_content_object()
     */
    public function create_content_object()
    {
        $object = new FrequentlyAskedQuestions();
        $this->set_content_object($object);
        return parent::create_content_object();
    }
}
