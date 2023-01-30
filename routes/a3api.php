<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function (GuzzleHttp\Client $client) {
    return redirect('login');
});




// usando GuzzleHttp\Client
// Route::get ('/', function(){
//     $client =new GuzzleHttp\Client([
//         'base_uri'=> 'https://jsonplaceholder.typicode.com'
//     ]);
//      $response = $client -> request('GET', 'posts');
//     //  dd ($response -> getBody()->getContents());
//     return json_decode($response->getBody() -> getContents());
//     //  return view('welcome');

// });

// //usando HTTP Client
// Route::get ('/', function(){
//     // $client = new Client([
//     //     'base_uri'=> 'https://jsonplaceholder.typicode.com'
//     // ]);
//      $response = Http::get(env('API_ENDPOINT'),[
//         'title'=> 'delectus aut autem'
//      ]);

//      //ejemplo enviando parámetros del Header
//     //  $response = Http:: withHeaders([

//     //     'token'=> 'xxxxxxxxxx'

//     //  ])-> post (env('API_ENDPOINT'),[
//     //     'name' => 'Juan',
//     //  ]);

//     //ejemplo autenticación
//     //basico
//     // $response = Http::withBasicAuth('taylor@laravel.com', 'secret')->post(/* ... */);

//     //token
//     // $response = Http::withToken('token')->post(/* ... */);

//     // dd ($response -> getBody()->getContents());
//     return response()->json(['status'=> true,'data'=> json_decode($response->body()), 'Message'=>"Currency retrieved successfully"], 200);

//      return view('welcome');

// });

// Route::post('/', function () {
//     $client = new GuzzleHttp\Client([
//         'headers' => [
//             'grant_type' => 'authorization_code',
//             'code' => '2132c39002e8cfce22fba9da9c027528b19f5b1002bec071a5af7a54445f0d36',
//             'redirect_uri' => 'http://localhost:53971/Login',
//             'client_id' => 'WK.ES.A3WebApi.00267',
//             // 'response_type' => 'code',
//             // 'response_mode' => 'form_post',
//              'client_secret' => 'cCgVFU4nX9wP',
//             // 'code' => $request->code,
//             // 'scope' =>'WK.ES.A3EquipoContex+IDInfo+openid+offline_access',
//             // 'state' => 'state',
//             // 'nonce' => '',
//             // 'acr_values' => ''
//         ]
//     ]);

 
//     $response = $client->request('GET','https://login.wolterskluwer.eu/auth/core/connect/authorize');
 
//     return $response -> getBody()->getContents();
// });



// Route::get('/', function () {
//     $guid = vsprintf('%s%s-%s-4000-8%.3s-%s%s%s0',str_split(dechex( microtime(true) * 1000 ) . bin2hex( random_bytes(8) ),4));

//     $query = http_build_query([
//         'client_id' => 'WK.ES.A3WebApi.00267',
//         'response_type' => 'code',
//         'redirect_uri' => 'http://localhost:53971/Login',
//         'scope' =>'offline_access+openid+IDInfo+WK.ES.A3EquipoContex',
//         'state' => 'GUID',
//          'nonce' => 'GUID' 
//     ]);

//     $url = env('AUTH_ENDPOINT').'?' . $query;
//     return redirect($url);
// });

// //usando HTTP Client
// Route::get ('/', function(){
//     // $client = new Client([
//     //     'base_uri'=> 'https://jsonplaceholder.typicode.com'
//     // ]);
//      $response = Http::get(env('API_ENDPOINT'),[
//         'title'=> 'delectus aut autem'
//      ]);

//      //ejemplo enviando parámetros del Header
//     //  $response = Http:: withHeaders([

//     //     'token'=> 'xxxxxxxxxx'

//     //  ])-> post (env('API_ENDPOINT'),[
//     //     'name' => 'Juan',
//     //  ]);

//     //ejemplo autenticación
//     //basico
//     // $response = Http::withBasicAuth('taylor@laravel.com', 'secret')->post(/* ... */);

//     //token
//     // $response = Http::withToken('token')->post(/* ... */);

//     // dd ($response -> getBody()->getContents());
//     return response()->json(['status'=> true,'data'=> json_decode($response->body()), 'Message'=>"Currency retrieved successfully"], 200);

//      return view('welcome');

// });

// Route::post('/', function () {
//     $client = new GuzzleHttp\Client([
//         'headers' => [
//             'grant_type' => 'authorization_code',
//             'code' => '2132c39002e8cfce22fba9da9c027528b19f5b1002bec071a5af7a54445f0d36',
//             'redirect_uri' => 'http://localhost:53971/Login',
//             'client_id' => 'WK.ES.A3WebApi.00267',
//             // 'response_type' => 'code',
//             // 'response_mode' => 'form_post',
//              'client_secret' => 'cCgVFU4nX9wP',
//             // 'code' => $request->code,
//             // 'scope' =>'WK.ES.A3EquipoContex+IDInfo+openid+offline_access',
//             // 'state' => 'state',
//             // 'nonce' => '',
//             // 'acr_values' => ''
//         ]
//     ]);

 
//     $response = $client->request('GET','https://login.wolterskluwer.eu/auth/core/connect/authorize');
 
//     return $response -> getBody()->getContents();
// });



// Route::get('/', function () {
//     // $guid = vsprintf('%s%s-%s-4000-8%.3s-%s%s%s0',str_split(dechex( microtime(true) * 1000 ) . bin2hex( random_bytes(8) ),4));

//     $query = http_build_query([
//         'client_id' => 'WK.ES.A3WebApi.00267',
//         'response_type' => 'code',
//         'redirect_uri' => 'http://localhost:53971/Login',
//         'scope' =>'offline_access+openid+IDInfo+WK.ES.A3EquipoContex',
//         'state' => 'GUID',
//          'nonce' => 'GUID' 
//     ]);

//     $url = env('AUTH_ENDPOINT').'?' . $query;
//     return redirect($url);
// });

// Route::get ('/', function(){
//    $response= Http::withToken('eyIkaWQiOiIxIiwidHlwIjoiSldUIiwiYWxnIjoiUlMyNTYiLCJ4NXQiOiIwQ1ZvX1k2YzdfOWxQTE9EeHFxZ3ZwS1hCQUkiLCJraWQiOiIwQ1ZvX1k2YzdfOWxQTE9EeHFxZ3ZwS1hCQUkifQ.eyIkaWQiOiIxIiwiJGlkIjoiMSIsImlzcyI6Imh0dHBzOi8vbG9naW4ud29sdGVyc2tsdXdlcmNsb3VkLmNvbSIsImF1ZCI6Imh0dHBzOi8vbG9naW4ud29sdGVyc2tsdXdlcmNsb3VkLmNvbS9yZXNvdXJjZXMiLCJleHAiOjE2NzE4MDAyNTMsIm5iZiI6MTY3MTc5NjY1MywiY2xpZW50X2lkIjoiV0suRVMuQTNXZWJBcGkuMDAyNjciLCJzY29wZSI6WyJvZmZsaW5lX2FjY2VzcyIsIm9wZW5pZCIsIklESW5mbyIsIldLLkVTLkEzRXF1aXBvQ29udGV4Il0sInN1YiI6IjQyM2JiMDVhLWZhZWMtNDdmMy1hNDlmLWFjNzAwMGQ4MWUxZCIsImF1dGhfdGltZSI6MTY3MTc4OTEyOSwiaWRwIjoiaWRzcnYiLCJ3ay5lcy5jbGllbnRpZCI6IjI2NyIsIndrLmVzLmlkY2RhIjoiMk5ZNzIiLCJ3ay5lcy5hM2VxdWlwb3VzZXJpZCI6InN1cGVydmlzb3IiLCJ3ay5lcy5zZWNvbmRjbGllbnRpZCI6IjI2NyIsInVzciI6IjIyNzZhN2ZjLTljM2EtNDZhNy1iZmMyLWFjNzAwMGQ4MWVmMCIsIm93YXBwIjoiMjA3MjVhZTItNGFkZS00NDA5LWJkNmQtYWU2ODAwZTM4NjE1IiwibWVtIjoiMjI3NmE3ZmMtOWMzYS00NmE3LWJmYzItYWM3MDAwZDgxZWYwIiwib3VuIjoiMGFlMzM2MGMtYzk2NC00ZDlkLWExZWQtYWM3MDAwZDgwNzg5IiwiYW1yIjpbInBhc3N3b3JkIl19.U9IzRdaqYnp7aQv7xP02zshERvauJTJk_lXjpVjm3OamGWnJLnwOac2yq9FN9wpc_O-Z9og_ok5P9-JaL4RPFvQpCQosBIzi9aZxmx9ayQ6aaxKg5k0gPmmAjop-bsaS_n-iqkmE3uXvjBY4MnsISPe7_wZ-AYEYx09N8Pf9ZkQ3rOc1kR9zl_dnfqfz8yk-fLXpFmuKcprkvf2-kQmFbAzUFOhNgHTfQPmNEXC19ysEgDNzdEByK9L7b5FYcnMjyorpjXqX_LQco-Yv5NQLmXnfzz_tOzEUt-mnHj2e-8jO2R3JkauVGf6VrLxZR1mu9Sr4ED4Axqk-bSNcLMyJQg')->get("https://a3api.wolterskluwer.es/Laboral/api/companies/16/workplaces?pageNumber=1&pageSize=25");
//     return json_decode( $response->getBody() -> getContents() );
// });

// Route::get ('/', function(){
//     $client =new GuzzleHttp\Client([
//         'base_uri'=> env('API_ENDPOINT')
//     ]);
//     //  $response = $client -> request('GET', 'posts');
//     $response = $client->request('GET', 'api/companies/7/employees/000009/identification', [
//         'headers' => [
//             'authorization' => 'Bearer eyIkaWQiOiIxIiwidHlwIjoiSldUIiwiYWxnIjoiUlMyNTYiLCJ4NXQiOiIwQ1ZvX1k2YzdfOWxQTE9EeHFxZ3ZwS1hCQUkiLCJraWQiOiIwQ1ZvX1k2YzdfOWxQTE9EeHFxZ3ZwS1hCQUkifQ.eyIkaWQiOiIxIiwiJGlkIjoiMSIsImlzcyI6Imh0dHBzOi8vbG9naW4ud29sdGVyc2tsdXdlcmNsb3VkLmNvbSIsImF1ZCI6Imh0dHBzOi8vbG9naW4ud29sdGVyc2tsdXdlcmNsb3VkLmNvbS9yZXNvdXJjZXMiLCJleHAiOjE2NzMzNDMxMzEsIm5iZiI6MTY3MzMzOTUzMSwiY2xpZW50X2lkIjoiV0suRVMuQTNXZWJBcGkuMDAyNjciLCJzY29wZSI6WyJvZmZsaW5lX2FjY2VzcyIsIm9wZW5pZCIsIklESW5mbyIsIldLLkVTLkEzRXF1aXBvQ29udGV4Il0sInN1YiI6IjQyM2JiMDVhLWZhZWMtNDdmMy1hNDlmLWFjNzAwMGQ4MWUxZCIsImF1dGhfdGltZSI6MTY3MzMzOTQ1MywiaWRwIjoiaWRzcnYiLCJ3ay5lcy5jbGllbnRpZCI6IjI2NyIsIndrLmVzLmlkY2RhIjoiMk5ZNzIiLCJ3ay5lcy5hM2VxdWlwb3VzZXJpZCI6InN1cGVydmlzb3IiLCJ3ay5lcy5zZWNvbmRjbGllbnRpZCI6IjI2NyIsInVzciI6IjIyNzZhN2ZjLTljM2EtNDZhNy1iZmMyLWFjNzAwMGQ4MWVmMCIsIm93YXBwIjoiMjA3MjVhZTItNGFkZS00NDA5LWJkNmQtYWU2ODAwZTM4NjE1IiwibWVtIjoiMjI3NmE3ZmMtOWMzYS00NmE3LWJmYzItYWM3MDAwZDgxZWYwIiwib3VuIjoiMGFlMzM2MGMtYzk2NC00ZDlkLWExZWQtYWM3MDAwZDgwNzg5IiwiYW1yIjpbInBhc3N3b3JkIl19.I3tz_NiuLttmdhPVErL-yPfeogserO7EL3GAk0AHDjTn8W_msuDUauYhU_RGykc7pRwGQGj-k_WteGcMdZ_cdIDvGYuFfdAUPVQKxXFLTiXW9WQAqrP0nZBb-zbh9rwV1hc5ibrVnYUq3S2yRp8qjd-YHOx_VxqIbXvQy1NH7mqOk9hLeFxOupTYrCiuSo3HrfTT8llGNPfPpXommKVvFRf9HdBVyKeJFnEjg0iCUnch01UOpSEFVm7j9ZLRaLoNvIRlZ6rznnXLe_ms2u5isRWZjc0z07wXlxqpam-Vndk6uFIiUjnTz5LLYvMqrUy4pUOUkIPGwsj7iLQupMN0Eg',

//             'Ocp-Apim-Subscription-Key'=> env('SUBSCRIPTION_KEY1')
//                 ],
//     ]);
//        dd ($response ->getBody()->getContents());
//     // return json_decode( $response->getBody() -> getContents() );
//     //   return view('welcome');

// });

//renew Token with refresh_token
// Route::get('/token', function () {
//     $response = (new GuzzleHttp\Client)->post(env('TOKEN_ENDPOINT'), [
//         'form_params' => [
//             'grant_type' => 'refresh_token',
//             'client_id' => env('OAUTH_CLIENTNAME'),
//             'client_secret' =>env('OAUTH_CLIENTSECRET'),
//             'refresh_token' => '75507e2b314a275abea339e60b20995e7280d0c090cc09173812f48af1f386e7'
//         ]
//     ]);

//     // dd($response->getBody()->getContents());
//     $result= json_decode($response->getBody() ->getContents(),true);

//     return $result['access_token'];

// });