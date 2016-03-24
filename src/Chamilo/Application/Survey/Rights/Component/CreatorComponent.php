<?php
namespace Chamilo\Application\Survey\Rights\Component;

use Chamilo\Application\Survey\Repository\EntityRelationRepository;
use Chamilo\Application\Survey\Rights\Form\RightsForm;
use Chamilo\Application\Survey\Service\EntityRelationService;
use Chamilo\Application\Survey\Service\RightsService;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package Chamilo\Application\Survey\Rights\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class CreatorComponent extends TabComponent
{

    public function build()
    {
        $publication = $this->getCurrentPublication();

        $form = new RightsForm($this->get_url(), null, $this->determineRightType());

        if ($form->validate())
        {
            try
            {
                $values = $form->exportValues();

                $entityRelationService = new EntityRelationService(new EntityRelationRepository());
                $rightsService = RightsService :: getInstance();

                $right = $rightsService->getAggregatedRight(
                    (int) $values[RightsForm :: PROPERTY_TAKE],
                    (int) $values[RightsForm :: PROPERTY_MAIL],
                    (int) $values[RightsForm :: PROPERTY_REPORT],
                    (int) $values[RightsForm :: PROPERTY_MANAGE],
                    (int) $values[RightsForm :: PROPERTY_PUBLISH]);

                $success = $entityRelationService->setEntityRelations(
                    $publication,
                    $values[RightsForm :: PROPERTY_ACCESS],
                    $right);

                $translation = $success ? 'RightsSet' : 'RightsNotSet';
                $message = Translation :: get($translation);
            }
            catch (\Exception $ex)
            {
                $success = false;
                $message = $ex->getMessage();
            }

            $this->redirect($message, ! $success, array(self :: PARAM_ACTION => self :: ACTION_BROWSE));
        }
        else
        {
            $html = array();

            $html[] = $this->render_header();
            $html[] = $form->toHtml();
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
    }
}