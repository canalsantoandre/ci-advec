<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class Sorteio extends BaseController
{
    public function index()
    {
        return view('sorteio/sorteio');
   
    }
}
