<?php
namespace Chamilo\Application\Weblcms\Tool\Interfaces;

use Chamilo\Application\Weblcms\Form\ContentObjectPublicationForm;
use Chamilo\Core\Repository\Publication\Publisher\Interfaces\PublicationHandlerInterface;

/**
 * Defines the necessary functionality for a publisher component that has a custom publication handler
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
interface PublisherCustomPublicationFormHandler
{

    /**
     * Constructs the publication form
     *
     * @param ContentObjectPublicationForm $publicationForm
     *
     * @return PublicationHandlerInterface
     */
    public function getPublicationHandler(ContentObjectPublicationForm $publicationForm);
}