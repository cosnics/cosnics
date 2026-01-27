<?php
namespace Chamilo\Libraries\Service\Utilities;

use Chamilo\Libraries\Architecture\Domain\ActionResult;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Libraries\Architecture
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ActionResultRenderer
{
    protected Translator $translator;

    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    public function getMessage(ActionResult $actionResult): string
    {
        $translator = $this->getTranslator();

        $parameters = [];
        $parameters['ACTION'] =
            $translator->trans('ActionResultAction' . $actionResult->getActionType(), [], $actionResult->getContext());

        if ($actionResult->isSingleAction())
        {
            $parameters['OBJECT'] = $translator->trans(
                'ActionResultSingleEntity' . $actionResult->getEntityType(), [], $actionResult->getContext()
            );

            if ($actionResult->hasFailed())
            {
                return $translator->trans('ActionResultSingleFailureMessage', $parameters);
            }
            else
            {
                return $translator->trans('ActionResultSingleSuccessMessage', $parameters);
            }
        }
        else
        {
            $parameters['OBJECT'] = $translator->trans(
                'ActionResultMultipleEntity' . $actionResult->getEntityType(), [], $actionResult->getContext()
            );

            if ($actionResult->hasSucceeded())
            {
                return $translator->trans('ActionResultMultipleSuccessMessage', $parameters);
            }
            elseif ($actionResult->hasFailedCompletely())
            {
                return $translator->trans('ActionResultMultipleFailureMessage', $parameters);
            }
            else
            {
                return $translator->trans('ActionResultSomeFailureMessage', $parameters);
            }
        }
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }
}