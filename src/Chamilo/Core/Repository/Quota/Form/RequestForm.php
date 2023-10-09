<?php
namespace Chamilo\Core\Repository\Quota\Form;

use Chamilo\Core\Repository\Quota\Calculator;
use Chamilo\Core\Repository\Quota\Storage\DataClass\Request;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Service\UserDetails\UserDetailsRenderer;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Utilities\StringUtilities;

class RequestForm extends FormValidator
{

    protected Calculator $calculator;

    private Request $request;

    /**
     * @throws \QuickformException
     */
    public function __construct($request, $action)
    {
        parent::__construct('request', self::FORM_METHOD_POST, $action);

        $this->request = $request;
        $this->calculator = new Calculator($request->get_user());

        $this->build();
        $this->setDefaults();
    }

    /**
     * @throws \QuickformException
     */
    public function build(): void
    {
        $translator = $this->getTranslator();

        if ($this->request->getId())
        {
            $user_details =
                $this->getUserDetailsRenderer()->renderUserDetails($this->request->get_user(), $this->getCurrentUser());

            $this->addElement(
                'static', null, $translator->trans('User', [], Manager::CONTEXT), $user_details
            );
        }

        $filesystemTools = $this->getFilesystemTools();

        $quota_bar = Calculator::getBar(
            $this->calculator->getUserDiskQuotaPercentage(),
            $filesystemTools->formatFileSize($this->calculator->getUsedUserDiskQuota()) . ' / ' .
            $filesystemTools->formatFileSize($this->calculator->getMaximumUserDiskQuota())
        );
        $this->addElement(
            'static', null, $translator->trans('UsedDiskSpace', [], \Chamilo\Core\Repository\Quota\Manager::CONTEXT),
            $quota_bar
        );

        $this->addElement(
            'text', Request::PROPERTY_QUOTA,
            $translator->trans('QuotaStep', [], \Chamilo\Core\Repository\Quota\Manager::CONTEXT), ['size' => '7']
        );
        $this->addRule(
            Request::PROPERTY_QUOTA, $translator->trans('ThisFieldIsRequired', [], StringUtilities::LIBRARIES),
            'required'
        );
        $this->addRule(
            Request::PROPERTY_QUOTA, $translator->trans('ThisFieldMustBeNumeric', [], StringUtilities::LIBRARIES),
            'numeric'
        );

        $this->addElement(
            'textarea', Request::PROPERTY_MOTIVATION,
            $translator->trans('Motivation', [], \Chamilo\Core\Repository\Quota\Manager::CONTEXT),
            ['cols' => 50, 'rows' => 6]
        );
        $this->addRule(
            Request::PROPERTY_MOTIVATION, $translator->trans('ThisFieldIsRequired', [], StringUtilities::LIBRARIES),
            'required'
        );

        if ($this->request->getId())
        {
            $this->addElement(
                'textarea', Request::PROPERTY_DECISION_MOTIVATION,
                $translator->trans('DecisionMotivation', [], \Chamilo\Core\Repository\Quota\Manager::CONTEXT),
                ['cols' => 50, 'rows' => 6]
            );
            $this->addRule(
                Request::PROPERTY_DECISION_MOTIVATION,
                $translator->trans('ThisFieldIsRequired', [], StringUtilities::LIBRARIES), 'required'
            );

            $buttons[] = $this->createElement(
                'style_submit_button', 'submit', $translator->trans('Update', [], StringUtilities::LIBRARIES), null,
                null, new FontAwesomeGlyph('arrow-right')
            );
        }
        else
        {
            $buttons[] = $this->createElement(
                'style_submit_button', 'submit', $translator->trans('Send', [], StringUtilities::LIBRARIES), null, null,
                new FontAwesomeGlyph('envelope')
            );
        }

        $buttons[] = $this->createElement(
            'style_reset_button', 'reset', $translator->trans('Reset', [], StringUtilities::LIBRARIES)
        );

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    public function getCurrentUser(): User
    {
        return $this->getService('Chamilo\Core\User\CurrentUser');
    }

    public function getUserDetailsRenderer(): UserDetailsRenderer
    {
        return $this->getService(UserDetailsRenderer::class);
    }

    public function setDefaults($defaultValues = [], $filter = null)
    {
        if ($this->request->get_quota())
        {
            $defaultValues[Request::PROPERTY_QUOTA] =
                $this->getFilesystemTools()->formatFileSize($this->request->get_quota(), false);
        }

        $defaultValues[Request::PROPERTY_MOTIVATION] = $this->request->get_motivation();

        if ($this->request->getId())
        {
            $defaultValues[Request::PROPERTY_DECISION_MOTIVATION] = $this->request->get_decision_motivation();
        }

        parent::setDefaults($defaultValues);
    }
}
