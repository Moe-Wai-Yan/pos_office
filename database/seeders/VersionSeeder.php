<?php

namespace Database\Seeders;

use App\Models\VersionSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VersionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $version = new VersionSetting();
        $version->android_version = "1";
        $version->ios_version = "1";
        $version->playstore_link = "https://play.google.com/store/apps/details?id=com.stove.epic7.google&pcampaignid=web_share";
        $version->appstore_link = "https://apps.apple.com/us/app/honkai-star-rail/id1599719154";
        $version->android_other_link = "https://drive.google.com/file/d/1psALCfQxJ7HFeiKR2urkQEb0XqPax6Zk/view?usp=drive_link";
        $version->ios_other_link = "https://drive.google.com/file/d/1psALCfQxJ7HFeiKR2urkQEb0XqPax6Zk/view?usp=drive_link";
        $version->release_note = "App release in Beta";
        $version->save();
    }
}
