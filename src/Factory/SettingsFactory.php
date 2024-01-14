<?php

namespace ProcessPilot\Client\Factory;

use ProcessPilot\Client\Settings;

class SettingsFactory
{
    public function __invoke()
    {
        return new Settings();
    }
}
