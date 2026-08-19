<?php

namespace App\Http\Controllers;

use App\Models\Visit;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VisitController extends Controller
{
    /**
     * Record the visitor's network type (Wi-Fi vs cellular), reported
     * by the browser's Network Information API after the page loads.
     */
    public function network(Request $request): Response
    {
        $type = (string) $request->query('type', '');
        if (! in_array($type, ['wifi', 'cellular'], true)) {
            return response()->noContent();
        }

        $query = Visit::whereNull('network_type')
            ->where('visited_at', '>=', now()->subMinutes(Visit::DEDUPE_MINUTES));

        $token = (string) $request->cookie('visit_token', '');
        if ($token !== '') {
            $query->where('visit_token', $token);
        } else {
            $query->where('ip_address', $request->ip());
        }

        $visit = $query->orderByDesc('visited_at')->first();

        if ($visit) {
            $visit->update(['network_type' => $type]);
        }

        return response()->noContent();
    }
}
