<?php

namespace App\Services\OpenAI\Exceptions;

use Exception;
use Illuminate\Http\Client\Response;

class OpenAIException extends Exception
{
    public function __construct(Response $response)
    {
        $this->message = sprintf(
            '[%s] request to [%s] finished with status %s %s: %s',
            $response->transferStats->getRequest()->getMethod(),
            $response->transferStats->getRequest()->getUri(),
            $response->getStatusCode(),
            $response->getReasonPhrase(),
            $response->json('error.message'),
        );
    }
}
