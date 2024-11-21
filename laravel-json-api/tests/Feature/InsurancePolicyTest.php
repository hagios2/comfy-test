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

    /**
     * * @dataProvider getAppliedFilters
     */
    public function testFilterByKeyword($filterBy, $value, $assertion, $count)
    {
        $user = $this->createUser();
        Passport::actingAs($user);
        $user->assignRole('User');

        $customer1 = Customer::factory()->create(['name' => 'John Doe']);
        $customer2 = Customer::factory()->create(['name' => 'Jane Smith']);

        InsurancePolicy::factory()->create([
            'policy_no' => 'POLICY-12345',
            'policy_type' => 'Health',
            'status' => 'Active',
            'created_at' => now()->subDays(10),
            'customer_id' => $customer1->id,
        ]);

        InsurancePolicy::factory()->create([
            'policy_no' => 'POLICY-67890-12345',
            'policy_type' => 'Life',
            'status' => 'Expired',
            'created_at' => now()->subDays(5),
            'customer_id' => $customer2->id,
        ]);

        InsurancePolicy::factory()->create([
            'policy_no' => 'POLICY-99999',
            'policy_type' => 'Health',
            'status' => 'Pending',
            'created_at' => now(),
            'customer_id' => $customer2->id,
        ]);

        $this->getJson("/api/insurancePolicy?{$filterBy}=$value")
            ->assertStatus(200)
            ->assertJsonCount($count, 'data')
            ->assertJsonFragment($assertion);
    }

    public function testPagination()
    {
        $user = $this->createUser();
        Passport::actingAs($user);
        $user->assignRole('User');
        InsurancePolicy::factory(4)->create();

        $response = $this->getJson('/api/insurancePolicy?limit=2&page=1');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data')
            ->assertJsonStructure([
                'data',
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

   public function getAppliedFilters(): array
   {
        return [
            'filter_by_status_active' => [
                'filter_by' => 'status',
                'value' => 'Active',
                'assertion' => ['policy_no' => 'POLICY-12345'],
                'expected_count' => 1,
            ],
            'filter_by_policy_type_health' => [
                'filter_by' => 'policy_type',
                'value' => 'Health',
                'assertion' => ['policy_no' => 'POLICY-12345'],
                'expected_count' => 1,
            ],
            'filter_by_keyword_customer_name' => [
                'filter_by' => 'keyword',
                'value' => 'Jane',
                'assertion' => ['policy_no' => 'POLICY-67890-12345'],
                'expected_count' => 1,
            ],
            'filter_by_date_range' => [
                'filter_by' => 'start_date',
                'value' => now()->subDays(10)->toDateString() . '&end_date=' . now()->subDays(5)->toDateString(),
                'assertion' => ['policy_no' => 'POLICY-12345'],
                'expected_count' => 1,
            ],
        ];
   }
}
