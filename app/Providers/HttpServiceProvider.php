<?php

namespace App\Providers;

use App\Services\Wamm\Exceptions\WammException;
use App\Services\Wamm\Exceptions\WammFailExecution;
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

        Http::macro('wammPost', function (string $method, ?array $data = null) {
            $response = Http::baseUrl('https://wamm.chat/api2')
                ->asJson()
                ->acceptJson()
                ->post("$method/".config('services.wamm.token'), $data);

            if ($response->json('err') !== 0) {
                throw new WammException($response->json('err'));
            }

            return $response;
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
