<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Trainer;
use App\Models\Facility;
use App\Models\Setting;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $trainers   = Trainer::where('status', true)->get();
        $facilities = Facility::all();
        $settings   = Setting::all()->pluck('value', 'key');

        // Real counts from DB
        $memberCount  = Member::count();
        $trainerCount = Trainer::where('status', true)->count();

        $categories = \App\Models\PlanCategory::with(['plans' => function ($query) {
            $query->where('is_active', true)->orderBy('price', 'asc');
        }])->where('is_active', true)->get();

        return view('frontend.home', compact(
            'trainers', 'facilities', 'settings', 'categories',
            'memberCount', 'trainerCount'
        ));
    }

    public function getPlansByCategory($slug)
    {
        $category = \App\Models\PlanCategory::with(['plans' => function ($query) {
            $query->where('is_active', true)->orderBy('price', 'asc');
        }])->where('slug', $slug)->firstOrFail();

        return view('frontend.partials.plans_list', compact('category'))->render();
    }
}
