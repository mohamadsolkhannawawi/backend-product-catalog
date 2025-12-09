<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Seller;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test user registration flow (combined User + Seller onboarding)
     * Verifies:
     * - User created with UUID primary key (user_id)
     * - Seller created with representative primary key (seller_id)
     * - Both use CHAR(36) string UUIDs
     */
    public function test_seller_registration_creates_user_and_seller_with_uuid()
    {
        // Arrange: Prepare valid registration data
        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            
            'store_name' => 'John Store',
            'store_description' => 'A great store',
            
            'pic_name' => 'John Pic',
            'pic_phone' => '081234567890',
            
            'address' => 'Jl. Main St',
            'rt' => '01',
            'rw' => '02',
            
            'province_id' => '12', // Valid province code
            'city_id' => '1201', // Valid city code
            'district_id' => '120101', // Valid district code
            'village_id' => '1201010001', // Valid village code
            
            'ktp_number' => '1234567890123456',
            'pic_image' => $this->createImage('pic.jpg'),
            'ktp_file' => $this->createImage('ktp.jpg'),
        ];

        // Act: Submit registration (multipart/form-data to include files)
        $response = $this->post('/api/auth/register', $userData);

        // Assert: Response status
        $response->assertStatus(201);
        
        // Assert: User created with UUID primary key
        $user = User::where('email', 'john@example.com')->first();
        $this->assertNotNull($user);
        $this->assertNotNull($user->user_id);
        $this->assertTrue(strlen($user->user_id) === 36); // UUID string format CHAR(36)
        $this->assertMatchesRegularExpression('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $user->user_id);
        
        // Assert: Seller created with UUID primary key and references user by user_id
        $seller = Seller::where('user_id', $user->user_id)->first();
        $this->assertNotNull($seller);
        $this->assertNotNull($seller->seller_id);
        $this->assertTrue(strlen($seller->seller_id) === 36);
        $this->assertMatchesRegularExpression('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $seller->seller_id);
        
        // Assert: Response includes user and seller
        $response->assertJsonStructure([
            'message',
            'user' => ['user_id', 'email', 'role'],
            'seller' => ['seller_id', 'user_id', 'store_name'],
        ]);
    }

    /**
     * Test invalid location foreign keys are rejected
     */
    public function test_registration_fails_with_invalid_location_codes()
    {
        $userData = [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 'password123',
            
            'store_name' => 'Jane Store',
            'store_description' => null,
            
            'pic_name' => 'Jane Pic',
            'pic_phone' => '081234567890',
            
            'address' => 'Jl. Jane St',
            'rt' => '01',
            'rw' => '02',
            
            'province_id' => 'INVALID', // Invalid code
            'city_id' => '1201',
            'district_id' => '120101',
            'village_id' => '1201010001',
            
            'ktp_number' => '1234567890123456',
            'pic_image' => $this->createImage('pic.jpg'),
            'ktp_file' => $this->createImage('ktp.jpg'),
        ];

        $response = $this->post('/api/auth/register', $userData);
        
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['province_id']);
    }

    /**
     * Test that seller must be approved before login
     */
    public function test_seller_cannot_login_if_not_approved()
    {
        // Create a seller user with pending status
        $user = User::factory()->create(['role' => 'seller']);
        Seller::factory()->create([
            'user_id' => $user->user_id,
            'status' => 'pending',
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'password', // matches factory
        ]);

        $response->assertStatus(403);
        $response->assertJson(['message' => 'Account pending approval by admin']);
    }

    /**
     * Test that approved seller can login
     */
    public function test_approved_seller_can_login()
    {
        $user = User::factory()->create([
            'role' => 'seller',
            'password' => bcrypt('password123'),
        ]);
        Seller::factory()->create([
            'user_id' => $user->user_id,
            'status' => 'approved',
            'is_active' => true,  // Explicitly set to active (clicked email activation)
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Logged in']);
    }

    /**
     * Test that approved but not yet activated seller cannot login (must click email)
     */
    public function test_approved_seller_cannot_login_if_not_activated_via_email()
    {
        $user = User::factory()->create([
            'role' => 'seller',
            'password' => bcrypt('password123'),
        ]);
        Seller::factory()->create([
            'user_id' => $user->user_id,
            'status' => 'approved',
            'is_active' => false,  // Not yet clicked email activation
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertStatus(403);
        $response->assertJson(['message' => 'Please activate your account via email']);
    }

    /**
     * Helper method to create a test image
     */
    private function createImage($name)
    {
        $path = storage_path('tmp/' . $name);
        if (!is_dir(storage_path('tmp'))) {
            mkdir(storage_path('tmp'), 0755, true);
        }

        // Create a minimal valid JPEG file
        $img = imagecreatetruecolor(100, 100);
        imagefilledrectangle($img, 0, 0, 100, 100, imagecolorallocate($img, 255, 255, 255));
        imagejpeg($img, $path);
        imagedestroy($img);

        // Mark the UploadedFile as a test file (last parameter = true) so is_uploaded_file checks are bypassed
        return new \Illuminate\Http\UploadedFile($path, $name, 'image/jpeg', null, true);
    }
}
