<?php

namespace App\Http\Controllers;

use App\Models\ImportBatch;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ImportController extends Controller
{
    /**
     * POST /imports/preview
     *
     * Eventually: parse the uploaded CSV -> validate every row -> store the file
     * -> create an ImportBatch (pending) -> return summary + rows so the user can
     * review before confirming.
     *
     * STUB: returns the SHAPE the real response will have, with fake data.
     * Note it returns BOTH a summary AND a rows array — the user reviews the
     * actual entries, not just counts.
     */
    public function preview(Request $request): JsonResponse
    {
        return response()->json([
            'import_batch_id' => 1,
            'summary' => [
                'total_rows' => 3,
                'valid_rows' => 2,
                'error_rows' => 1,
                'date_range' => ['from' => '2026-06-20', 'to' => '2026-06-22'],
            ],
            'rows' => [
                [
                    'line'       => 2,
                    'date'       => '2026-06-20',
                    'start_time' => '09:00',
                    'end_time'   => '10:00',
                    'label'      => 'Coding',
                    'valid'      => true,
                    'errors'     => [],
                ],
                [
                    'line'       => 3,
                    'date'       => '2026-06-20',
                    'start_time' => '09:30',
                    'end_time'   => '10:30',
                    'label'      => 'Meeting',
                    'valid'      => false,
                    'errors'     => ['Overlaps row 2 in this file'],
                ],
                [
                    'line'       => 4,
                    'date'       => '2026-06-22',
                    'start_time' => '14:00',
                    'end_time'   => '15:00',
                    'label'      => 'Review',
                    'valid'      => true,
                    'errors'     => [],
                ],
            ],
        ]);
    }

    /**
     * POST /imports/confirm
     *
     * Eventually: look up the batch -> re-validate guard -> dispatch the import job
     * -> mark processing -> return immediately.
     *
     * STUB: returns 202 Accepted on purpose. The import is queued, so this endpoint
     * can't return a result — only "accepted, processing in the background". 202 (not
     * 200) is the honest code for that; real clients rely on the distinction.
     */
    public function confirm(Request $request): JsonResponse
    {
        return response()->json([
            'import_batch_id' => 1,
            'status'          => 'processing',
        ], 202);
    }

    /**
     * GET /imports/{importBatch}
     *
     * Route-model-bound: Laravel resolves the ImportBatch by id automatically.
     * Client polls this to learn how the queued import went.
     *
     * STUB: returns the batch's current state straight off the model.
     *
     * TODO (Phase 6): scope to owner — IDOR. Route binding fetches ANY user's
     * batch by id. Must verify $importBatch->user_id === $request->user()->id
     * (or a policy) before returning, or users can read each other's imports.
     */
    public function show(ImportBatch $importBatch): JsonResponse
    {
        return response()->json([
            'import_batch_id' => $importBatch->id,
            'status'          => $importBatch->status,
            'imported_rows'   => $importBatch->imported_rows,
            'skipped_rows'    => $importBatch->skipped_rows,
            'errors'          => $importBatch->errors,
        ]);
    }
}