<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Response;
// use Illuminate\Http\Client\Response;



use App\Services\A3Service;
use Illuminate\Http\Client\PendingRequest;

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


        //MACROS
        
            Http::macro('a3', function ($access_token) {
                return Http::withHeaders([
                    'authorization' => 'Bearer ' . $access_token,
                    'Ocp-Apim-Subscription-Key' => env('SUBSCRIPTION_KEY1')
                ])->baseUrl( env('API_ENDPOINT').'/');
            });

            // PendingRequest::macro('a3', function ($access_token) {
            //     return PendingRequest::withHeaders([
            //         'authorization' => 'Bearer ' . $access_token,
            //         'Ocp-Apim-Subscription-Key' => env('SUBSCRIPTION_KEY1')
            //     ])->baseUrl( env('API_ENDPOINT').'/');
            // });

            Response:: macro('success', function($data){
                return response()-> json($data);
            });
       
            Response:: macro('error', function($error, $status_code){
                return response()-> json([
                    'success'=> false,
                    'data'=> $error
                ], $status_code);
            });
        
    }
}
