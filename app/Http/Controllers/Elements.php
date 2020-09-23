<?php

namespace App\Http\Controllers;
use App\Models\Element;

use Illuminate\Http\Request;

class Elements extends Controller
{
    public function home() {
        $element = Element::findOrFail(1);
        // Create a feature from the element
        $feature = [
            'type' => 'Feature',
            'id' => 1,
            'geometry' => json_decode($element->geometry)
        ];
 
        return view('welcome', ['features' => ["type" => "FeatureCollection", "features" => [$feature]]]);
    }
}
