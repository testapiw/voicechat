<?php

namespace App\Controller;

use App\Service\FreeswitchService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

// eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE3NTQzODc1MTIsImV4cCI6MTc1NDM5MTExMiwicm9sZXMiOlsiUk9MRV9VU0VSIl0sInVzZXJuYW1lIjoidGVzdHVzZXJAZXhhbXBsZS5jb20ifQ.ZjR08bhLzn1EP__AbVQ9mP_78dFCUmChcCZQgc9vQKlxgHluyX7p7MKAUYJ6aIrWdybkcf-Rq82FtwKTa304-P7UpmrF8NxOKxp7XZbEGNsCSsXN5FNmfC1FBVboTGOA5nCmG6xL26IKa7YibaI8TnIQMcLO4vx3iC7VTmxUIRpUQAFHELsHXhOdUgAZ3AIPHBS15WkPuF7Lh54X2uZncEBoiyfoy51QIaXIcSW3pqmd1uafilKZP5wv9q81EtEvuer_wRRxhFbKNjh0qDPYacF9-fYlLnoqqC4qTQ8VmyGFL5WqmbQq4Xr9RxBW_TK8ns4XLt9WmCRk3QJ5kH7C6h-895aCjsVxWO5dnxTMfi2PYt7y8BsJm4WzeaYhYnf_jhghGhODSTe2_kUL1GhNb8CKZ8t_YqJfU8HYDmbJSMc1IEUQ1f5tL0YMYZGRpLMO6WjmdIqpNtcEr_V7uvfi0VDRaABT2hpW_rd__xJjGcfqrr7nqWa8c--kcrofTGNS67LAWhjoGyniI5UoQaiGRCpKiy38A5t-IcYtcer4qffNAzKZIzdJwMy6bHUXy1VE6CcJbThKl9tC6C0kP64el3MBiDdpUjFpa__fTBGK-qCmD1D8esLl5yZgg7iL9cvGssv41R3ea9PBC1i2EP_QklefP381C6i62BRjs_eKun8
class FreeswitchPingController extends AbstractController
{
    #[Route('/api/ping', name: 'api_freeswitch_ping', methods: ['GET'])]
    public function ping(FreeswitchService $fs): JsonResponse
    {
        try {
            $status = $fs->status();

            return $this->json([
                'status' => 'connected',
                'freeswitch' => $status,
            ]);
        } catch (\Throwable $e) {
            return $this->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
