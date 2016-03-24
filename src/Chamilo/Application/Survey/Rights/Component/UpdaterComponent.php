.<?php
namespace Chamilo\Application\Survey\Rights\Component;

use Chamilo\Application\Survey\Repository\EntityRelationRepository;
use Chamilo\Application\Survey\Rights\Form\RightsForm;
use Chamilo\Application\Survey\Service\EntityRelationService;
use Chamilo\Application\Survey\Service\RightsService;
use Chamilo\Application\Survey\Storage\DataClass\PublicationEntityRelation;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package Chamilo\Application\Survey\Rights\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class UpdaterComponent extends TabComponent
{

    public function build()
    {
        $entityRelation = $this->getCurrentEntityRelation();

        if (! $entityRelation instanceof PublicationEntityRelation)
        {
            throw new ObjectNotExistException(Translation :: get('PublicationEntityRelation'));
        }

        $form = new RightsForm(
            $this->get_url(array(self :: PARAM_ENTITY_RELATION_ID => $this->getCurrentEntityRelation()->getId())),
            $entityRelation, $this->determineRightType());

        if ($form->validate())
        {
            try
            {
                $values = $form->exportValues();

                $rightsService = RightsService :: getInstance();

                $right = $rightsService->getAggregatedRight(
                    (int) $values[RightsForm :: PROPERTY_TAKE],
                    (int) $values[RightsForm :: PROPERTY_MAIL],
                    (int) $values[RightsForm :: PROPERTY_REPORT],
                    (int) $values[RightsForm :: PROPERTY_MANAGE],
                    (int) $values[RightsForm :: PROPERTY_PUBLISH]);
                
                $success = $this->getEntityRelationService()->updateEntityRelation(
                    $entityRelation,
                    $this->getCurrentPublication()->getId(),
                    $entityRelation->getEntityType(),
                    $entityRelation->getEntityId(),
                    $right);

                $translation = $success ? 'RightsUpdated' : 'RightsNotUpdated';
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

    /**
     *
     * @return \Chamilo\Application\Survey\Service\EntityRelationService
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
     * @return \Chamilo\Application\Survey\Storage\DataClass\PublicationEntityRelation
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