<?php

namespace App\Http\Controllers;

use App\Models\Trainer;
use App\Models\Facility;
use App\Models\Plan;
use App\Models\Setting;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $trainers = Trainer::all();
        $facilities = Facility::all();
        $settings = Setting::all()->pluck('value', 'key');
        
        $categories = \App\Models\PlanCategory::with(['plans' => function($query) {
            $query->where('is_active', true)->orderBy('price', 'asc');
        }])->where('is_active', true)->get();

        return view('frontend.home', compact('trainers', 'facilities', 'settings', 'categories'));
    }

    public function getPlansByCategory($slug)
    {
        $category = \App\Models\PlanCategory::with(['plans' => function($query) {
            $query->where('is_active', true)->orderBy('price', 'asc');
        }])->where('slug', $slug)->firstOrFail();

        return view('frontend.partials.plans_list', compact('category'))->render();
    }
}
