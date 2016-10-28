<?php
namespace Chamilo\Application\Weblcms\Tool\Interfaces;

use Chamilo\Application\Weblcms\Form\ContentObjectPublicationForm;

/**
 * Defines the necessary functionality for a publisher component that has a custom publication form
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
interface PublisherCustomPublicationFormInterface
{

    /**
     * Constructs the publication form
     *
     * @param ContentObjectPublication[] $publications
     * @param ContentObject[] $selectedContentObjects
     *
     * @return ContentObjectPublicationForm
     */
    public function constructPublicationForm($publications, $selectedContentObjects);
}