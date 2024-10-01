<?php

namespace App\Http\Controllers;

use App\Models\autojurnal;
use App\Http\Requests\StoreautojurnalRequest;
use App\Http\Requests\UpdateautojurnalRequest;
use App\Repository\AutoJurnal\AutojurnalRepository;

class AutojurnalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $autojurnalRepository;
    public function __construct(AutojurnalRepository $autojurnalRepository)
    {
        $this->autojurnalRepository = $autojurnalRepository;
    }
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreautojurnalRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(autojurnal $autojurnal)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(autojurnal $autojurnal)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateautojurnalRequest $request, autojurnal $autojurnal)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(autojurnal $autojurnal)
    {
        //
    }
}
