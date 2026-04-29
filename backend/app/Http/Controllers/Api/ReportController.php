<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\ReportRequest;
use App\Services\ReportService;
class ReportController extends Controller
{
public function index(ReportRequest $request, ReportService $service)
    {
        return $service->generate(auth()->id(),
        $request->validated()['from'],
        $request->validated()['to'],
        $request->boolean('refresh'));
    }


}
