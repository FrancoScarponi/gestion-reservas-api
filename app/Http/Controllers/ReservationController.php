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
    public function index(Request $request)
    {
        $user = $request->user();

        $reservations = $user->reservations()
            ->with(['user', 'workspace'])
            ->orderBy('created_at', 'desc')
            ->paginate(4);
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
        $user = $request->user();
        $validated = $request->validate([
            'workspace_id' => 'required|integer|exists:workspaces,id',
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|integer|min:8|max:19',
            'end_time' => 'required|integer|min:9|max:20|gt:start_time',
        ]);

        //Valido que el horario no este reservado
        //con una query para ver si existe un turno con este horario
        $superimposed = Reservation::where('workspace_id', $validated['workspace_id'])
            ->where('date', $validated['date'])
            ->where('status', '!=', 'rejected')
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

        //Si el horario ya esta en uso retorno, 
        //si no creo la reserva con estado pendiente.               
        if ($superimposed) {
            return response()->json([
                'message' => 'Este horario ya esta reservado.',
            ], 422);
        }

        $validated['user_id'] = $user->id;
        $validated['status'] = 'pending'; //el default
        $reservation = Reservation::create($validated);
        $reservation->load(['user', 'workspace']);

        return response()->json([
            'message' => 'Reserva creada con exito.',
            'reservation' => new ReservationResource($reservation),
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $reservation = Reservation::findOrFail($id);
        $reservation->delete();
        return response()->json([
            'message' => 'Reserva eliminada.'
        ], 200);
    }

    /**
     * Cambia el estado de la reserva.
     */
    public function changeStatus(Request $request, string $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,approved,rejected'
        ]);

        $reservation = Reservation::findOrFail($id);
        $reservation->status = $validated['status'];
        $reservation->save();

        return response()->json([
            'message' => 'Estado actualizado.',
            'data' => new ReservationResource($reservation),
        ], 200);
    }

    /**
     * Obtiene el cronograma de un workspace en un dia determinado con sus horarios y disponibilidad.
     */
    public function getSchedule(Request $request)
    {
        $validated = $request->validate([
            'workspace_id' => 'required|integer|exists:workspaces,id',
            'date' => 'required|date',
        ]);

        $workspaceId = $validated['workspace_id'];
        $date = $validated['date'];

        $reservations = Reservation::where('workspace_id', $workspaceId)
            ->where('date', $date)
            ->where('status', '!=', 'rejected')
            ->get();

        //Genero lista de horarios de 8 a 20 con disponibilidad en true
        $schedule = [];
        for ($hour = 8; $hour <= 19; $hour++) {
            $schedule[$hour] = true; // Inicialmente, todos los horarios estan disponibles
        }

        // Marco los turnos ocupados
        foreach ($reservations as $reservation) {
            for ($hour = $reservation->start_time; $hour < $reservation->end_time; $hour++) {
                $schedule[$hour] = false; // Si hay una reserva en ese horario, se marca como no disponible
            }
        }

        //Lo formateo para la respuesta
        $result = [];
        foreach ($schedule as $hour => $available) {
            $result[] = ['hour' => $hour, 'available' => $available];
        }

        return response()->json([
            'message' => 'Horario obtenido con exito.',
            'schedule' => $result
        ], 200);
    }

    /**
     * Reservas con estado pendiente para mostrar a los admins
     */
    public function indexPending()
    {
        $reservations = Reservation::where('status', 'pending')
            ->with(['user', 'workspace'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        if ($reservations->isEmpty()) {
            return response()->json([
                'message' => 'No hay reservas pendientes.',
            ], 404);
        };

        return response()->json([
            'message' => 'Listado de reservas pendientes.',
            'data' => ReservationResource::collection($reservations),
            'pagination' => new PaginationResource($reservations)
        ], 200);
    }
}
