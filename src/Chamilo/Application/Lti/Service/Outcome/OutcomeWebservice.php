<?php

namespace Chamilo\Application\Lti\Service\Outcome;

use Chamilo\Application\Lti\Manager;
use Chamilo\Application\Lti\Service\LtiProviderService;
use Chamilo\Application\Lti\Domain\Exception\ParseMessageException;
use Chamilo\Application\Lti\Domain\Exception\UnsupportedOperationException;
use Chamilo\Application\Lti\Service\Outcome\MessageHandler\MessageHandlerBuilder;
use Chamilo\Application\Lti\Service\Security\OAuthSecurity;
use IMSGlobal\LTI\OAuth\OAuthException;
use Symfony\Component\HttpFoundation\Request;

/**
 * @package Chamilo\Application\Lti\Service\Outcome
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class OutcomeWebservice
{
    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @var \Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerInterface
     */
    protected $exceptionLogger;

    /**
     * @var \Chamilo\Application\Lti\Service\LtiProviderService
     */
    protected $ltiProviderService;

    /**
     * @var OAuthSecurity
     */
    protected $oauthSecurity;

    /**
     * @var MessageHandlerBuilder
     */
    protected $messageHandlerBuilder;

    /**
     * @var \Chamilo\Application\Lti\Service\Outcome\MessageParser
     */
    protected $messageParser;

    /**
     * OutcomeWebservice constructor.
     *
     * @param \Twig_Environment $twig
     * @param \Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerInterface $exceptionLogger
     * @param \Chamilo\Application\Lti\Service\LtiProviderService $ltiProviderService
     * @param \Chamilo\Application\Lti\Service\Security\OAuthSecurity $oauthSecurity
     * @param \Chamilo\Application\Lti\Service\Outcome\MessageHandler\MessageHandlerBuilder $messageHandlerBuilder
     * @param \Chamilo\Application\Lti\Service\Outcome\MessageParser $messageParser
     */
    public function __construct(
        \Twig_Environment $twig,
        \Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerInterface $exceptionLogger,
        LtiProviderService $ltiProviderService, OAuthSecurity $oauthSecurity,
        MessageHandlerBuilder $messageHandlerBuilder, MessageParser $messageParser
    )
    {
        $this->twig = $twig;
        $this->exceptionLogger = $exceptionLogger;
        $this->messageHandlerBuilder = $messageHandlerBuilder;
        $this->messageParser = $messageParser;
        $this->oauthSecurity = $oauthSecurity;
        $this->ltiProviderService = $ltiProviderService;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return string
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function handleRequest(Request $request)
    {
        try
        {
            $ltiProvider = $this->getLtiProviderFromRequest($request);
            // TODO: enable security
//            $this->oauthSecurity->verifyRequest($this->getApplicationFromRequest($request), $request);
            $outcomeMessage = $this->messageParser->parseMessage($request->getContent());
            $messageHandler = $this->messageHandlerBuilder->buildMessageHandler($outcomeMessage);

            return $messageHandler->handleMessage($outcomeMessage);
        }
        catch (UnsupportedOperationException $ex)
        {
            return $this->twig->render(
                'Chamilo\Application\Lti:Message/UnsupportedOperationResponse.xml.twig',
                ['OPERATION' => $ex->getOperation()]
            );
        }
        catch (ParseMessageException $ex)
        {
            $this->exceptionLogger->logException($ex);

            return $this->twig->render(
                'Chamilo\Application\Lti:Message/DefaultResponse.xml.twig', [
                    'RESPONSE_MESSAGE_ID' => uniqid(), 'STATUS' => 'failure',
                    'MESSAGE' => sprintf('The request message could not be parsed. REASON: %s', $ex->getMessage())
                ]
            );
        }
        catch (OAuthException $ex)
        {
            $this->exceptionLogger->logException($ex);

            return $this->twig->render(
                'Chamilo\Application\Lti:Message/DefaultResponse.xml.twig', [
                    'RESPONSE_MESSAGE_ID' => uniqid(), 'STATUS' => 'failure',
                    'MESSAGE' => sprintf('The OAuth verification has failed. REASON: %s', $ex->getMessage())
                ]
            );
        }
        catch (\Exception $ex)
        {
            $this->exceptionLogger->logException($ex);

            return $this->twig->render(
                'Chamilo\Application\Lti:Message/DefaultResponse.xml.twig', [
                    'RESPONSE_MESSAGE_ID' => uniqid(), 'STATUS' => 'failure',
                    'MESSAGE' => sprintf('The request could not be completed. REASON: %s', $ex->getMessage())
                ]
            );
        }
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Chamilo\Application\Lti\Storage\Entity\LtiProvider
     */
    protected function getLtiProviderFromRequest(Request $request)
    {
        return $this->ltiProviderService->getLtiProviderByUUID($request->get(Manager::PARAM_UUID));
    }
}