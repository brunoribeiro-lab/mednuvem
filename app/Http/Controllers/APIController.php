<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Address;
use App\Models\City;
use App\Models\State;

class APIController extends Controller {

    /**
     * Display a listing of the resource.
     */
    public function index() {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id) {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id) {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id) {
        //
    }

    /**
     * Retornar endereço do CEP
     * 
     * @access public
     * @param string $cep CEP completo
     * @return json
     */
    public function cep($cep) {
        $zipcode = substr(preg_replace('/\D/', '', $cep), 0, 8);

        $address = Address::select([
                    'address.zip_code',
                    'address.address',
                    'address.neighborhood',
                    'city.city',
                    'city.cod',
                    'state.initials AS state'
                ])
                ->join('city', 'address.city', '=', 'city.id')
                ->join('state', 'city.state', '=', 'state.initials')
                ->where('address.zip_code', $zipcode)
                ->first();

        if (!$address)
            return response()->json(['error' => true, 'message' => 'Cep não encontrado']);  // Func::badRequest equivalente

        return response()->json([
                    'error' => false,
                    'zip_code' => $address->zip_code,
                    'address' => $address->address,
                    'neighborhood' => $address->neighborhood,
                    'city' => $address->city,
                    'cod' => $address->cod,
                    'state' => $address->state
        ]);
    }

}
