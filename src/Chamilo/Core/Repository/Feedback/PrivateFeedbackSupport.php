<?php
namespace Chamilo\Core\Repository\Feedback;

/**
 * Interface which indicates a feedback message can be private or not
 *
 * @author Stefan Gabriëls <stefan.gabriels@hogent.be>
 */
interface PrivateFeedbackSupport
{
    const PROPERTY_PRIVATE = 'isPrivate';
}
