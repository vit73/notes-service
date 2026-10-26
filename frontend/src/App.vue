<script setup lang="ts">
import { onMounted, ref } from 'vue'
import type { Note } from './types/note'
import {
  getNotes,
  createNote,
  updateNote,
  deleteNote,
} from './api/notes'

const notes = ref<Note[]>([])
const title = ref('')
const content = ref('')
const editingId = ref<number | null>(null)

const loading = ref(false)
const saving = ref(false)
const error = ref('')

async function loadNotes() {
  loading.value = true
  error.value = ''

  try {
    notes.value = await getNotes()
  } catch {
    error.value = 'Не удалось загрузить заметки'
  } finally {
    loading.value = false
  }
}

function resetForm() {
  title.value = ''
  content.value = ''
  editingId.value = null
}

function editNote(note: Note) {
  editingId.value = note.id
  title.value = note.title
  content.value = note.content
}

async function saveNote() {
  if (!title.value.trim() || !content.value.trim()) {
    error.value = 'Заполните заголовок и текст'
    return
  }

  saving.value = true
  error.value = ''

  try {
    const data = {
      title: title.value.trim(),
      content: content.value.trim(),
    }

    if (editingId.value !== null) {
      await updateNote(editingId.value, data)
    } else {
      await createNote(data)
    }

    resetForm()
    await loadNotes()
  } catch {
    error.value = 'Не удалось сохранить заметку'
  } finally {
    saving.value = false
  }
}

async function removeNote(id: number) {
  if (!window.confirm('Удалить заметку?')) {
    return
  }

  error.value = ''

  try {
    await deleteNote(id)

    if (editingId.value === id) {
      resetForm()
    }

    await loadNotes()
  } catch {
    error.value = 'Не удалось удалить заметку'
  }
}

onMounted(loadNotes)
</script>

<template>
  <main class="container">
    <header>
      <h1>Заметки</h1>
      <p>Простое приложение для хранения заметок</p>
    </header>

    <section class="form-card">
      <h2>{{ editingId === null ? 'Новая заметка' : 'Редактирование заметки' }}</h2>

      <form @submit.prevent="saveNote">
        <label for="title">Заголовок</label>
        <input
          id="title"
          v-model="title"
          type="text"
          placeholder="Введите заголовок"
          maxlength="255"
          required
        />

        <label for="content">Содержание</label>
        <textarea
          id="content"
          v-model="content"
          placeholder="Введите текст заметки"
          rows="5"
          required
        />

        <div class="form-actions">
          <button class="btn-primary" type="submit" :disabled="saving">
            {{ saving ? 'Сохранение...' : 'Сохранить' }}
          </button>

          <button
            v-if="editingId !== null"
            class="btn-secondary"
            type="button"
            @click="resetForm"
          >
            Отмена
          </button>
        </div>
      </form>
    </section>

    <p v-if="error" class="error" role="alert">{{ error }}</p>

    <section class="notes-section">
      <h2>Мои заметки</h2>

      <p v-if="loading" class="empty-state">Загрузка...</p>

      <p v-else-if="notes.length === 0" class="empty-state">
        Заметок пока нет. Создайте первую!
      </p>

      <div v-else class="notes-list">
        <article v-for="note in notes" :key="note.id" class="note-card">
          <h3>{{ note.title }}</h3>
          <p class="note-content">{{ note.content }}</p>

          <div class="note-actions">
            <button
              class="btn-secondary"
              type="button"
              @click="editNote(note)"
            >
              Изменить
            </button>

            <button
              class="btn-danger"
              type="button"
              @click="removeNote(note.id)"
            >
              Удалить
            </button>
          </div>
        </article>
      </div>
    </section>
  </main>
</template>

<style scoped>
:global(body) {
  margin: 0;
  background: #f5f6f8;
  color: #222;
  font-family: Arial, sans-serif;
}

:global(*) {
  box-sizing: border-box;
}

.container {
  max-width: 760px;
  margin: 0 auto;
  padding: 40px 20px;
}

header {
  margin-bottom: 32px;
}

h1 {
  margin: 0 0 8px;
  font-size: 30px;
}

header p {
  margin: 0;
  color: #777;
}

h2 {
  margin: 0 0 20px;
  font-size: 20px;
}

.form-card,
.note-card {
  background: #fff;
  border: 1px solid #e5e7eb;
  border-radius: 10px;
  padding: 24px;
}

.form-card {
  margin-bottom: 32px;
}

label {
  display: block;
  margin-bottom: 8px;
  font-size: 14px;
  font-weight: 600;
}

input,
textarea {
  display: block;
  width: 100%;
  padding: 12px;
  margin-bottom: 20px;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  background: #fff;
  color: #222;
  font: inherit;
}

input:focus,
textarea:focus {
  outline: none;
  border-color: #2563eb;
}

textarea {
  resize: vertical;
}

button {
  padding: 10px 16px;
  border: 0;
  border-radius: 6px;
  font: inherit;
  font-size: 14px;
  cursor: pointer;
}

button:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.btn-primary {
  background: #2563eb;
  color: white;
}

.btn-secondary {
  background: #e5e7eb;
  color: #333;
}

.btn-danger {
  background: #fee2e2;
  color: #b91c1c;
}

.form-actions,
.note-actions {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
}

.notes-section h2 {
  margin-bottom: 16px;
}

.notes-list {
  display: grid;
  gap: 16px;
}

.note-card h3 {
  margin: 0 0 12px;
  font-size: 18px;
}

.note-content {
  margin: 0 0 20px;
  color: #555;
  line-height: 1.6;
  white-space: pre-wrap;
  overflow-wrap: anywhere;
}

.empty-state {
  padding: 32px;
  text-align: center;
  color: #777;
  background: #fff;
  border: 1px dashed #d1d5db;
  border-radius: 10px;
}

.error {
  padding: 12px;
  margin-bottom: 24px;
  border-radius: 6px;
  background: #fee2e2;
  color: #b91c1c;
}

@media (max-width: 600px) {
  .container {
    padding: 24px 16px;
  }

  .form-card,
  .note-card {
    padding: 18px;
  }
}
</style>
