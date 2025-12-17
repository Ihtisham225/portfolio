<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SkillController extends Controller
{
    public function index(Request $request)
    {
        $query = Skill::query();
        
        // Search
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter by featured
        if ($request->has('featured')) {
            $query->where('is_featured', $request->featured === 'true');
        }

        $skills = $query->sorted()->paginate(20);

        return view('admin.skills.index', compact('skills'));
    }

    public function create()
    {
        $skill = new Skill();
        return view('admin.skills.form', compact('skill'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:skills,slug',
            'percentage' => 'required|integer|min:0|max:100',
            'color' => 'nullable|string|max:7',
            'icon' => 'nullable|string|max:50',
            'sort_order' => 'nullable|integer',
            'is_featured' => 'boolean',
        ]);

        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $validated['is_featured'] = $request->has('is_featured');

        Skill::create($validated);

        return redirect()->route('admin.skills.index')
            ->with('success', 'Skill created successfully.');
    }

    public function edit(Skill $skill)
    {
        return view('admin.skills.form', compact('skill'));
    }

    public function update(Request $request, Skill $skill)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:skills,slug,' . $skill->id,
            'percentage' => 'required|integer|min:0|max:100',
            'color' => 'nullable|string|max:7',
            'icon' => 'nullable|string|max:50',
            'sort_order' => 'nullable|integer',
            'is_featured' => 'boolean',
        ]);

        $validated['is_featured'] = $request->has('is_featured');

        $skill->update($validated);

        return redirect()->route('admin.skills.index')
            ->with('success', 'Skill updated successfully.');
    }

    public function destroy(Skill $skill)
    {
        $skill->delete();

        return redirect()->route('admin.skills.index')
            ->with('success', 'Skill deleted successfully.');
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:delete,featured,unfeatured',
            'ids' => 'required|array',
            'ids.*' => 'exists:skills,id',
        ]);

        $action = $request->action;
        $ids = $request->ids;

        switch ($action) {
            case 'delete':
                Skill::whereIn('id', $ids)->delete();
                $message = 'Skills deleted successfully.';
                break;
            case 'featured':
                Skill::whereIn('id', $ids)->update(['is_featured' => true]);
                $message = 'Skills marked as featured.';
                break;
            case 'unfeatured':
                Skill::whereIn('id', $ids)->update(['is_featured' => false]);
                $message = 'Skills removed from featured.';
                break;
        }

        return redirect()->back()->with('success', $message);
    }
}