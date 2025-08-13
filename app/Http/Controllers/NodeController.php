<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\TenancyPeriod;
use App\Models\Tenant;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class NodeController extends Controller
{
    public function createRentalContract(Request $request, Property $property)
    {
        $validated = $request->validate([
            'tenants' => 'required|array|min:1|max:4',
            'tenants.*.name' => 'required|string|max:255',
            'tenants.*.email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('tenants', 'email')
            ],
            'tenants.*.phone' => 'nullable|string|max:255',
            'tenants.*.comment' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'name' => 'nullable|string|max:255',
        ]);

        // Check if there's already an active tenancy period
        $hasActiveTenancy = $property->tenancyPeriods()
            ->where('start_date', '<=', $validated['end_date'])
            ->where('end_date', '>=', $validated['start_date'])
            ->exists();

        if ($hasActiveTenancy) {
            return response()->json([
                'message' => 'Property already has an active tenancy period for the specified dates',
            ], 422);
        }

        // Create tenancy period
        $tenancyPeriod = $property->tenancyPeriods()->create([
            'name' => $validated['name'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
        ]);

        // Create tenants
        foreach ($validated['tenants'] as $tenantData) {
            // this should possibly be changed to create or update
            $tenant = Tenant::create([
                'name' => $tenantData['name'],
                'email' => $tenantData['email'],
                'phone' => $tenantData['phone'] ?? null,
                'comments' => $tenantData['comment'] ?? null,
            ]);

            // Associate tenant with tenancy period
            $tenancyPeriod->tenants()->attach($tenant->id);
        }

        return response()->json([
            'message' => 'Rental contract created successfully',
            'tenancy_period' => $tenancyPeriod->load('tenants'),
        ], 201);
    }

    public function getPropertyTenants(Property $property)
    {
        return $property->tenants();
    }

    public function moveTenant(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'target_tenancy_period_id' => [
                'required',
                'exists:tenancy_periods,id',
                function ($attribute, $value, $fail) {
                    $tenancyPeriod = TenancyPeriod::find($value);
                    if ($tenancyPeriod->tenants()->count() >= 4) {
                        $fail('The target tenancy period already has the maximum number of tenants (4).');
                    }
                },
            ],
            'start_date' => [
                'required',
                'date',
                function ($attribute, $value, $fail) use ($request) {
                    $tenancyPeriod = TenancyPeriod::find($request->target_tenancy_period_id);
                    if ($value < $tenancyPeriod->start_date || $value > $tenancyPeriod->end_date) {
                        $fail('The start date must be within the tenancy period dates.');
                    }
                },
            ],
            'end_date' => [
                'required',
                'date',
                'after:start_date',
                function ($attribute, $value, $fail) use ($request) {
                    $tenancyPeriod = TenancyPeriod::find($request->target_tenancy_period_id);
                    if ($value > $tenancyPeriod->end_date) {
                        $fail('The end date cannot exceed the tenancy period end date.');
                    }
                },
            ],
        ]);

        try {
            DB::transaction(function () use ($tenant, $validated) {
                // Get the target tenancy period
                $targetTenancyPeriod = TenancyPeriod::findOrFail($validated['target_tenancy_period_id']);

                // Remove tenant from current tenancy period(s) that overlap with the new dates
                $tenant->tenancyPeriods()
                    ->where(function ($query) use ($validated) {
                        $query->whereBetween('start_date', [$validated['start_date'], $validated['end_date']])
                            ->orWhereBetween('end_date', [$validated['start_date'], $validated['end_date']]);
                    })
                    ->detach();

                // Attach tenant to new tenancy period
                $targetTenancyPeriod->tenants()->attach($tenant->id, [
                    'start_date' => $validated['start_date'],
                    'end_date' => $validated['end_date']
                ]);
            });

            return response()->json([
                'message' => 'Tenant successfully moved to new tenancy period',
                'tenant' => $tenant->load('tenancyPeriods')
            ]);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to move tenant',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
