<?php
namespace Chamilo\Application\Survey\Component;

use Chamilo\Application\Survey\Form\PublicationForm;
use Chamilo\Application\Survey\Repository\PublicationRepository;
use Chamilo\Application\Survey\Service\PublicationService;
use Chamilo\Application\Survey\Service\RightsService;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package Chamilo\Application\Survey\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class UpdaterComponent extends TabComponent
{

    /**
     * Executes this controller
     */
    public function build()
    {
        $publicationIdentifier = $this->getRequest()->query->get(self :: PARAM_PUBLICATION_ID);
        
        if (! $publicationIdentifier)
        {
            throw new NoObjectSelectedException(Translation :: get('Publication'));
        }
        
        $publicationService = new PublicationService(new PublicationRepository());
        $publication = $publicationService->getPublicationByIdentifier($publicationIdentifier);
        
        $rightsService = RightsService :: getInstance();
        
        if ($rightsService->hasPublicationCreatorRights($this->get_user(), $publication))
        {
            $form = new PublicationForm(
                PublicationForm :: TYPE_EDIT, 
                $publication, 
                $this->get_user(), 
                $this->get_url(array(self :: PARAM_PUBLICATION_ID => $publication->getId())), 
                $publication);
            
            if ($form->validate())
            {
                $success = $form->update_publication();
                $this->redirect(
                    $success ? Translation :: get('PublicationUpdated') : Translation :: get('PublicationNotUpdated'), 
                    ! $success, 
                    array(self :: PARAM_ACTION => self :: ACTION_BROWSE));
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
        else
        {
            throw new NotAllowedException();
        }
    }

    public function get_additional_parameters()
    {
        return array(self :: PARAM_PUBLICATION_ID);
    }
}