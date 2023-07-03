<?php
namespace Chamilo\Core\Repository\Quota\Form;

use Chamilo\Core\Repository\Quota\Calculator;
use Chamilo\Core\Repository\Quota\Storage\DataClass\Request;
use Chamilo\Core\User\UserDetails;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

class RequestForm extends FormValidator
{

    private $request;

    public function __construct($request, $action)
    {
        parent::__construct('request', self::FORM_METHOD_POST, $action);

        $this->request = $request;
        $this->calculator = new Calculator($request->get_user());

        $this->build();
        $this->setDefaults();
    }

    public function build()
    {
        if ($this->request->get_id())
        {
            $user_details = new UserDetails($this->request->get_user());
            $this->addElement('static', null, Translation::get('User'), $user_details->toHtml());
        }

        $filesystemTools = $this->getFilesystemTools();

        $quota_bar = Calculator::getBar(
            $this->calculator->getUserDiskQuotaPercentage(),
            $filesystemTools->formatFileSize($this->calculator->getUsedUserDiskQuota()) . ' / ' .
            $filesystemTools->formatFileSize($this->calculator->getMaximumUserDiskQuota())
        );
        $this->addElement('static', null, Translation::get('UsedDiskSpace'), $quota_bar);

        $this->addElement('text', Request::PROPERTY_QUOTA, Translation::get('QuotaStep'), ['size' => '7']);
        $this->addRule(
            Request::PROPERTY_QUOTA, Translation::get('ThisFieldIsRequired', null, StringUtilities::LIBRARIES),
            'required'
        );
        $this->addRule(
            Request::PROPERTY_QUOTA, Translation::get('ThisFieldMustBeNumeric', null, StringUtilities::LIBRARIES),
            'numeric'
        );

        $this->addElement(
            'textarea', Request::PROPERTY_MOTIVATION, Translation::get('Motivation'), ['cols' => 50, 'rows' => 6]
        );
        $this->addRule(
            Request::PROPERTY_MOTIVATION, Translation::get('ThisFieldIsRequired', null, StringUtilities::LIBRARIES),
            'required'
        );

        if ($this->request->get_id())
        {
            $this->addElement(
                'textarea', Request::PROPERTY_DECISION_MOTIVATION, Translation::get('DecisionMotivation'),
                ['cols' => 50, 'rows' => 6]
            );
            $this->addRule(
                Request::PROPERTY_DECISION_MOTIVATION,
                Translation::get('ThisFieldIsRequired', null, StringUtilities::LIBRARIES), 'required'
            );

            $buttons[] = $this->createElement(
                'style_submit_button', 'submit', Translation::get('Update', null, StringUtilities::LIBRARIES), null,
                null, new FontAwesomeGlyph('arrow-right')
            );
        }
        else
        {
            $buttons[] = $this->createElement(
                'style_submit_button', 'submit', Translation::get('Send', null, StringUtilities::LIBRARIES), null, null,
                new FontAwesomeGlyph('envelope')
            );
        }

        $buttons[] = $this->createElement(
            'style_reset_button', 'reset', Translation::get('Reset', null, StringUtilities::LIBRARIES)
        );

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    /**
     * Sets default values.
     *
     * @param $defaults array Default values for this form's parameters.
     */
    public function setDefaults($defaults = [], $filter = null)
    {
        if ($this->request->get_quota())
        {
            $defaults[Request::PROPERTY_QUOTA] =
                $this->getFilesystemTools()->formatFileSize($this->request->get_quota(), false);
        }

        $defaults[Request::PROPERTY_MOTIVATION] = $this->request->get_motivation();

        if ($this->request->get_id())
        {
            $defaults[Request::PROPERTY_DECISION_MOTIVATION] = $this->request->get_decision_motivation();
        }

        parent::setDefaults($defaults);
    }
}
