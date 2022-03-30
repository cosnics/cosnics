<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Display\Component;

use Chamilo\Core\Repository\ContentObject\Rubric\Display\Form\EntryFormType;
use Chamilo\Core\Repository\ContentObject\Rubric\Display\Form\Handler\EntryFormHandler;
use Chamilo\Core\Repository\ContentObject\Rubric\Display\Form\Handler\EntryFormHandlerParameters;
use Chamilo\Core\Repository\ContentObject\Rubric\Display\Manager;
use Chamilo\Core\Repository\ContentObject\Rubric\Service\RubricResultJSONGenerator;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;

/**
 * @package Chamilo\Core\Repository\ContentObject\Rubric\Display\Component
 *
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
class EntryComponent extends Manager implements DelegateComponent
{
    const PARAM_COMPLETED = 'completed';

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
        $completedMessage = $this->getTranslator()->trans(
            'RubricEntryComplete', [], 'Chamilo\Core\Repository\ContentObject\Rubric'
        );

        $completed = $this->getRequest()->getFromUrl(self::PARAM_COMPLETED);
        if($completed)
        {
            return '<div class="alert alert-success">' . $completedMessage . '</div>';
        }

        $rubric = $this->getRubric();
        $rubricData = $this->getRubricService()->getRubric($rubric->getActiveRubricDataId());

        $form = $this->getForm()->create(EntryFormType::class);

        $formHandler = $this->getFormHandler();

        $formHandler->setParameters(
            new EntryFormHandlerParameters(
                $this->getUser(), $rubricData, $this->getRubricBridge()->getContextIdentifier(),
                $this->getRubricBridge(),
                $this->getRubricBridge()->getTargetUsers()
            )
        );

        $formHandled = $formHandler->handle($form, $this->getRequest());
        if ($formHandled)
        {
            $parameters = $this->getRubricBridge()->getPostSaveRedirectParameters();
            if (!isset($parameters))
            {
                $parameters = [self::PARAM_COMPLETED => 1];
            }
            $this->redirect($completedMessage, false, $parameters);
            return null;
        }
        else
        {
            $result = null;
            $resultId = $this->getRequest()->getFromUrl('result_id');
            if (!is_null($resultId))
            {
                $results = $this->getRubricResultJSONGenerator()->generateRubricResultsJSON(
                    $rubricData, $this->getRubricBridge()->getContextIdentifier()
                );
                foreach ($results as $res)
                {
                    if ($res->getResultId() === $resultId)
                    {
                        $result = $res;
                        break;
                    }
                }
            }

            return $this->getTwig()->render(
                'Chamilo\Core\Repository\ContentObject\Rubric:RubricEntry.html.twig',
                [
                    'LANGUAGE' => $this->getTranslator()->getLocale(),
                    'RUBRIC_DATA_JSON' => $this->getSerializer()->serialize($rubricData, 'json'),
                    'FORM' => $form->createView(),
                    'RUBRIC_EXISTING_RESULT' => $this->getSerializer()->serialize($result, 'json')
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

    /**
     * @return RubricResultJSONGenerator
     */
    protected function getRubricResultJSONGenerator()
    {
        return $this->getService(RubricResultJSONGenerator::class);
    }
}
