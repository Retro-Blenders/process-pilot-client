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
        dump('send to pp');

        if (!self::$settings->isEnabled()) {
            return false;
        }

        $ch = curl_init();

        curl_setopt(
            $ch,
            CURLOPT_URL,
            self::$settings->getHost() . '/api/import/' . self::$settings->getProjectHash()
        );
        curl_setopt($ch, CURLOPT_POST, 1);

        $payload = [
            'exception' => [
                'name' => $e::class,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'code' => $e->getCode(),
                'trace' => $e->getTrace()
            ]
        ];

        if (self::$settings->isSessionPayload() && session_status() === PHP_SESSION_ACTIVE) {
            $payload['session'] = $_SESSION;
        }

        dump($payload);

        // In real life you should use something like:
        curl_setopt(
            $ch,
            CURLOPT_POSTFIELDS,
            http_build_query($payload)
        );

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec($ch);

        curl_close($ch);

        return true;
    }
}
