<?php

namespace App\Repository\UjiCoba;

use Illuminate\Http\Request;

class UjiCobaRepository
{
    public function index()
    {
        try {
            return view('ujicoba.ordertruck');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error loading index view.');
        }
    }

    public function onscrolltext()
    {
        try {
            return view('ujicoba.onscrolltextanimation');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error loading onscrolltext view.');
        }
    }

    public function darkmode()
    {
        try {
            return view('ujicoba.darkmode');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error loading darkmode view.');
        }
    }

    public function dropdown()
    {
        try {
            return view('ujicoba.dropdown');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error loading dropdown view.');
        }
    }

    public function checkbox()
    {
        try {
            return view('ujicoba.checkbox');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error loading checkbox view.');
        }
    }

    public function pb()
    {
        try {
            return view('ujicoba.pb');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error loading pb view.');
        }
    }
}
