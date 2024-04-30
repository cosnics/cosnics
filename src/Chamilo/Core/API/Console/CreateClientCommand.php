<?php

namespace Chamilo\Core\API\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Translation\Translator;
use Chamilo\Core\API\Storage\Repository\ClientRepository;


/**
 * @package Chamilo\Core\API\Console\Command
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CreateClientCommand extends Command
{
    const ARG_CLIENT = 'client';
    const ARG_SECRET = 'secret';

    protected Translator $translator;
    protected ClientRepository $clientRepository;

    public function __construct(Translator $translator, ClientRepository $clientRepository)
    {
        $this->translator = $translator;
        $this->clientRepository = $clientRepository;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('chamilo:api:client_create')
            ->addArgument(
                self::ARG_CLIENT, InputArgument::REQUIRED,
                $this->translator->trans('ClientCreateCommandArgClientDescription', [], 'Chamilo\Core\API')
            )
            ->addArgument(
                self::ARG_SECRET, InputArgument::REQUIRED,
                $this->translator->trans('ClientCreateCommandArgSecretDescription', [], 'Chamilo\Core\API')
            )
            ->setDescription($this->translator->trans('ClientCreateCommandDescription', [], 'Chamilo\Core\Queue'));
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int|null|void
     * @throws \Throwable
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $clientName = $input->getArgument(self::ARG_CLIENT);
        $clientSecret = $input->getArgument(self::ARG_SECRET);

        $this->clientRepository->createClient($clientName, $clientSecret, $clientName);

        return 0;
    }

}