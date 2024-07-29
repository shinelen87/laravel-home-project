<?php

namespace Tests\Feature\Admin;

use App\Enums\Role;
use App\Models\Category;
use Tests\TestCase;
use Tests\Feature\Traits\SetupTrait;

class CategoriesControllerTest extends TestCase
{
    use SetupTrait;

    public function test_allow_see_categories_for_admin_role()
    {
        $categories = Category::factory(5)->create();

        $response = $this->actingAs($this->user())
            ->get(route('admin.categories.index'));

        $response->assertSuccessful();
        $response->assertViewIs('admin.categories.index'); // check view
        $response->assertSeeInOrder($categories->pluck('name')->toArray());
    }

    public function test_allow_see_categories_for_moderator_role()
    {
        $categories = Category::factory(5)->create();

        $response = $this->actingAs($this->user(Role::MODERATOR))
            ->get(route('admin.categories.index'));

        $response->assertSuccessful();
        $response->assertViewIs('admin.categories.index'); // check view
        $response->assertSeeInOrder($categories->pluck('name')->toArray());
    }

    public function test_does_not_allow_see_categories_for_customer_role()
    {
        $response = $this->actingAs($this->user(Role::CUSTOMER))
            ->get(route('admin.categories.index'));

        $response->assertForbidden();
    }

    public function test_it_creates_category_with_valid_data()
    {
        $data = Category::factory()->makeOne()->toArray();

        $this->assertDatabaseMissing(Category::class, [
            'name' => $data['name']
        ]);

        $response = $this->actingAs($this->user())
            ->post(route('admin.categories.store'), $data);

        $response->assertStatus(302);
        $response->assertRedirectToRoute('admin.categories.index');

        $response->assertSessionHas('toasts');
        $response->assertSessionHas(
            'toasts',
            fn ($collection) => $collection->first()['message'] === "Category [$data[name]] was created."
        );

        $this->assertDatabaseHas(Category::class, [
            'name' => $data['name']
        ]);
    }

    public function test_it_creates_category_with_parent_from_valid_data()
    {
        $parent = Category::factory()->createOne();
        $data = Category::factory()->makeOne(['parent_id' => $parent->id])->toArray();

        $this->assertDatabaseMissing(Category::class, [
            'name' => $data['name']
        ]);

        $this->actingAs($this->user())->post(route('admin.categories.store'), $data);

        $this->assertDatabaseHas(Category::class, [
            'name' => $data['name'],
            'parent_id' => $parent->id
        ]);
    }

    public function test_does_not_create_category_with_invalid_name()
    {
        $data = ['name' => 'a'];

        $this->assertDatabaseMissing(Category::class, [
            'name' => $data['name']
        ]);

        $response = $this->actingAs($this->user())
            ->post(route('admin.categories.store'), $data);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['name']);
        $response->assertRedirectToRoute('admin.categories.create');
        $this->assertDatabaseMissing(Category::class, [
            'name' => $data['name']
        ]);
    }

    public function test_does_not_create_category_with_invalid_parent_id()
    {
        $data = Category::factory()->makeOne(['parent_id' => 99999999])->toArray();

        $this->assertDatabaseMissing(Category::class, [
            'name' => $data['name']
        ]);

        $response = $this->actingAs($this->user())
            ->post(route('admin.categories.store'), $data);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['parent_id']);
        $response->assertRedirectToRoute('admin.categories.create');
        $this->assertDatabaseMissing(Category::class, [
            'name' => $data['name']
        ]);
    }

    public function test_it_updates_category_with_valid_data()
    {
        $newName = 'updated';
        $category = Category::factory()->createOne();
        $data = array_merge($category->toArray(), ['name' => $newName]);

        $this->assertDatabaseHas(Category::class, [
            'name' => $category->name,
            'slug' => $category->slug
        ]);
        $this->assertDatabaseMissing(Category::class, [
            'name' => $newName,
            'slug' => $newName
        ]);

        $this->actingAs($this->user())
            ->put(route('admin.categories.update', $category), $data);

        $this->assertDatabaseHas(Category::class, [
            'name' => $newName,
            'slug' => $newName
        ]);
        $this->assertDatabaseMissing(Category::class, [
            'name' => $category->name,
            'slug' => $category->slug
        ]);
    }

    public function test_it_removes_category_for_admin_role()
    {
        $category = Category::factory()->create();

        $this->assertDatabaseHas(Category::class, [
            'id' => $category->id
        ]);

        $this->actingAs($this->user())
            ->delete(route('admin.categories.destroy', $category));


        $this->assertDatabaseMissing(Category::class, [
            'id' => $category->id
        ]);
    }

    public function test_it_removes_category_and_set_null_to_child()
    {
        $category = Category::factory()->createOne();
        $child = Category::factory()->createOne(['parent_id' => $category->id]);

        $this->assertDatabaseHas(Category::class, [
            'id' => $category->id
        ]);
        $this->assertEquals($category->id, $child->parent_id);

        $this->actingAs($this->user())
            ->delete(route('admin.categories.destroy', $category));


        $this->assertDatabaseMissing(Category::class, [
            'id' => $category->id
        ]);

        $child->refresh();

        $this->assertNull($child->parent_id);
    }

    public function test_admin_can_view_categories()
    {
        $this->actingAs($this->user(Role::ADMIN))
            ->get(route('admin.categories.index'))
            ->assertStatus(200);
    }

    public function test_moderator_can_view_categories()
    {
        $this->actingAs($this->user(Role::MODERATOR))
            ->get(route('admin.categories.index'))
            ->assertStatus(200);
    }

    public function test_customer_cannot_view_categories()
    {
        $this->actingAs($this->user(Role::CUSTOMER))
            ->get(route('admin.categories.index'))
            ->assertStatus(403);
    }

    public function test_admin_can_view_create_category_page()
    {
        $this->actingAs($this->user(Role::ADMIN))
            ->get(route('admin.categories.create'))
            ->assertStatus(200);
    }

    public function test_moderator_can_view_create_category_page()
    {
        $this->actingAs($this->user(Role::MODERATOR))
            ->get(route('admin.categories.create'))
            ->assertStatus(200);
    }

    public function test_customer_cannot_view_create_category_page()
    {
        $this->actingAs($this->user(Role::CUSTOMER))
            ->get(route('admin.categories.create'))
            ->assertStatus(403);
    }

    public function test_admin_can_create_category_with_valid_data()
    {
        $data = Category::factory()->make()->toArray();

        $this->actingAs($this->user(Role::ADMIN))
            ->post(route('admin.categories.store'), $data)
            ->assertStatus(302)
            ->assertRedirect(route('admin.categories.index'));

        $this->assertDatabaseHas('categories', $data);
    }

    public function test_moderator_can_view_edit_category_page()
    {
        $category = Category::factory()->create();

        $this->actingAs($this->user(Role::MODERATOR))
            ->get(route('admin.categories.edit', $category))
            ->assertStatus(200);
    }

    public function test_customer_cannot_view_edit_category_page()
    {
        $category = Category::factory()->create();

        $this->actingAs($this->user(Role::CUSTOMER))
            ->get(route('admin.categories.edit', $category))
            ->assertStatus(403);
    }

    public function test_admin_can_update_category_with_valid_data()
    {
        $category = Category::factory()->create();
        $data = ['name' => 'Updated Category'];

        $this->actingAs($this->user(Role::ADMIN))
            ->put(route('admin.categories.update', $category), $data)
            ->assertStatus(302)
            ->assertRedirect(route('admin.categories.index'));

        $this->assertDatabaseHas('categories', $data);
    }

    public function test_admin_can_delete_category()
    {
        $category = Category::factory()->create();

        $this->actingAs($this->user(Role::ADMIN))
            ->delete(route('admin.categories.destroy', $category))
            ->assertStatus(302)
            ->assertRedirect(route('admin.categories.index'));

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }
}
