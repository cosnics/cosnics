<?php

namespace Chamilo\Core\Repository\ContentObject\Presence\Display\Component;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Core\Repository\ContentObject\Presence\Display\Manager;
use Chamilo\Core\Repository\ContentObject\Presence\Storage\DataClass\Presence;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\DataClass\Rubric;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Core\Repository\ContentObject\Presence\Display\Ajax\Manager as AjaxManager;
use Chamilo\Libraries\File\Redirect;
use Endroid\QrCode\QrCode;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\Presence\Display\Component
 * @author Stefan Gabriëls <stefan.gabriels@hogent.be>
 */
class BrowserComponent extends Manager
{
    /**
     * @return string
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function run()
    {
        $this->checkAccessRights();

        return $this->getTwig()->render(
            Manager::context() . ':Browser.html.twig', $this->getTemplateProperties()
        );
    }

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     */
    protected function checkAccessRights()
    {
        if (!$this->getPresenceServiceBridge()->canEditPresence())
        {
            throw new NotAllowedException();
        }
    }

    /**
     * @return array
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ClassNotExistException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     */
    protected function getTemplateProperties(): array
    {
        $presence = $this->getPresence();

        //$contextIdentifier = $this->getPresenceServiceBridge()->getContextIdentifier();

        $registerPresenceUrl = $this->get_url(
            [
                self::PARAM_CONTEXT => \Chamilo\Application\Presence\Manager::context(),
                self::PARAM_ACTION => \Chamilo\Application\Presence\Manager::ACTION_PRESENCE_REGISTRATION,
                \Chamilo\Application\Presence\Manager::PARAM_PUBLICATION_ID => $this->getRequest()->getFromUrl(
                    \Chamilo\Application\Weblcms\Manager::PARAM_PUBLICATION
                ),
                \Chamilo\Application\Presence\Manager::PARAM_TREE_NODE_ID => $this->getRequest()->getFromUrl(
                    \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::PARAM_CHILD_ID
                )
            ]
        );

        $presenceQRCode = new QrCode($registerPresenceUrl);
        $presenceQRCode->setWriterByName('png');
        $presenceQRCode->setSize('500');

        $qrCodeString =  'data:image/png;base64,' . base64_encode($presenceQRCode->writeString());

        return [
            'HEADER' => $this->render_header(),
            'FOOTER' => $this->render_footer(),
            'LANGUAGE' => $this->getTranslator()->getLocale(),
            //'CONTEXT_CLASS' => $contextIdentifier->getContextClass(),
            //'CONTEXT_ID' => $contextIdentifier->getContextId(),
            'CONTENT_OBJECT_TITLE' => $presence->get_title(),
            'CONTENT_OBJECT_RENDITION' => $this->renderContentObject(),
            'SELF_SERVICE_QR_CODE' => $qrCodeString,
            'LOAD_PRESENCE_URL' => $this->get_url(
                [
                    self::PARAM_ACTION => self::ACTION_AJAX,
                    AjaxManager::PARAM_ACTION => AjaxManager::ACTION_LOAD_PRESENCE
                ]
            ),
            'UPDATE_PRESENCE_URL' => $this->get_url(
                [
                    self::PARAM_ACTION => self::ACTION_AJAX,
                    AjaxManager::PARAM_ACTION => AjaxManager::ACTION_UPDATE_PRESENCE
                ]
            ),
            'LOAD_PRESENCE_ENTRIES_URL' => $this->get_url(
                [
                    self::PARAM_ACTION => self::ACTION_AJAX,
                    AjaxManager::PARAM_ACTION => AjaxManager::ACTION_LOAD_PRESENCE_ENTRIES
                ]
            ),
            'SAVE_PRESENCE_ENTRY_URL' => $this->get_url(
                [
                    self::PARAM_ACTION => self::ACTION_AJAX,
                    AjaxManager::PARAM_ACTION => AjaxManager::ACTION_SAVE_PRESENCE_ENTRY
                ]
            ),
            'CREATE_PRESENCE_PERIOD_URL' => $this->get_url(
                [
                    self::PARAM_ACTION => self::ACTION_AJAX,
                    AjaxManager::PARAM_ACTION => AjaxManager::ACTION_CREATE_PRESENCE_PERIOD
                ]
            ),
            'UPDATE_PRESENCE_PERIOD_URL' => $this->get_url(
                [
                    self::PARAM_ACTION => self::ACTION_AJAX,
                    AjaxManager::PARAM_ACTION => AjaxManager::ACTION_UPDATE_PRESENCE_PERIOD
                ]
            ),
            'DELETE_PRESENCE_PERIOD_URL' => $this->get_url(
                [
                    self::PARAM_ACTION => self::ACTION_AJAX,
                    AjaxManager::PARAM_ACTION => AjaxManager::ACTION_DELETE_PRESENCE_PERIOD
                ]
            ),
            'LOAD_REGISTERED_PRESENCE_ENTRY_STATUSES_URL' => $this->get_url(
                [
                    self::PARAM_ACTION => self::ACTION_AJAX,
                    AjaxManager::PARAM_ACTION => AjaxManager::ACTION_LOAD_REGISTERED_PRESENCE_ENTRY_STATUSES
                ]
            ),
            'TOGGLE_PRESENCE_ENTRY_CHECKOUT_URL' => $this->get_url(
                [
                    self::PARAM_ACTION => self::ACTION_AJAX,
                    AjaxManager::PARAM_ACTION => AjaxManager::ACTION_TOGGLE_PRESENCE_ENTRY_CHECKOUT
                ]
            ),
            'EXPORT_URL' => $this->get_url([self::PARAM_ACTION => self::ACTION_EXPORT])
        ];
    }

    /**
     *
     * @return string
     */
    protected function renderContentObject()
    {
        $display = ContentObjectRenditionImplementation::factory(
            $this->get_root_content_object(),
            ContentObjectRendition::FORMAT_HTML,
            ContentObjectRendition::VIEW_DESCRIPTION,
            $this
        );

        return $display->render();
    }

    public function render_header($pageTitle = '')
    {
        $html = [];
        $html[] = parent::render_header('');

        return implode(PHP_EOL, $html);
    }
}
