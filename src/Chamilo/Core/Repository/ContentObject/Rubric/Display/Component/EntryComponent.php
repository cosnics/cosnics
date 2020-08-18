<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Display\Component;

use Chamilo\Core\Repository\ContentObject\Rubric\Display\Form\EntryFormType;
use Chamilo\Core\Repository\ContentObject\Rubric\Display\Form\Handler\EntryFormHandler;
use Chamilo\Core\Repository\ContentObject\Rubric\Display\Form\Handler\EntryFormHandlerParameters;
use Chamilo\Core\Repository\ContentObject\Rubric\Display\Manager;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;

/**
 * @package Chamilo\Core\Repository\ContentObject\Rubric\Display\Component
 *
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
class EntryComponent extends Manager implements DelegateComponent
{

    /**
     * @return string
     * @throws \Doctrine\ORM\ORMException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\InvalidChildTypeException
     * @throws \Exception
     */
    function run()
    {
        $rubric = $this->getRubric();
        $rubricData = $this->getRubricService()->getRubric($rubric->getActiveRubricDataId());

        $form = $this->getForm()->create(EntryFormType::class);

        $formHandler = $this->getFormHandler();

        $formHandler->setParameters(
            new EntryFormHandlerParameters(
                $this->getUser(), $rubricData, $this->getRubricBridge()->getContextIdentifier(),
                $this->getRubricBridge()->getTargetUsers()
            )
        );
        //var_dump($this->getRubricBridge()->getContextIdentifier());
        //var_dump($this->getRubricBridge()->getEntityName());
        //var_dump($this->getRubricBridge()->getTargetUsers()[0]);

        $formHandled = $formHandler->handle($form, $this->getRequest());
        //var_dump($form->createView()->children['rubric_results']);
        if ($formHandled)
        {
            return '<div class="alert alert-success">' .
                $this->getTranslator()->trans(
                    'RubricEntryComplete', [], 'Chamilo\Core\Repository\ContentObject\Rubric'
                ) .
                '</div>';
        }
        else
        {
            return $this->getTwig()->render(
                'Chamilo\Core\Repository\ContentObject\Rubric:RubricEntry.html.twig',
                [
                    'RUBRIC_DATA_JSON' => $this->getSerializer()->serialize($rubricData, 'json'),
                    'FORM' => $form->createView()
                ]
            );
        }
    }

    /**
     * @return EntryFormHandler
     */
    protected function getFormHandler()
    {
        return $this->getService(EntryFormHandler::class);
    }
}
