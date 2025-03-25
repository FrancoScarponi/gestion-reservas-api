<?php

namespace App\Http\Controllers;

use App\Http\Resources\PaginationResource;
use App\Http\Resources\ReservationResource;
use App\Models\Reservation;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $reservations = Reservation::with(['user', 'workspace'])->paginate(10);
        return response()->json([
            'message' => "Lista de todas las reservas.",
            'data' => ReservationResource::collection($reservations),
            'pagination' => new PaginationResource($reservations)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'workspace_id' => 'required|integer|exists:workspaces,id',
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|integer|min:8|max:19',
            'end_time' => 'required|integer|min:9|max:20|gt:start_time',
        ]);
        //'status' => 'required|in:pending,approved,rejected',

        
        //Valide que el horario no este reservado
        //con una query para ver si existe un tunro con este horario
        $superimposed = Reservation::where('workspace_id', $validated['workspace_id'])
            ->where('date', $validated['date'])
            ->where(
                fn($q) =>
                $q->whereBetween('start_time', [$validated['start_time'], $validated['end_time'] - 1]) //start_time este en otro turno
                    ->orWhereBetween('end_time', [$validated['start_time'] + 1, $validated['end_time'] - 1]) //end_time este en otro turno
                    ->orWhere(
                        fn($q) =>
                        $q->where('start_time', '<', $validated['start_time']) //por si existe una reserva que superpone por completo el turno
                            ->where('end_time', '>', $validated['end_time'])
                    )
            )->exists();

        //Si existe retorno, sino creo la reserva con estado pendiente.               
        if ($superimposed) {
            return response()->json([
                'message' => 'Este horario ya esta reservado.',
            ], 422);
        }

        $reservation = Reservation::create($validated);
        $reservation->load(['user', 'workspace']);

        return response()->json([
            'message' => 'Reserva creada con exito.',
            'reservation' => new ReservationResource($reservation),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        
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
