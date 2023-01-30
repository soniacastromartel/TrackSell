<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Psr\Http\Message\RequestInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;

use App\Services\A3Service;



class AppServiceProvider extends ServiceProvider
{

    protected $a3service;
    
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

        //obtenemos el valor de nuentro EndPoint
        // $baseUrl = env('API_ENDPOINT');
        // $someAccessToken= 'token';

        // $client = new Client([
        //             'base_uri'=> $baseUrl
        //         ]);

        // $handler = HandlerStack::create();
        // $handler->push(Middleware::mapRequest(function (RequestInterface $request) {
        //     return $request->withHeader('Authorization', "Bearer {{$someAccessToken}}");
        // }));

        // //usamos un objeto singleton para registrar nuestro servicio
        // $this->app->singleton(Client::class, function ($app) use ($baseUrl) {
        //     return new Client(['base_uri' => $baseUrl,
    //                             'handler' => $handler
    // ]);
        // });


    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $a3service= new A3Service();

        if (env('APP_DEBUG')) {
            DB::listen(function ($query) {
                $bindings = array_map(function ($value) {
                    if (is_a($value, 'DateTime')) {
                        return $value->format('Y-m-d H:i:s');
                    } else {
                        return $value;
                    }
                }, $query->bindings);

                File::append(
                    storage_path('/logs/query.log'),
                    $query->sql . ' [' . implode(', ',  $bindings) . ']' . PHP_EOL
                );
            });
        }


        view()->composer('*', function ($view) {
            $nDays = '';
            $currentDay = date('d');
            $currentMonth = date('m');
            if ($currentDay >= 14) {
                $textMonths = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
                $currentMonth = $textMonths[$currentMonth - 1];

                $nDays = env('END_DAY_PERIOD') - $currentDay;
            }
            $view->with('nDays', $nDays >= 0 ? $nDays : '');
            $view->with('currentMonth', $currentMonth);
        });


        // try {
        //     Http::macro('a3', function ($access_token) {
        //         return Http::withHeaders([
        //             'authorization' => 'Bearer ' . $access_token,
        //             'Ocp-Apim-Subscription-Key' => env('SUBSCRIPTION_KEY1')
        //         ])->baseUrl( env('API_ENDPOINT'));
        //     });
        // } catch (\Exception $e) {
        //     return response()->json(
        //         [
        //             'success' => 'false',
        //             'errors'  => $e->getMessage(),
        //         ],
        //         400
        //     );
        // }
       

    }
}
