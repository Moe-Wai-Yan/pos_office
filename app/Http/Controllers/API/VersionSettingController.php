<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\VersionSetting;
use Illuminate\Http\Request;

class VersionSettingController extends BaseController
{
    public function index()
    {
        $version = VersionSetting::first();
        if (!$version) {
            return $this->sendError(204, 'No Version Found');
        }
        return $this->sendResponse('success', $version);
    }
}
