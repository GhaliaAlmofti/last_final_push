<?php

namespace Tests\Feature\Admin\Book;

use App\Models\Book\Book;
use App\Models\User;
use App\Models\Book\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BookManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();
        
        Storage::fake('public');
        
        $this->admin = User::factory()->create(['type' => 'admin']);
        // Create the category once
        $this->category = Category::create(['name' => 'Technology']);
    }

    #[Test]
    public function an_admin_can_create_a_book_with_multiple_authors_and_a_cover()
    {
        $owner = User::factory()->create(['type' => 'author']);
        $coAuthor = User::factory()->create(['type' => 'author']);

        $payload = [
            'category_id'  => $this->category->id,
            'title'        => 'The Art of Clean Code',
            'isbn'         => '978-3-16-148410-0',
            'price'        => 50.00,
            'publish_year' => 2025,
            'stock'        => 10,
            'owner_id'     => $owner->id,
            'author_ids'   => [$coAuthor->id],
            'cover'        => UploadedFile::fake()->image('cover.jpg')
        ];

        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/v1/admin/books', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.title', 'The Art of Clean Code');

        $this->assertDatabaseHas('books', ['title' => 'The Art of Clean Code']);
    }

    #[Test]
    public function it_can_show_book_details_by_slug()
    {
        // Note: The boot method in your model will generate the slug automatically
        $book = Book::create([
            'title'        => 'Mastering Laravel',
            'category_id'  => $this->category->id,
            'isbn'         => '999-888-777',
            'price'        => 75.00,
            'publish_year' => 2024,
            'stock'        => 20,
        ]);

        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson("/api/v1/admin/books/{$book->slug}");

        $response->assertStatus(200)
            ->assertJsonPath('data.title', 'Mastering Laravel');
    }

    #[Test]
    public function it_can_update_a_book_and_replace_the_physical_cover_file()
    {
        $book = Book::create([
            'title' => 'Old Title',
            'category_id' => $this->category->id,
            'isbn' => '555-555',
            'price' => 10,
            'publish_year' => 2020,
            'stock' => 5
        ]);

        $oldPath = 'books/covers/old-image.jpg';
        Storage::disk('public')->put($oldPath, 'fake content');
        
        // Fix: Provide mime_type and size to satisfy DB constraints
        $book->cover()->create([
            'file_path' => $oldPath,
            'file_name' => 'old-image.jpg',
            'collection' => 'cover',
            'mime_type' => 'image/jpeg',
            'size' => 1024
        ]);

        $owner = User::factory()->create();

        $response = $this->actingAs($this->admin, 'sanctum')
            ->putJson("/api/v1/admin/books/{$book->slug}", [
                'title'        => 'Updated Title',
                'category_id'  => $this->category->id,
                'isbn'         => '555-555', // Existing ISBN is fine for update
                'price'        => 99.99,
                'publish_year' => 2024,
                'stock'        => 50,
                'owner_id'     => $owner->id,
                'cover'        => UploadedFile::fake()->image('new-cover.jpg')
            ]);

        $response->assertStatus(200);
        Storage::disk('public')->assertMissing($oldPath);
    }

    #[Test]
    public function it_soft_deletes_a_book_but_keeps_the_file_on_disk()
    {
        $book = Book::create([
            'title' => 'To Be Deleted',
            'category_id' => $this->category->id,
            'isbn' => '999-000',
            'price' => 10,
            'publish_year' => 2020,
            'stock' => 5
        ]);
        
        $path = 'books/covers/temp.jpg';
        Storage::disk('public')->put($path, 'content');
        
        // Fix: Provide mime_type and size to satisfy DB constraints
        $book->cover()->create([
            'file_path' => $path, 
            'collection' => 'cover', 
            'file_name' => 'temp.jpg',
            'mime_type' => 'image/jpeg',
            'size' => 1024
        ]);

        $response = $this->actingAs($this->admin, 'sanctum')
            ->deleteJson("/api/v1/admin/books/{$book->slug}");

        $response->assertStatus(200);
        $this->assertSoftDeleted('books', ['id' => $book->id]);
        Storage::disk('public')->assertExists($path);
    }
}
