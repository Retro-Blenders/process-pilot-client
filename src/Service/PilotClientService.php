<?php

namespace ProcessPilot\Client\Service;

use ProcessPilot\Client\Settings;

class PilotClientService
{
    private static ?self $instance = null;

    public static Settings $settings;

    public static function getInstance(): self
    {
        if (null === self::$instance) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    public static function setSettings(Settings $settings)
    {
        self::$settings = $settings;
    }

    public function sendToServer(\Throwable $e): bool
    {
        if (!self::$settings->isEnabled()) {
            return false;
        }

        echo '[send2pilot] ' . $e::class . ' - ' . $e->getMessage() . PHP_EOL;

        return true;
    }
}
