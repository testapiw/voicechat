<?php

namespace App\Freeswitch;

use App\Freeswitch\ESLResponse;

class ESLconnection
{
    private string $host;
    private int $port;
    private string $password;
    private $fp = null;

    public function __construct(string $host, int $port, string $password)
    {
        $this->host = $host;
        $this->port = $port;
        $this->password = $password;

        $this->connect();
    }

    private function connect(): void
    {
        $this->fp = fsockopen($this->host, $this->port, $errno, $errstr, 10);
        if (!$this->fp) {
            throw new \RuntimeException("Unable to connect to {$this->host}:{$this->port} - $errstr ($errno)");
        }

        // Сделаем неблокирующим сокет (можно убрать, если хочешь блокирующий режим)
        socket_set_blocking($this->fp, false);

        // Читаем приветствие и ждём запроса авторизации
        $authRequested = false;
        $start = microtime(true);
        while (!feof($this->fp) && (microtime(true) - $start) < 5) {
            $line = fgets($this->fp, 1024);
            if ($line === false) {
                usleep(10000);
                continue;
            }

            if (trim($line) === "Content-Type: auth/request") {
                // Отправляем пароль
                fputs($this->fp, "auth {$this->password}\n\n");
                $authRequested = true;
                break;
            }
        }

        if (!$authRequested) {
            fclose($this->fp);
            throw new \RuntimeException("FreeSWITCH did not request authentication.");
        }

        // Ждём ответа от FS
        $authenticated = false;
        $buffer = "";
        $start = microtime(true);
        while (!feof($this->fp) && (microtime(true) - $start) < 5) {
            $buffer .= fgets($this->fp, 1024);
            if (strpos($buffer, "Reply-Text: +OK accepted") !== false) {
                $authenticated = true;
                break;
            }
            usleep(10000);
        }

        if (!$authenticated) {
            fclose($this->fp);
            throw new \RuntimeException("Authentication failed.");
        }
    }

    public function connected(): bool
    {
        return is_resource($this->fp);
    }

    public function sendRecv(string $cmd): ESLResponse
    {
        if (!$this->connected()) {
            throw new \RuntimeException("Not connected to FreeSWITCH");
        }

        // Отправляем команду с двойным переносом строк
        fputs($this->fp, $cmd . "\n\n");

        // Читаем ответ
        $response = "";
        $contentLength = 0;
        $start = microtime(true);
        while (!feof($this->fp) && (microtime(true) - $start) < 5) {
            $line = fgets($this->fp, 4096);
            if ($line === false) {
                usleep(10000);
                continue;
            }

            $trimmed = trim($line);
            if ($contentLength === 0 && strpos($trimmed, 'Content-Length:') === 0) {
                $parts = explode(':', $trimmed, 2);
                $contentLength = (int)trim($parts[1]);
                continue;
            }

            if ($contentLength > 0) {
                $response .= $line;
                if (strlen($response) >= $contentLength) {
                    break;
                }
            }
        }

        return new ESLResponse($response);
    }

    public function __destruct()
    {
        if ($this->fp && is_resource($this->fp)) {
            fclose($this->fp);
        }
    }
}


