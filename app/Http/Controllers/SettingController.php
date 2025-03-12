<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use QCod\Settings\Setting\Setting;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.setting_bot_telegram');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.setting_bot_telegram');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request);
        if ($request->has('test_chat')) {
            $message = "Test Pesan Web Gudang Skytama";
            sendTelegramMessage($message, $request->test_chat);
            if ($request['token_bot'] == null) {
                return back();
            }
        }
        $dataSettings = $request->except('_token');
        settings()->set($dataSettings);
        return redirect()->back()->with('success', 'data berhasil disimpan');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
