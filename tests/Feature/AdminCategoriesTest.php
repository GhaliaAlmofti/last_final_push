<?php
namespace Tests\Feature\Admin;

use App\Enums\User\UserType;
use App\Models\Book\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test; 
use Illuminate\Support\Str;

class AdminCategoriesTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create([
            'type' => UserType::Admin,
            'status' => 'active'
        ]);
    }

    protected function createCategory(array $attributes = []): Category
    {
        $name = $attributes['name'] ?? 'Test Category ' . Str::random(5);
        
        return Category::create(array_merge([
            'name' => $name,
            'slug' => Str::slug($name),
            'parent_id' => null,
            'order_column' => 0,
        ], $attributes));
    }

    #[Test]
    public function it_can_list_categories_with_pagination_and_trio_logic()
    {
        for ($i = 1; $i <= 10; $i++) {
            $this->createCategory();
        }

        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/v1/admin/categories?per_page=5');

        $response->assertStatus(200)
            ->assertJsonCount(5, 'data.data');
    }

    #[Test]
    public function it_can_filter_categories_by_name()
    {
        $this->createCategory(['name' => 'Fiction']);
        $this->createCategory(['name' => 'History']);
        
        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/v1/admin/categories?filter[name]=Fiction');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data.data')
            ->assertJsonPath('data.data.0.name', 'Fiction');
    }

    #[Test]
    public function it_can_include_nested_children_recursively()
    {
        $parent = $this->createCategory(['name' => 'Parent', 'slug' => 'parent-slug']);
        $child = $this->createCategory(['name' => 'Child', 'parent_id' => $parent->id]);

        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson("/api/v1/admin/categories?include=childrenRecursive&filter[slug]=parent-slug");

        $response->assertStatus(200)
            ->assertJsonPath('data.data.0.children.0.id', $child->id);
    }

    #[Test]
    public function it_prevents_deletion_of_category_with_children()
    {
        $parent = $this->createCategory();
        $this->createCategory(['parent_id' => $parent->id]);

        $response = $this->actingAs($this->admin, 'sanctum')
            ->deleteJson("/api/v1/admin/categories/{$parent->slug}");

        $response->assertStatus(422)
            ->assertJsonPath('success', false);
    }

    #[Test]
    public function it_automatically_generates_slug_on_store()
    {
        $payload = ['name' => 'Science Fiction', 'parent_id' => null];

        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/v1/admin/categories', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.slug', 'science-fiction');
    }

    #[Test]
    public function it_ensures_slugs_are_unique()
    {
        // Create the first category
        $this->createCategory(['name' => 'Fiction', 'slug' => 'fiction']);
        
        $payload = [
            'name' => 'New Fiction Title',
            'slug' => 'fiction' 
        ];
        
        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/v1/admin/categories', $payload);

        $response->assertStatus(422)
                ->assertJsonValidationErrors('slug');
    }
}
