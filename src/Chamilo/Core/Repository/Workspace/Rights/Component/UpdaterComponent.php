<?php
namespace Chamilo\Core\Repository\Workspace\Rights\Component;

use Chamilo\Core\Repository\Workspace\Rights\Manager;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Core\Repository\Workspace\Rights\Form\RightsForm;
use Chamilo\Core\Repository\Workspace\Repository\EntityRelationRepository;
use Chamilo\Core\Repository\Workspace\Service\EntityRelationService;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\WorkspaceEntityRelation;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;

/**
 *
 * @package Chamilo\Core\Repository\Workspace\Rights\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class UpdaterComponent extends Manager
{

    public function run()
    {
        $entityRelation = $this->getCurrentEntityRelation();

        if (! $entityRelation instanceof WorkspaceEntityRelation)
        {
            throw new ObjectNotExistException(Translation :: get('WorkspaceEntityRelation'));
        }

        $form = new RightsForm($this->get_url(), $entityRelation);

        if ($form->validate())
        {
            try
            {
                $values = $form->exportValues();

                $success = $this->getEntityRelationService()->setEntityRelations(
                    $workspace,
                    $values[RightsForm :: PROPERTY_ACCESS],
                    (int) $values[RightsForm :: PROPERTY_VIEW],
                    (int) $values[RightsForm :: PROPERTY_USE],
                    (int) $values[RightsForm :: PROPERTY_COPY]);

                $translation = $success ? 'RightsSet' : 'RightsNotSet';
                $message = Translation :: get($translation);
            }
            catch (\Exception $ex)
            {
                $success = false;
                $message = $ex->getMessage();
            }

            $this->redirect(
                $message,
                ! $success,
                array(
                    self :: PARAM_ACTION => self :: ACTION_BROWSE,
                    self :: PARAM_WORKSPACE_ID => $this->getCurrentWorkspace()->getId()));
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

    /**
     *
     * @return \Chamilo\Core\Repository\Workspace\Service\EntityRelationService
     */
    private function getEntityRelationService()
    {
        if (! isset($this->entityRelationService))
        {
            $this->entityRelationService = new EntityRelationService(new EntityRelationRepository());
        }

        return $this->entityRelationService;
    }

    /**
     *
     * @return \Chamilo\Core\Repository\Workspace\Storage\DataClass\WorkspaceEntityRelation
     */
    public function getCurrentEntityRelation()
    {
        return $this->getEntityRelationService()->getEntityRelationByIdentifier(
            $this->getCurrentEntityRelationIdentifier());
    }

    /**
     *
     * @return \Symfony\Component\HttpFoundation\mixed
     */
    private function getCurrentEntityRelationIdentifier()
    {
        return $this->getRequest()->query->get(self :: PARAM_ENTITY_RELATION_ID);
    }
}