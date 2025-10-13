<?php

namespace App\Http\Controllers\ScoringMesin;

use App\Models\ScoringMesin\MachineProcess;
use App\Models\ScoringMesin\Machine;
use App\Models\ScoringMesin\ProcessParameter;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MachineProcessController extends Controller
{
    /**
     * Display a listing of machine processes
     */
    public function index(Request $request)
    {
        if ($request->wantsJson() || $request->ajax()) {
            $machineProcesses = MachineProcess::with([
                'machine',
                'processParameter.sections.parts'
            ])->get();

            return response()->json($machineProcesses);
        }

        return view('scoringmesin.machine-processes.index');
    }

    /**
     * Get all processes for a specific machine
     */
    public function byMachine(Request $request, $machineId)
    {
        $machineProcesses = MachineProcess::with([
            'processParameter.sections.parts'
        ])
            ->where('machine_id', $machineId)
            ->get();

        return response()->json($machineProcesses);
    }

    /**
     * Store a newly created machine process
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'machine_id' => 'required|exists:machines,id',
            'process_parameter_id' => 'required|exists:process_parameters,id',
            'catatan' => 'nullable|string',
        ]);

        // Check if combination already exists
        $exists = MachineProcess::where('machine_id', $validated['machine_id'])
            ->where('process_parameter_id', $validated['process_parameter_id'])
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Kombinasi mesin dan parameter proses sudah ada'
            ], 422);
        }

        $machineProcess = MachineProcess::create($validated);

        return response()->json([
            'message' => 'Machine process berhasil ditambahkan',
            'data' => $machineProcess->load('machine', 'processParameter')
        ], 201);
    }

    /**
     * Display the specified machine process
     */
    public function show(MachineProcess $machineProcess)
    {
        return response()->json(
            $machineProcess->load([
                'machine',
                'processParameter.sections.parts'
            ])
        );
    }

    /**
     * Update the specified machine process
     */
    public function update(Request $request, MachineProcess $machineProcess)
    {
        $validated = $request->validate([
            'machine_id' => 'required|exists:machines,id',
            'process_parameter_id' => 'required|exists:process_parameters,id',
            'catatan' => 'nullable|string',
        ]);

        // Check if combination already exists (excluding current record)
        $exists = MachineProcess::where('machine_id', $validated['machine_id'])
            ->where('process_parameter_id', $validated['process_parameter_id'])
            ->where('id', '!=', $machineProcess->id)
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Kombinasi mesin dan parameter proses sudah ada'
            ], 422);
        }

        $machineProcess->update($validated);

        return response()->json([
            'message' => 'Machine process berhasil diupdate',
            'data' => $machineProcess->load('machine', 'processParameter')
        ]);
    }

    /**
     * Remove the specified machine process
     */
    public function destroy(MachineProcess $machineProcess)
    {
        $machineProcess->delete();

        return response()->json([
            'message' => 'Machine process berhasil dihapus'
        ]);
    }

    /**
     * Get machine process tree structure (Machine -> Process -> Section -> Part)
     */
    public function tree($machineId)
    {
        $machine = Machine::with([
            'machineProcesses.processParameter.sections.parts'
        ])->findOrFail($machineId);

        $tree = [
            'machine' => [
                'id' => $machine->id,
                'name' => $machine->name,
                'code' => $machine->code,
                'status' => $machine->status,
                'status_text' => $machine->status_text,
            ],
            'processes' => []
        ];

        foreach ($machine->machineProcesses as $machineProcess) {
            $processData = [
                'id' => $machineProcess->id,
                'process_parameter_id' => $machineProcess->processParameter->id,
                'process_parameter_name' => $machineProcess->processParameter->name,
                'catatan' => $machineProcess->catatan,
                'sections' => []
            ];

            foreach ($machineProcess->processParameter->sections as $section) {
                $sectionData = [
                    'id' => $section->id,
                    'name' => $section->name,
                    'parts' => []
                ];

                foreach ($section->parts as $part) {
                    $sectionData['parts'][] = [
                        'id' => $part->id,
                        'name' => $part->name,
                        'critical' => $part->critical,
                        'standar' => $part->standar,
                    ];
                }

                $processData['sections'][] = $sectionData;
            }

            $tree['processes'][] = $processData;
        }

        return response()->json($tree);
    }

    /**
     * Bulk assign process parameters to a machine
     */
    public function bulkAssign(Request $request, $machineId)
    {
        $validated = $request->validate([
            'process_parameter_ids' => 'required|array',
            'process_parameter_ids.*' => 'exists:process_parameters,id',
            'catatan' => 'nullable|string',
        ]);

        $machine = Machine::findOrFail($machineId);
        $created = [];
        $skipped = [];

        foreach ($validated['process_parameter_ids'] as $processParameterId) {
            $exists = MachineProcess::where('machine_id', $machineId)
                ->where('process_parameter_id', $processParameterId)
                ->exists();

            if (!$exists) {
                $machineProcess = MachineProcess::create([
                    'machine_id' => $machineId,
                    'process_parameter_id' => $processParameterId,
                    'catatan' => $validated['catatan'] ?? null,
                ]);
                $created[] = $machineProcess->load('processParameter');
            } else {
                $skipped[] = $processParameterId;
            }
        }

        return response()->json([
            'message' => 'Bulk assign selesai',
            'created' => count($created),
            'skipped' => count($skipped),
            'data' => $created
        ], 201);
    }
}
