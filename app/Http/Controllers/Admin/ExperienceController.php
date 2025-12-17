<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Experience;
use Illuminate\Http\Request;

class ExperienceController extends Controller
{
    public function index(Request $request)
    {
        $query = Experience::query();
        
        // Search
        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('company', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by current
        if ($request->has('current')) {
            $query->where('is_current', $request->current === 'true');
        }

        $experiences = $query->sorted()->paginate(20);

        return view('admin.experiences.index', compact('experiences'));
    }

    public function create()
    {
        $experience = new Experience();
        return view('admin.experiences.form', compact('experience'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'company' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_current' => 'boolean',
            'description' => 'required|string',
            'technologies' => 'nullable|array',
            'technologies.*' => 'string|max:50',
            'sort_order' => 'nullable|integer',
        ]);

        $validated['is_current'] = $request->has('is_current');
        $validated['technologies'] = $request->technologies ?? [];

        Experience::create($validated);

        return redirect()->route('admin.experiences.index')
            ->with('success', 'Experience added successfully.');
    }

    public function edit(Experience $experience)
    {
        return view('admin.experiences.form', compact('experience'));
    }

    public function update(Request $request, Experience $experience)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'company' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_current' => 'boolean',
            'description' => 'required|string',
            'technologies' => 'nullable|array',
            'technologies.*' => 'string|max:50',
            'sort_order' => 'nullable|integer',
        ]);

        $validated['is_current'] = $request->has('is_current');
        $validated['technologies'] = $request->technologies ?? [];

        $experience->update($validated);

        return redirect()->route('admin.experiences.index')
            ->with('success', 'Experience updated successfully.');
    }

    public function destroy(Experience $experience)
    {
        $experience->delete();

        return redirect()->route('admin.experiences.index')
            ->with('success', 'Experience deleted successfully.');
    }
}