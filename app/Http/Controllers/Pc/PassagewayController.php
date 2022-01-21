<?php

namespace App\Http\Controllers\Pc;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pc\PassagewayRequest;
use App\Http\Resources\Pc\PassagewayResource;
use App\Models\Passageway;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class PassagewayController extends Controller
{
    public function index()
    {
        return PassagewayResource::collection(Passageway::whenName(request('name'))
            ->whenGateNumber(request('gate_number'))
            ->with('gates')
            ->latest('id')
            ->paginate(request('pageSize', 10))
        );
    }

    public function store(PassagewayRequest $passagewayRequest)
    {
        $passageway = DB::transaction(function() use ($passagewayRequest){
            $passageway = Passageway::create(Arr::only($passagewayRequest->validated(), ['name', 'note']));
            $passageway->gates()->attach($passagewayRequest->gate_ids);
            return $passageway;
        });
        return send_data(new PassagewayResource($passageway->load('gates')));
    }

    public function show(Passageway $passageway)
    {
        return send_data(new PassagewayResource($passageway->load('gates')));
    }

    public function update(Passageway $passageway, PassagewayRequest $passagewayRequest)
    {
        $passageway = DB::transaction(function() use ($passagewayRequest, $passageway){
            $passageway->fill(Arr::only($passagewayRequest->validated(), ['name', 'note']))->save();
            $passageway->gates()->sync($passagewayRequest->gate_ids);
            return $passageway;
        });
        return send_data(new PassagewayResource($passageway->load('gates')));
    }

    public function destroy(PassagewayRequest $passagewayRequest)
    {
        Passageway::findMany($passagewayRequest->ids)->each->delete();
        return no_content();
    }

    public function select()
    {
        return send_data(PassagewayResource::collection(Passageway::all()));
    }
}
