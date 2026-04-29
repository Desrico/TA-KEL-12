<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\Challenge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EducationController extends Controller
{
    /**
     * Display the education landing page.
     */
    public function index()
    {
        $moduleCount = Module::count();
        $challengeCount = Challenge::count();

        return view('admin.education.index', compact('moduleCount', 'challengeCount'));
    }

    // --- MODULE CRUD ---

    public function moduleIndex()
    {
        $modules = Module::latest()->get();
        return view('admin.education.modules.index', compact('modules'));
    }

    public function moduleCreate()
    {
        return view('admin.education.modules.form');
    }

    public function moduleStore(Request $request)
    {
        $validated = $request->validate([
            'title'          => 'required|string|max:255',
            'thumbnail_file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'thumbnail_url'  => 'nullable|url',
            'description'    => 'required|string',
            'content_file'   => 'nullable|file|mimes:pdf|max:10240',
            'content_url'    => 'nullable|url',
            'reward_point'   => 'required|integer|min:0',
            'status'         => 'required|boolean',
        ]);

        $data = $validated;

        // Handle Thumbnail
        if ($request->hasFile('thumbnail_file')) {
            $data['thumbnail'] = $request->file('thumbnail_file')->store('modules/thumbnails', 'public');
        } else {
            $data['thumbnail'] = $request->thumbnail_url;
        }

        // Handle Content
        if ($request->hasFile('content_file')) {
            $data['content_url'] = $request->file('content_file')->store('modules/content', 'public');
        } else {
            $data['content_url'] = $request->content_url;
        }

        Module::create($data);

        return redirect()->route('counselor.education.modules.index')
            ->with('success', 'Modul berhasil ditambahkan.');
    }

    public function moduleEdit(Module $module)
    {
        return view('admin.education.modules.form', compact('module'));
    }

    public function moduleUpdate(Request $request, Module $module)
    {
        $validated = $request->validate([
            'title'          => 'required|string|max:255',
            'thumbnail_file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'thumbnail_url'  => 'nullable|url',
            'description'    => 'required|string',
            'content_file'   => 'nullable|file|mimes:pdf|max:10240',
            'content_url'    => 'nullable|url',
            'reward_point'   => 'required|integer|min:0',
            'status'         => 'required|boolean',
        ]);

        $data = $validated;

        // Handle Thumbnail Update
        if ($request->hasFile('thumbnail_file')) {
            if ($module->thumbnail && Storage::disk('public')->exists($module->thumbnail)) {
                Storage::disk('public')->delete($module->thumbnail);
            }
            $data['thumbnail'] = $request->file('thumbnail_file')->store('modules/thumbnails', 'public');
        } elseif ($request->filled('thumbnail_url')) {
            // Jika user memilih memasukkan URL baru, hapus file lama jika ada
            if ($module->thumbnail && Storage::disk('public')->exists($module->thumbnail)) {
                Storage::disk('public')->delete($module->thumbnail);
            }
            $data['thumbnail'] = $request->thumbnail_url;
        }

        // Handle Content Update
        if ($request->hasFile('content_file')) {
            if ($module->content_url && Storage::disk('public')->exists($module->content_url)) {
                Storage::disk('public')->delete($module->content_url);
            }
            $data['content_url'] = $request->file('content_file')->store('modules/content', 'public');
        } elseif ($request->filled('content_url')) {
            if ($module->content_url && Storage::disk('public')->exists($module->content_url)) {
                Storage::disk('public')->delete($module->content_url);
            }
            $data['content_url'] = $request->content_url;
        }

        $module->update($data);

        return redirect()->route('counselor.education.modules.index')
            ->with('success', 'Modul berhasil diperbarui.');
    }

    public function moduleDestroy(Module $module)
    {
        // Delete files if exist
        if ($module->thumbnail && Storage::disk('public')->exists($module->thumbnail)) {
            Storage::disk('public')->delete($module->thumbnail);
        }
        if ($module->content_url && Storage::disk('public')->exists($module->content_url)) {
            Storage::disk('public')->delete($module->content_url);
        }

        $module->delete();
        return redirect()->route('counselor.education.modules.index')
            ->with('success', 'Modul berhasil dihapus.');
    }

    // --- CHALLENGE CRUD ---

    public function challengeIndex()
    {
        $challenges = Challenge::latest()->get();
        return view('admin.education.challenges.index', compact('challenges'));
    }

    public function challengeCreate()
    {
        return view('admin.education.challenges.form');
    }

    public function challengeStore(Request $request)
    {
        $validated = $request->validate([
            'title'           => 'required|string|max:255',
            'description'     => 'required|string',
            'total_questions' => 'required|integer|min:1',
            'reward_point'    => 'required|integer|min:0',
            'status'          => 'required|boolean',
        ]);

        Challenge::create($validated);

        return redirect()->route('counselor.education.challenges.index')
            ->with('success', 'Challenge berhasil ditambahkan.');
    }

    public function challengeEdit(Challenge $challenge)
    {
        return view('admin.education.challenges.form', compact('challenge'));
    }

    public function challengeUpdate(Request $request, Challenge $challenge)
    {
        $validated = $request->validate([
            'title'           => 'required|string|max:255',
            'description'     => 'required|string',
            'total_questions' => 'required|integer|min:1',
            'reward_point'    => 'required|integer|min:0',
            'status'          => 'required|boolean',
        ]);

        $challenge->update($validated);

        return redirect()->route('counselor.education.challenges.index')
            ->with('success', 'Challenge berhasil diperbarui.');
    }

    public function challengeDestroy(Challenge $challenge)
    {
        $challenge->delete();
        return redirect()->route('counselor.education.challenges.index')
            ->with('success', 'Challenge berhasil dihapus.');
    }
}
