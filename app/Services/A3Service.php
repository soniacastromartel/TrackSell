<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Pool;

use Illuminate\Support\Carbon;




class A3Service
{

    private $access_token;
    public $expires_in;
    public $expires_at;
    public $refresh_token;
    public $isExpired;
    //public $totalPages;

    public function __construct()
    {
        $this->refreshToken();
    }


    /**
     * Function to get the access token
     */
    public function getToken($code)
    {
        try {
            $url = env('TOKEN_ENDPOINT');
            $response = Http::asForm()->post($url, [
                'grant_type' => 'authorization_code',
                'code' => $code,
                'client_id' => env('OAUTH_CLIENTNAME'),
                'client_secret' => env('OAUTH_CLIENTSECRET'),
                'redirect_uri' => env('REDIRECT_URI')

            ]);
            $this->access_token = $response['access_token'];
            $this->expires_in = $response['expires_in'];
            $this->refresh_token = $response['refresh_token'];
            $this->expires_at = $this->calculateTokenExpiration($response['expires_in']);
            $this->isExpired = false;

            return $response->json([
                'success' => true, 'url'    => null, 'mensaje' => ''
            ], 200);
        } catch (\Exception $e) {
            \Log::channel('a3')->info("get_token");
            \Log::channel('a3')->info($e->getMessage());
            return response()->json(
                [
                    'success' => 'false',
                    'errors'  => $e->getMessage(),
                ],
                400
            );
        }
    }

    /**
     * Function to refresh the access token
     */
    public function refreshToken()
    {
        try {
            $url = env('TOKEN_ENDPOINT');
            $response = Http::asForm()->post($url, [
                'grant_type' => 'refresh_token',
                'client_id' => env('OAUTH_CLIENTNAME'),
                'client_secret' => env('OAUTH_CLIENTSECRET'),
                'refresh_token' => env('REFRESH_TOKEN')
            ]);
            $this->access_token = $response['access_token'];
            $this->expires_in = $response['expires_in'];
            $this->refresh_token = $response['refresh_token'];
            $this->expires_at = $this->calculateTokenExpiration($response['expires_in']);
            $this->isExpired = false;

            return $response->json([
                'success' => true, 'url'    => null, 'mensaje' => ''
            ], 200);
        } catch (\Exception $e) {
            \Log::channel('a3')->info("refresh_token");
            \Log::channel('a3')->info($e->getMessage());
            return response()->json(
                [
                    'success' => 'false',
                    'errors'  => $e->getMessage(),
                ],
                400
            );
        }
    }

    /**
     * Function to get the number of pages on each request
     * @param $companyCode
     * @param $workplaceCode
     */

    // public function getPages($companyCode, $workplaceCode)
    // {

    //     \Log::channel('a3')->info($this->expires_at);
    //     \Log::channel('a3')->info($this->getTokenExpired($this->expires_at));

    //     if ($this->isExpired) {
    //         $this->refreshToken();
    //         \Log::channel('a3')->info($this->isExpired);
    //         \Log::channel('a3')->info('Token refrescado');
    //     }

    //     $url = $companyCode . '/employees';
    //     $response = Http::a3($this->access_token)->get($url, [
    //         'pageNumber' => 1,
    //         'pageSize' => 600,
    //         'filter' => 'workplaceCode eq ' . $workplaceCode . ' and dropDate eq null or dropDate ge 2019-01-01'
    //     ]);

    //     $pagination = json_decode($response->header('X-pagination'), true);
    //     return $pagination['totalPages'];
    // }


    public function getPages($companyCode, $workplaceCode)
    {

        \Log::channel('a3')->info($this->expires_at);
        \Log::channel('a3')->info($this->getTokenExpired($this->expires_at));

        if ($this->isExpired) {
            $this->refreshToken();
            \Log::channel('a3')->info($this->isExpired);
            \Log::channel('a3')->info('Token refrescado');
        }

        $url = $companyCode . '/employees';
        $response = Http::a3($this->access_token)->get($url, [
            'pageNumber' => 1,
            'pageSize' => 600,
            'filter' => 'workplaceCode eq ' . $workplaceCode . ' and dropDate eq null or dropDate ge 2019-01-01'
        ]);

        $pagination = json_decode($response->header('X-pagination'), true);
        return $pagination['totalPages'];
    }

    /**
     * Function to get Employess for a specific company
     * @param $companyCode 
     */
    public function getEmployees($companyCode, $workplaceCode, $pageNumber)
    {
        try {
            $url = $companyCode . '/employees';

            $response = Http::a3($this->access_token)->get($url, [
                'pageNumber' => $pageNumber,
                'pageSize' => 1000,
                'filter' => 'workplaceCode eq ' . $workplaceCode . ' and dropDate eq null or dropDate ge 2019-01-01',
                'orderBy' => 'employeeCode asc',

            ]);
            return $response->json();
        } catch (\Exception $e) {
            \Log::channel('a3')->info("employees");
            \Log::channel('a3')->info($e->getMessage());
            return response()->json(
                [
                    'success' => 'false',
                    'errors'  => $e->getMessage(),
                ],
                400
            );
        }
    }

    /**
     * Function to get the job category for a specific employee
     * @param $companyCode
     * @param $employeeCode
     */
    public function getJobTitle($companyCode = null, $employeeCode)
    {

        $this->isExpired = $this->getTokenExpired($this->expires_at);
        if ($this->isExpired) {
            $this->refreshToken();
            \Log::channel('a3')->info($this->isExpired);
            \Log::channel('a3')->info('Token refrescado');
        }

        try {
            $url = $companyCode . '/employees/' . $employeeCode . '/jobtitle';
            $response = Http::a3($this->access_token)->retry(3, 500)->get($url);
            return $response->json([
                'success' => true, 'url'    => null, 'mensaje' => ''
            ], 200);
        } catch (\Exception $e) {
            \Log::channel('a3')->info("getJobTitle");
            \Log::channel('a3')->info($e->getMessage());

            return response()->json(
                [
                    'success' => 'false',
                    'errors'  => $e->getMessage(),
                ],
                400
            );
        }
    }

    /**
     * Function to get personal contact data for a specific employee
     * @param $companyCode
     * @param $employeeCode
     */
    public function getContactData($companyCode = null, $employeeCode)
    {

        try {
            $this->isExpired = $this->getTokenExpired($this->expires_at);
            if ($this->isExpired) {
                $this->refreshToken();
                \Log::channel('a3')->info($this->isExpired);
                \Log::channel('a3')->info('Token refrescado');
            }


            $url =  $companyCode . '/employees/' . $employeeCode . '/contactdata/personal';
            $response = Http::a3($this->access_token)->retry(3, 500)->get($url);
            return $response->json([
                'success' => true, 'url'    => null, 'mensaje' => ''
            ], 200);
        } catch (\Exception $e) {
            \Log::channel('a3')->info("contactData");
            \Log::channel('a3')->info($e->getMessage());
            return response()->json(
                [
                    'success' => 'false',
                    'errors'  => $e->getMessage(),
                ],
                400
            );
        }
    }

    /**
     * Function to get the hiring data for a specific employee
     * @param $companyCode
     * @param $employeeCode
     */
    public function getHiringData($companyCode = null, $employeeCode)
    {
        try {

            $this->isExpired = $this->getTokenExpired($this->expires_at);
            if ($this->isExpired) {
                $this->refreshToken();
                \Log::channel('a3')->info($this->isExpired);
                \Log::channel('a3')->info('Token refrescado');
            }

            $url = $companyCode . '/employees/' . $employeeCode . '/hiringdates';
            $response = Http::a3($this->access_token)->retry(3, 500)->get($url);
            return $response->json([
                'success' => true, 'url'    => null, 'mensaje' => ''
            ], 200);
        } catch (\Exception $e) {
            \Log::channel('a3')->info("hiringData");
            \Log::channel('a3')->info($e->getMessage());

            return response()->json(
                [
                    'success' => 'false',
                    'errors'  => $e->getMessage(),
                ],
                400
            );
        }
    }

    /**
     * Function to get the workplaceName for a specific employee
     * @param $companyCode
     * @param $workplaceCode
     */
    public function getCentreName($companyCode = null, $workplaceCode = null)
    {
        try {

            $this->isExpired = $this->getTokenExpired($this->expires_at);
            if ($this->isExpired) {
                $this->refreshToken();
                \Log::channel('a3')->info($this->isExpired);
                \Log::channel('a3')->info('Token refrescado');
            }

            $url =  $companyCode . '/workplaces';
            $response = Http::a3($this->access_token)->retry(3, 500)->get($url, [
                'pageNumber' => 1,
                'pageSize' => 1000,
                'filter' => 'workplaceCode eq ' . $workplaceCode
            ]);
            return $response[0]['workplaceName'];
        } catch (\Exception $e) {
            \Log::channel('a3')->info("centreName");
            \Log::channel('a3')->info($e->getMessage());


            return response()->json(
                [
                    'success' => 'false',
                    'errors'  => $e->getMessage(),
                ],
                400
            );
        }
    }

    /**
     * Function to transform a string into a lowercase string without tildes
     * @param $cadena the string to be converted
     * 
     **/
    public function sanitize($cadena)
    {
        $cadena = str_replace(
            array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
            array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
            $cadena
        );
        $cadena = str_replace(
            array('-'),
            array(' '),
            $cadena
        );

        $cadena = str_replace(
            array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
            array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
            $cadena
        );

        $cadena = str_replace(
            array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
            array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
            $cadena
        );

        $cadena = str_replace(
            array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
            array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
            $cadena
        );

        $cadena = str_replace(
            array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
            array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
            $cadena
        );

        $cadena = str_replace(
            array('ñ', 'Ñ', 'ç', 'Ç'),
            array('n', 'N', 'c', 'C',),
            $cadena
        );


        return strtolower($cadena);
    }

    public function calculateTokenExpiration($expires_in)
    {
        $time = date_create(date('H:i:s'));
        $expires_at = date_add($time, date_interval_create_from_date_string($expires_in . ' seconds'));
        return date_format($expires_at, 'H:i:s');
    }

    public function getTokenExpired($expires_at)
    {
        $time = date('H:i:s');
        if ($time >= $expires_at) {
            $this->isExpired = true;
            // $this->refreshToken();
        } else {
            $this->isExpired = false;
        }
        return $this->isExpired;
    }
}
