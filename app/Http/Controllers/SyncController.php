<?php

namespace App\Http\Controllers;

use App\Services\PegawaiSyncService;
use Illuminate\Http\Request;

class SyncController extends Controller
{
    protected $syncService;

    public function __construct(PegawaiSyncService $syncService)
    {
        $this->syncService = $syncService;
    }

    public function index()
    {
        return view('admin.sync.index');
    }

    public function init()
    {
        try {
            $total = $this->syncService->countSource();
            return response()->json([
                'status' => 'success',
                'total' => $total
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function batch(Request $request)
    {
        $request->validate([
            'limit' => 'required|integer',
            'offset' => 'required|integer',
        ]);

        try {
            $count = $this->syncService->syncBatch($request->limit, $request->offset);
            return response()->json([
                'status' => 'success',
                'processed' => $count
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function cleanup()
    {
        try {
            $deleted = $this->syncService->cleanup();
            return response()->json([
                'status' => 'success',
                'deleted' => $deleted
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
