<?php
namespace Chamilo\Core\Repository\Workspace\Rights\Component;

use Chamilo\Core\Repository\Workspace\Rights\Form\RightsForm;
use Chamilo\Core\Repository\Workspace\Rights\Manager;
use Exception;

/**
 * @package Chamilo\Core\Repository\Workspace\Rights\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class CreatorComponent extends Manager
{
    /**
     * @throws \QuickformException
     */
    public function run()
    {
        $translator = $this->getTranslator();
        $workspace = $this->getCurrentWorkspace();

        $form = new RightsForm($this->get_url());

        if ($form->validate())
        {
            $values = $form->exportValues();

            try
            {
                $right = $this->getRightsService()->getAggregatedRight(
                    (int) $values[RightsForm::PROPERTY_VIEW], (int) $values[RightsForm::PROPERTY_USE],
                    (int) $values[RightsForm::PROPERTY_COPY], (int) $values[RightsForm::PROPERTY_MANAGE]
                );

                $success = $this->getEntityRelationService()->setEntityRelations(
                    $workspace, $values[RightsForm::PROPERTY_ACCESS], $right
                );

                $translation = $success ? 'RightsSet' : 'RightsNotSet';
                $message = $translator->trans($translation, [], Manager::CONTEXT);
            }
            catch (Exception $ex)
            {
                $success = false;
                $message = $ex->getMessage();
            }

            $parameters = $filters = [];

            if ($values['submit'] == $translator->trans('SaveAndAddNew', [], Manager::CONTEXT))
            {
                $parameters[self::PARAM_ACTION] = self::ACTION_CREATE;
            }
            else
            {
                $filters[] = self::PARAM_ACTION;
                $parameters[\Chamilo\Core\Repository\Workspace\Manager::PARAM_ACTION] = $this->getRequest()->get(
                    \Chamilo\Core\Repository\Workspace\Manager::PARAM_BROWSER_SOURCE
                );
            }

            $this->redirectWithMessage($message, !$success, $parameters, $filters);
        }
        else
        {
            $html = [];

            $html[] = $this->renderHeader();
            $html[] = $form->render();
            $html[] = $this->renderFooter();

            return implode(PHP_EOL, $html);
        }
    }
}