<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CertificationController extends Controller
{
    public function index(Request $request)
    {
        $query = Certification::query();
        
        // Search
        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('issuer', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by validity
        if ($request->has('validity')) {
            if ($request->validity === 'valid') {
                $query->valid();
            } elseif ($request->validity === 'expired') {
                $query->expired();
            }
        }

        $certifications = $query->sorted()->paginate(20);

        return view('admin.certifications.index', compact('certifications'));
    }

    public function create()
    {
        $certification = new Certification();
        return view('admin.certifications.form', compact('certification'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'issuer' => 'required|string|max:255',
            'issue_date' => 'required|date',
            'expiration_date' => 'nullable|date|after_or_equal:issue_date',
            'credential_id' => 'nullable|string|max:255',
            'credential_url' => 'nullable|url',
            'image' => 'nullable|image|max:5120',
            'sort_order' => 'nullable|integer',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('certifications', 'public');
        }

        Certification::create($validated);

        return redirect()->route('admin.certifications.index')
            ->with('success', 'Certification added successfully.');
    }

    public function edit(Certification $certification)
    {
        return view('admin.certifications.form', compact('certification'));
    }

    public function update(Request $request, Certification $certification)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'issuer' => 'required|string|max:255',
            'issue_date' => 'required|date',
            'expiration_date' => 'nullable|date|after_or_equal:issue_date',
            'credential_id' => 'nullable|string|max:255',
            'credential_url' => 'nullable|url',
            'image' => 'nullable|image|max:5120',
            'sort_order' => 'nullable|integer',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($certification->image) {
                Storage::disk('public')->delete($certification->image);
            }
            $validated['image'] = $request->file('image')->store('certifications', 'public');
        }

        $certification->update($validated);

        return redirect()->route('admin.certifications.index')
            ->with('success', 'Certification updated successfully.');
    }

    public function destroy(Certification $certification)
    {
        // Delete image
        if ($certification->image) {
            Storage::disk('public')->delete($certification->image);
        }

        $certification->delete();

        return redirect()->route('admin.certifications.index')
            ->with('success', 'Certification deleted successfully.');
    }
}