<?php

use App\Models\User;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$email = 'petani1@example.com';
$user = User::where('email', $email)->first();

if ($user) {
    $oldName = $user->nama;
    $user->nama = "Pak Budi (Lama)";
    $user->save();
    echo "SUCCESS: Renamed user ($email) from '$oldName' to '{$user->nama}'\n";
} else {
    echo "ERROR: User with email $email not found.\n";
}
