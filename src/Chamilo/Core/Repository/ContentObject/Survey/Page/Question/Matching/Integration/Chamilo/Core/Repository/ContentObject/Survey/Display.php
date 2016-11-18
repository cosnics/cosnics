<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Matching\Integration\Chamilo\Core\Repository\ContentObject\Survey;

use Chamilo\Core\Repository\ContentObject\Survey\Page\ComplexContentObjectPathNode;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Display\QuestionDisplay;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Matching\Storage\DataClass\Matching;
use Chamilo\Core\Repository\ContentObject\Survey\Service\AnswerServiceInterface;

class Display extends QuestionDisplay
{

    function process(ComplexContentObjectPathNode $complexContentObjectPathNode, AnswerServiceInterface $answerService)
    {
        $formValidator = $this->get_formvalidator();
        
        $formRendition = ContentObjectRenditionImplementation::factory(
            $complexContentObjectPathNode->get_content_object(), 
            ContentObjectRendition::FORMAT_HTML, 
            ContentObjectRendition::VIEW_FORM, 
            Matching::package());
        
        $formRendition->setFormValidator($formValidator);
        $formRendition->setComplexContentObjectPathNode($complexContentObjectPathNode);
        $formRendition->setPrefix($answerService->getPrefix());
        $formValidator = $formRendition->initialize();
        $formValidator->setDefaults($answerService->getAnswer($complexContentObjectPathNode->get_id()));
    }
}
?>