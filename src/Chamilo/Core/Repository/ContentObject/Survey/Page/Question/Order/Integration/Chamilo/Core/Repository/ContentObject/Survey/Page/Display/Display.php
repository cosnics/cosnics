<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Order\Integration\Chamilo\Core\Repository\ContentObject\Survey\Page\Display;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Core\Repository\ContentObject\Survey\Page\ComplexContentObjectPathNode;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Order\Storage\DataClass\Order;

class Display extends \Chamilo\Core\Repository\ContentObject\Survey\Page\Display\QuestionDisplay
{

    function process(ComplexContentObjectPathNode $complexContentObjectPathNode, $answer)
    {
        $formValidator = $this->get_formvalidator();
             
        $formRendition = ContentObjectRenditionImplementation :: factory(
            $complexContentObjectPathNode->get_content_object(),
            ContentObjectRendition :: FORMAT_HTML,
            ContentObjectRendition :: VIEW_FORM,
            Order :: package());
        
        $formRendition->setFormValidator($formValidator);
        $formRendition->setComplexContentObjectPathNode($complexContentObjectPathNode);
        $formValidator = $formRendition->initialize();
        $formValidator->setDefaults($answer);
    }
}
?>