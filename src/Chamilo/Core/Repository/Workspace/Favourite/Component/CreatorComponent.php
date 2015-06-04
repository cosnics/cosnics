<?php
namespace Chamilo\Core\Repository\Workspace\Favourite\Component;

use Chamilo\Libraries\Platform\Translation;
use Chamilo\Core\Repository\Workspace\Rights\Form\RightsForm;
use Chamilo\Core\Repository\Workspace\Repository\EntityRelationRepository;
use Chamilo\Core\Repository\Workspace\Service\EntityRelationService;
use Chamilo\Core\Repository\Workspace\Repository\ContentObjectRelationRepository;
use Chamilo\Core\Repository\Workspace\Service\ContentObjectRelationService;
use Chamilo\Core\Repository\Workspace\Service\RightsService;
use Chamilo\Core\Repository\Workspace\Favourite\Manager;

/**
 *
 * @package Chamilo\Core\Repository\Workspace\Rights\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class CreatorComponent extends Manager
{

    public function run()
    {
        $workspace = $this->getCurrentWorkspace();

        $form = new RightsForm($this->get_url());

        if ($form->validate())
        {
            try
            {
                $values = $form->exportValues();

                $entityRelationService = new EntityRelationService(new EntityRelationRepository());
                $contentObjectRelationService = new ContentObjectRelationService(new ContentObjectRelationRepository());
                $rightsService = new RightsService($contentObjectRelationService, $entityRelationService);

                $right = $rightsService->getAggregatedRight(
                    (int) $values[RightsForm :: PROPERTY_VIEW],
                    (int) $values[RightsForm :: PROPERTY_USE],
                    (int) $values[RightsForm :: PROPERTY_COPY]);

                $success = $entityRelationService->setEntityRelations(
                    $workspace,
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

            $this->redirect($message, ! $success, array(self :: PARAM_ACTION => self :: ACTION_CREATE));
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