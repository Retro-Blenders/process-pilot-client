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

        $ch = curl_init();

        curl_setopt(
            $ch,
            CURLOPT_URL,
            self::$settings->getHost() . '/api/import/' . self::$settings->getProjectHash()
        );
        curl_setopt($ch, CURLOPT_POST, 1);

        // In real life you should use something like:
        curl_setopt(
            $ch,
            CURLOPT_POSTFIELDS,
            http_build_query(['log' => $e])
        );

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec($ch);

        curl_close($ch);

        return true;
    }
}
