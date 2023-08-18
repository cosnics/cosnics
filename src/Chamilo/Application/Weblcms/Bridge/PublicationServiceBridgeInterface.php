<?php

namespace Chamilo\Application\Weblcms\Bridge;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;

/**
 * @author Stefan Gabriëls - Hogeschool Gent
 */
interface PublicationServiceBridgeInterface
{
    /**
     * @return ContentObjectPublication
     */
    public function getContentObjectPublication(): ContentObjectPublication;

    /**
     * @return Course
     */
    public function getCourse(): Course;
}