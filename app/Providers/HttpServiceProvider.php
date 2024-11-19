<?php

namespace App\Providers;

use App\Services\OpenAI\Exceptions\OpenAIException;
use App\Services\Wamm\Exceptions\WammException;
use App\Services\Wamm\Exceptions\WammFailExecution;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;

class HttpServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        Http::macro('wammGet', function (string $method, ?array $query = null, ?string $token = null) {
            if ($token === null) {
                $token = config('services.wamm.token');
            }

            $response = Http::baseUrl('https://wamm.chat/api2')
                ->acceptJson()
                ->get("$method/$token", $query);

            if ($response->json('err') !== 0) {
                if ($response->json('err') === 'fail execution') {
                    throw new WammFailExecution('Ошибка исполнения. Стоит повторить отправку метода '.$method);
                }

                throw new WammException($response->json('err'));
            }

            return $response;
        });

        Http::macro('openai', function () {
            return Http::withHeaders(['Authorization' => 'Bearer '.config('services.openai.token')])
                ->timeout(60)
                ->asJson()
                ->acceptJson()
                ->baseUrl('https://api.openai.com/v1')
                ->throw(function (Response $response) {
                    throw new OpenAIException($response);
                });
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
