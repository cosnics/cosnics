<?php
namespace Chamilo\Core\Repository\Display;

/**
 * A class implements the <code>PreviewResetSupport</code> interface to indicate that the complex content object preview
 * test data can be reset.
 * This should not have an impact on the actual content and structure of the object, but will reset randomized tracking
 * data, comments, rights and other functionality which is generally only available when actually publishing the content
 * object in an application
 * 
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface PreviewResetSupport
{

    /**
     * Resets the previews temporary storage to it's initial state
     */
    public function reset();
}
