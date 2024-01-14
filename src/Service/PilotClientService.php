<?php

namespace ProcessPilot\Client\Service;

use ProcessPilot\Client\Settings;

class PilotClientService
{
    public function __construct(private readonly Settings $settings)
    {
    }

    public function sendToServer(\Throwable $e): bool
    {
        if (!$this->settings->isEnabled()) {
            return false;
        }

        echo '[send2pilot] ' . $e::class . ' - ' . $e->getMessage() . PHP_EOL;

        return true;
    }
}
