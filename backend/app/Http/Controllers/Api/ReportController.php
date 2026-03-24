<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\ReportRequest;
use App\Services\ReportService;
class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
public function index(ReportRequest $request, ReportService $service)
    {
        return $service->generate(auth()->id(),
        $request->input('from'),
        $request->input('to'),
        $request->boolean('refresh'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
