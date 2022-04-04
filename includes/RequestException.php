<?php

class RequestException extends Exception 
{
    private int $statusCode;

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    private function __construct($statusCode = 500, $message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($statusCode, $message, $code, $previous);
        $this->statusCode = $statusCode;
    }
}