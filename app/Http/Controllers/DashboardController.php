<?php

namespace App\Http\Controllers;

use App\Models\MedicalCase;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __invoke()
    {
        // One grouped query instead of five separate counts (no user input involved).
        $counts = MedicalCase::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $stats = [['label' => 'Total cases', 'count' => $counts->sum(), 'status' => null]];
        foreach (['Planning', 'In Review', 'Approved', 'Completed'] as $status) {
            $stats[] = ['label' => $status, 'count' => $counts[$status] ?? 0, 'status' => $status];
        }

        return view('dashboard', compact('stats'));
    }
}
