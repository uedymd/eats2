<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        dd('test');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function show(Setting $setting)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Setting  $setting
     * @param  int  $site
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, Setting $setting, $site)
    {
        $settings = $setting->where('site', $site)->get();
        return view('setting', compact('settings', 'site'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Setting $setting, $site)
    {
        $count = $setting->where('site', $site)->count();

        if ($count === 0) {
            $settings = new Setting();
            $settings->site = $site;
            $settings->ng_title = $request->input('ng_title');
            $settings->ng_content = $request->input('ng_content');
            $result = $settings->save();
        } else {
            $settings = $setting->where('site', $site)->first();
            $settings->ng_title = $request->input('ng_title');
            $settings->ng_content = $request->input('ng_content');
            $result = $settings->update();
        }
        if ($result) {
            return redirect($site);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function destroy(Setting $setting)
    {
        //
    }
}
