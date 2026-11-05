<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLinkRequest;
use App\Http\Requests\UpdateLinkRequest;
use App\Models\Link;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class LinkController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $links = $request->user()
            ->links()
            ->withCount('clicks')
            ->latest()
            ->paginate(10);

        return view('links.index', compact('links'));
    }

    public function create()
    {
        return view('links.create');
    }

    public function store(StoreLinkRequest $request)
    {
        $link = $request->user()->links()->create([
            ...$request->validated(),
            'code' => Link::generateCode(),
        ]);

        return redirect()
            ->route('links.index')
            ->with('status', 'Enlace creado: ' . url($link->code));
    }

    public function show(Link $link)
    {
        $this->authorize('view', $link);

        $link->loadCount('clicks');

        return view('links.show', compact('link'));
    }

    public function edit(Link $link)
    {
        $this->authorize('update', $link);

        return view('links.edit', compact('link'));
    }

    public function update(UpdateLinkRequest $request, Link $link)
    {
        $this->authorize('update', $link);

        $link->update($request->validated());

        return redirect()
            ->route('links.index')
            ->with('status', 'Enlace actualizado.');
    }

    public function destroy(Link $link)
    {
        $this->authorize('delete', $link);

        $link->delete();

        return redirect()
            ->route('links.index')
            ->with('status', 'Enlace eliminado.');
    }
}