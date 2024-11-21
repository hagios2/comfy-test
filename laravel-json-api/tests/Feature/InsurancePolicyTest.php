<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\InsurancePolicy;
use App\Models\User;
use Database\Seeders\RolesTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

class InsurancePolicyTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed([RolesTableSeeder::class]);
    }

    /**
     ** @dataProvider getEndpointsAndMethods
     */
    public function testCanNotAccessEndpointsWhenNotAuthenticated(string $endpoint, string $method)
    {
        $policy = InsurancePolicy::factory()->create();

        $endpoint = str_replace('{id}', $policy->id, $endpoint);

        $this->$method($endpoint, [
        ])
        ->assertStatus(401)
        ->assertExactJson(['message' => 'Unauthenticated.']);
    }

    /**
     *     * @dataProvider getEndpointsAndMethods
     */
    public function testCanNotAccessEndpointsWhenNotAuthorized(string $endpoint, string $method)
    {
        $user = $this->createUser();
        Passport::actingAs($user);
        $policy = InsurancePolicy::factory()->create();

        $endpoint = str_replace('{id}', $policy->id, $endpoint);

        $this->$method($endpoint)
        ->assertStatus(403)
        ->assertExactJson(['message' => 'This action is unauthorized.']);
    }

    public function testCanListInsurancePolicies()
    {
        $user = $this->createUser();
        Passport::actingAs($user);
        $user->assignRole('User');

        InsurancePolicy::factory(5)->create();

        $this->getJson('api/insurancePolicy')
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'customer' => [
                            'id',
                            'name',
                            'address',
                            'email'
                        ],
                        'policy_no',
                        'policy_type',
                        'status',
                        'premium_amount',
                        'start_date',
                        'end_date',
                    ],
                ],
                'links',
                'meta',
            ]);
    }

    public function testCanCreateInsurancePolicy()
    {
        $user = $this->createUser();
        Passport::actingAs($user);
        $user->assignRole('User');

        $customer = Customer::factory()->create();
        $data = [
            'customer_id' => $customer->id,
            'policy_type' => 'Life',
            'premium_amount' => 5000,
            'start_date' => '2024-01-01',
            'end_date' => '2024-12-01',
        ];

        $this->postJson('api/insurancePolicy', $data)
            ->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'policy_no',
                    'policy_type',
                    'premium_amount',
                    'start_date',
                    'end_date',
                ],
            ]);

        $this->assertDatabaseHas('insurance_policies', [
            'customer_id' => $customer->id,
            'policy_type' => 'Life',
            'premium_amount' => 5000,
        ]);
    }

    public function testCanViewInsurancePolicy()
    {
        $user = $this->createUser();
        Passport::actingAs($user);
        $user->assignRole('User');

        $policy = InsurancePolicy::factory()->create();

        $response = $this->getJson("api/insurancePolicy/{$policy->id}", [
            'Accept' => 'application/json',
        ]);

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'policy_no',
                    'policy_type',
                    'premium_amount',
                    'start_date',
                    'end_date',
                ],
            ]);
    }

    public function testCanUpdateInsurancePolicy()
    {
        $user = $this->createUser();
        Passport::actingAs($user);
        $user->assignRole('User');

        $policy = InsurancePolicy::factory()->create();

        $data = [
            'policy_type' => 'Health',
            'premium_amount' => 6000,
        ];

        $response = $this->putJson("api/insurancePolicy/{$policy->id}", $data);

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'status',
                    'policy_no',
                    'policy_type',
                    'premium_amount',
                    'start_date',
                    'end_date',
                ],
            ]);

        $this->assertDatabaseHas('insurance_policies', [
            'id' => $policy->id,
            'policy_type' => 'Health',
            'premium_amount' => 6000,
        ]);
    }

    public function testOnlyAdminCanDeleteInsurancePolicy()
    {
        $user = $this->createUser();
        Passport::actingAs($user);
        $user->assignRole('User');

        $policy = InsurancePolicy::factory()->create();

        $this->deleteJson("api/insurancePolicy/{$policy->id}")
            ->assertStatus(403)
            ->assertExactJson(['message' => 'This action is unauthorized.']);

        //login as a user with admin role

        $adminUser = $this->createUser();
        Passport::actingAs($adminUser);
        $adminUser->assignRole('Admin');

        $this->deleteJson("api/insurancePolicy/{$policy->id}")
            ->assertStatus(200)
            ->assertExactJson([
                'message' => 'Insurance Policy deleted successfully',
                'data' => [],
            ]);

        $this->assertDatabaseMissing('insurance_policies', [
            'id' => $policy->id,
        ]);
    }

    private function createUser()
    {
        return User::factory()->create();
    }

    public static function getEndpointsAndMethods(): array
    {
        return [
            ['api/insurancePolicy', 'getJson'],
            ['api/insurancePolicy', 'postJson'],
            ['api/insurancePolicy/{id}', 'getJson'],
            ['api/insurancePolicy/{id}', 'putJson'],
            ['api/insurancePolicy/{id}', 'deleteJson'],
        ];
    }
}
