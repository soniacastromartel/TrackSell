<?php
namespace App\Charts;

use ConsoleTVs\Charts\Classes\Chartjs\Chart;

class CentreServicesGraph extends Chart
{
    public function __construct()
    {
        parent::__construct();
        $this->setup();
    }

    public function setup()
    {
        $this->labels(['Label1', 'Label2'])
             ->dataset('Sample Dataset', 'bar', [5, 10])
             ->options([
                 'backgroundColor' => ['rgba(255, 99, 132, 0.2)', 'rgba(54, 162, 235, 0.2)'],
                 'borderColor' => ['rgba(255,99,132,1)', 'rgba(54, 162, 235, 1)'],
                 'borderWidth' => 1
             ]);
    }
}
