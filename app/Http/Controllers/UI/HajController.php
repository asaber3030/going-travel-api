<?php

namespace App\Http\Controllers\UI;

use App\Http\Controllers\Controller;
use App\Models\Haj;

class HajController extends Controller
{
  public function index()
  {
    $type = request()->query('type', '');
    $haj = Haj::query()->orderBy('id', 'desc');
    if ($type) {
      $haj = $haj->where('type', $type);
    }
    $data = $haj->with('days')->paginate(10);
    return sendResponse('Haj', 200, $data);
  }

  public function show($id)
  {
    $haj = Haj::with('days')->find($id);
    if (!$haj) return sendResponse(404, 'Haj not found');
    return sendResponse('Haj', 200,  $haj);
  }
}
