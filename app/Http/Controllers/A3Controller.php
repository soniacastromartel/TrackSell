<?php

namespace App\Http\Controllers;

use App\Services\A3Service;
use App\A3Centre;

class A3Controller extends Controller
{

    private $a3service;

    public function __construct(A3Service $a3svc)
    {
        $this->a3service = $a3svc;
    }

    public function index()
    {
        return A3Centre::getA3Companies();;
    }
    public function getCentres($companyCode)
    {
        return A3Centre::getA3Centres($companyCode);;
    }

    public function refreshToken()
    {
        return $this->a3service->refreshToken();
    }

    public function getEmployees($companyCode, $workplaceCode)
    {
        return $this->a3service->getEmployees($companyCode,$workplaceCode);
    }

    public function getAllEmployees()
    {
        // $centres = $this->index();
        // foreach ($centres as $companyCode) {
        //     $employees[] = $this->getEmployees($companyCode);
        // }
        // return $employees;
    }

    public function getJobTitle($companyCode = null, $employeeCode)
    {
        return $this->a3service->getJobTitle($companyCode, $employeeCode);
    }

    public function getContactData($companyCode = null, $employeeCode)
    {
        return $this->a3service->getContactData($companyCode, $employeeCode);
    }

    public function getHiringData($companyCode = null, $employeeCode)
    {
        return $this->a3service->getHiringData($companyCode, $employeeCode);
    }

    public function getCentreName($companyCode = null, $workplaceCode = null)
    {
        return $this->a3service->getCentreName($companyCode, $workplaceCode);
    }
}
