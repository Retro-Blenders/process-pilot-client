<?php

namespace ProcessPilot\Client;

final class Settings
{
    private bool $enabled = true;
    private string $host;
    private string $projectId = '';
    private string $projectHash = '';

    public function getHost(): string
    {
        return $this->host;
    }

    public function setHost(string $host): void
    {
        $this->host = $host;
    }

    public function getProjectId(): string
    {
        return $this->projectId;
    }

    public function setProjectId(string $projectId): void
    {
        $this->projectId = $projectId;
    }

    public function getProjectHash(): string
    {
        return $this->projectHash;
    }

    public function setProjectHash(string $projectHash): void
    {
        $this->projectHash = $projectHash;
    }

    public function isEnabled(): bool
    {
        return $this->getProjectHash() !== '' && $this->getProjectId() !== '' && $this->enabled;
    }
}
