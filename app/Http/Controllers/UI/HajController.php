<?php

namespace App\Http\Controllers\UI;

use App\Http\Controllers\Controller;
use App\Models\Haj;

class HajController extends Controller
{
  public function index()
  {
    $haj = Haj::with('days')->orderBy('id', 'desc')->paginate();
    return sendResponse('Haj', 200,  $haj);
  }

  public function show($id)
  {
    $haj = Haj::with('days')->find($id);
    if (!$haj) return sendResponse(404, 'Haj not found');
    return sendResponse('Haj', 200,  $haj);
  }
}
