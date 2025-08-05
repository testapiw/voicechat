<?php

namespace App\Service;

use App\Freeswitch\ESLconnection;

class FreeswitchService
{
    private ESLconnection $esl;

    public function __construct(string $host, int $port, string $password)
    {
        $this->esl = new ESLconnection($host, $port, $password);
        if (!$this->esl->connected()) {
            throw new \RuntimeException('Failed to connect to FreeSWITCH');
        }
    }

    // Getting FreeSWITCH status
    public function status(): string
    {
        return $this->esl->sendRecv("api status")->getBody();
    }

    // Outgoing call to destination with parking (waiting)
    public function originateAndPark(string $destination): string
    {
        $cmd = "api originate {$destination} &park()";
        return $this->esl->sendRecv($cmd)->getBody();
    }

    // Outgoing call with file playback (path must be accessible by FS)
    public function originateAndPlayback(string $destination, string $filePath): string
    {
        $cmd = "api originate {ignore_early_media=true} {$destination} &playback({$filePath})";
        return $this->esl->sendRecv($cmd)->getBody();
    }

    // Terminate a call by UUID
    public function hangupCall(string $uuid): string
    {
        $cmd = "api uuid_kill {$uuid}";
        return $this->esl->sendRecv($cmd)->getBody();
    }

    // Getting detailed information about a call by UUID
    public function uuidDump(string $uuid): string
    {
        $cmd = "api uuid_dump {$uuid}";
        return $this->esl->sendRecv($cmd)->getBody();
    }

    // Example test: make a call with a unique UUID and return it for further actions
    public function originateWithUUID(string $destination): string
    {
        $uuid = uniqid('call_');
        $cmd = "api originate {origination_uuid={$uuid},ignore_early_media=true} {$destination} &park()";
        $this->esl->sendRecv($cmd);
        return $uuid;
    }
}
