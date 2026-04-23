<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Services\AssetPropertyTagService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssetPropertyTagController extends Controller
{
    public function bulk(Request $request): \Illuminate\Http\Response
    {
        abort_unless(Auth::check(), 403);

        $ids = collect(explode(',', $request->query('ids', '')))
            ->map(fn ($id) => (int) trim($id))
            ->filter()
            ->unique()
            ->values();

        abort_if($ids->isEmpty(), 400, 'No IDs provided.');

        $assets = Asset::whereIn('id', $ids)
            ->with('category')
            ->get();

        abort_if($assets->isEmpty(), 404, 'No assets found.');

        $html = AssetPropertyTagService::generate($assets);

        return response($html, 200, ['Content-Type' => 'text/html; charset=UTF-8']);
    }
}