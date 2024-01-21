<?php

namespace ProcessPilot\Client\Factory;

use ProcessPilot\Client\Service\PilotClientService;

class PilotClientServiceFactory
{
    public function __invoke(): PilotClientService
    {
        $pilotClient = PilotClientService::getInstance();
        $pilotClient::setSettings((new SettingsFactory)());

        return $pilotClient;
    }
}
