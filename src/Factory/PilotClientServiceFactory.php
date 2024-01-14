<?php

namespace ProcessPilot\Client\Factory;

use ProcessPilot\Client\Service\PilotClientService;

class PilotClientServiceFactory
{
    public function __invoke()
    {
        return new PilotClientService((new SettingsFactory)());
    }
}
