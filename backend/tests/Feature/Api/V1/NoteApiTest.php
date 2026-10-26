<?php

namespace Tests\Feature\Api\V1;

use App\Models\Note;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NoteApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_a_list_of_notes(): void
    {
        Note::create([
            'title' => 'Первая заметка',
            'content' => 'Первый текст',
        ]);

        Note::create([
            'title' => 'Вторая заметка',
            'content' => 'Второй текст',
        ]);

        $response = $this->getJson('/api/v1/notes');

        $response
            ->assertOk()
            ->assertJsonCount(2, 'data');
    }

    public function test_it_creates_a_note(): void
    {
        $response = $this->postJson('/api/v1/notes', [
            'title' => 'Новая заметка',
            'content' => 'Содержимое заметки',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.title', 'Новая заметка');

        $this->assertDatabaseHas('notes', [
            'title' => 'Новая заметка',
            'content' => 'Содержимое заметки',
        ]);
    }

    public function test_it_returns_a_single_note(): void
    {
        $note = Note::create([
            'title' => 'Тестовая заметка',
            'content' => 'Тестовый текст',
        ]);

        $response = $this->getJson("/api/v1/notes/{$note->id}");

        $response
            ->assertOk()
            ->assertJsonPath('data.id', $note->id)
            ->assertJsonPath('data.title', 'Тестовая заметка');
    }

    public function test_it_updates_a_note(): void
    {
        $note = Note::create([
            'title' => 'Старый заголовок',
            'content' => 'Старый текст',
        ]);

        $response = $this->putJson("/api/v1/notes/{$note->id}", [
            'title' => 'Новый заголовок',
            'content' => 'Новый текст',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.title', 'Новый заголовок');

        $this->assertDatabaseHas('notes', [
            'id' => $note->id,
            'title' => 'Новый заголовок',
            'content' => 'Новый текст',
        ]);
    }

    public function test_it_deletes_a_note(): void
    {
        $note = Note::create([
            'title' => 'Удаляемая заметка',
            'content' => 'Текст',
        ]);

        $response = $this->deleteJson("/api/v1/notes/{$note->id}");
        $response->assertNoContent();
        $this->assertDatabaseMissing('notes', [
            'id' => $note->id,
        ]);
    }

    public function test_it_validates_required_fields(): void
    {
        $response = $this->postJson('/api/v1/notes', []);
        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'title',
                'content',
            ]);
    }

    public function test_it_returns_404_for_missing_note(): void
    {
        $response = $this->getJson('/api/v1/notes/999999');
        $response->assertNotFound();
    }
}
