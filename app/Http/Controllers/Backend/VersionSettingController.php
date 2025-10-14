<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\VersionSetting;
use Illuminate\Http\Request;

class VersionSettingController extends Controller
{
    public function index()
    {
        return view('backend.version-setting.index');
    }

    public function edit($versionId) {
        $version = VersionSetting::find($versionId);
        return view('backend.version-setting.edit', compact('version'));
    }

    public function update(Request $request, $versionId)
    {
        $version = VersionSetting::find($versionId);
        if ($version) {
            $version->android_version = $request->android_version ?? $version->android_version;
            $version->ios_version = $request->ios_version ?? $version->ios_version;
            $version->playstore_link = $request->playstore_link ?? $version->playstore_link;
            $version->appstore_link = $request->appstore_link ?? $version->appstore_link;
            $version->android_other_link = $request->android_other_link ?? $version->android_other_link;
            $version->ios_other_link = $request->ios_other_link ?? $version->ios_other_link;
            $version->release_note = $request->release_note ?? $version->release_note;
            $version->update();
        }
        return redirect()->route('versionSetting');
    }

    public function serverSide()
    {
        $versions = VersionSetting::orderBy('id','desc')->get();
        return datatables($versions)
        ->addColumn('action', function ($each) {
            $edit_icon = '<a href="'.route('versionSetting.edit', $each->id).'" class="btn btn-sm btn-success mr-3 edit_btn"><i class="mdi mdi-square-edit-outline btn_icon_size"></i></a>';

            return '<div class="action_icon">'. $edit_icon.'</div>';
        })
        ->rawColumns(['action'])
        ->toJson();
    }
}
