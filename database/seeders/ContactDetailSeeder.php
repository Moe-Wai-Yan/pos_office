<?php

namespace Database\Seeders;

use App\Models\ContactDetail;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ContactDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $contact = new ContactDetail();
        $contact->email = 'app.com.mm@gmail.com';
        $contact->phone = '09798574131';
        $contact->address = 'Htu Par Yone St, Tharketa, Yangon';
        $contact->save();
    }
}
