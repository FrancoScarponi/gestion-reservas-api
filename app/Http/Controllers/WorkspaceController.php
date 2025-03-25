<?php

namespace App\Http\Controllers;

use App\Http\Resources\PaginationResource;
use App\Http\Resources\WorkspaceResource;
use App\Models\Workspace;
use Illuminate\Http\Request;

class WorkspaceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $workspaces = Workspace::paginate(10);
        return response()->json([
            'message' => 'Workspaces listados.',
            'data' => WorkspaceResource::collection($workspaces),
            'pagination' => new PaginationResource($workspaces)
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:60',
            'description' => 'required|string|max:500',
            'location' => 'required|string'
        ]);

        $workspace = Workspace::create($validated);

        return response()->json([
            'message' => 'Workspace creado exitosamente.',
            'data' => new WorkspaceResource($workspace),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $workspace = Workspace::findOrFail($id);
        return response()->json([
            'message' => 'Datos del workspace.',
            'data' => new WorkspaceResource($workspace),
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:60',
            'description' => 'required|string|max:500',
            'location' => 'required|string'
        ]);

        $workspace = Workspace::findOrFail($id);

        $workspace->update($validated);

        return response()->json([
            'message'=> 'Workspace actualizado.',
            'data'=> new WorkspaceResource($workspace),
        ],200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $workspace = Workspace::findOrFail($id);
        $workspace->delete();
        return response()->json([
            'message' => 'Workspace eliminado correctamente.'
        ], 200);
    }
}
