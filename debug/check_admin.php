<?php
require_once 'vendor/autoload.php';
require_once 'bootstrap/app.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$user = \App\Models\User::where('role', 'admin')->first();
if ($user) {
    echo "Admin found: " . $user->email . " (ID: " . $user->user_id . ")\n";
    echo "Role: " . $user->role . "\n";
} else {
    echo "No admin user found\n";
}

$allUsers = \App\Models\User::get();
echo "\nAll users:\n";
foreach ($allUsers as $u) {
    echo "- " . $u->email . " (role: " . $u->role . ")\n";
}
?>
