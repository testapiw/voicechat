<?php

namespace App\Tests\Service;

use PHPUnit\Framework\TestCase;
use App\Service\FreeswitchService;

class FreeswitchServiceTest extends TestCase
{
    private FreeswitchService $service;

    protected function setUp(): void
    {
        $host = 'freeswitch'; // hostname or IP of the FreeSWITCH container
        $port = 8021;
        $password = 'ClueCon';

        $this->service = new FreeswitchService($host, $port, $password);
    }

    public function testStatus()
    {
        $status = $this->service->status();
        $this->assertStringContainsString('FreeSWITCH', $status);
    }

    public function testOriginateAndPark()
    {
        $response = $this->service->originateAndPark('sofia/internal/1001@domain.local');
        $this->assertStringContainsString('+OK', $response);
    }

    public function testOriginateAndPlayback()
    {
        $response = $this->service->originateAndPlayback('sofia/internal/1001@domain.local', '/usr/local/freeswitch/sounds/en/us/callie/ivr/8000/silence_stream.wav');
        $this->assertStringContainsString('+OK', $response);
    }

    public function testOriginateWithUUIDAndUuidDump()
    {
        $uuid = $this->service->originateWithUUID('sofia/internal/1001@domain.local');
        $this->assertNotEmpty($uuid);

        // You can wait a few seconds if the call is active, or make a repeat request later
        sleep(2);

        $dump = $this->service->uuidDump($uuid);
        $this->assertStringContainsString($uuid, $dump);
    }

    public function testHangupCall()
    {
        $uuid = $this->service->originateWithUUID('sofia/internal/1001@domain.local');
        sleep(2);

        $response = $this->service->hangupCall($uuid);
        $this->assertStringContainsString('+OK', $response);
    }
}
