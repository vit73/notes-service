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
  <main>
    <h1>Заметки</h1>

    <form @submit.prevent="saveNote">
      <h2>{{ editingId === null ? 'Новая заметка' : 'Редактирование' }}</h2>

      <input
        v-model="title"
        type="text"
        placeholder="Заголовок"
        maxlength="255"
        required
      />

      <textarea
        v-model="content"
        placeholder="Текст заметки"
        rows="5"
        required
      />

      <button type="submit" :disabled="saving">
        {{ saving ? 'Сохранение...' : 'Сохранить' }}
      </button>

      <button
        v-if="editingId !== null"
        type="button"
        @click="resetForm"
      >
        Отмена
      </button>
    </form>

    <p v-if="error" role="alert">{{ error }}</p>

    <h2>Список заметок</h2>

    <p v-if="loading">Загрузка...</p>

    <p v-else-if="notes.length === 0">Заметок пока нет</p>

    <ul v-else>
      <li v-for="note in notes" :key="note.id">
        <h3>{{ note.title }}</h3>
        <p class="note-content">{{ note.content }}</p>

        <button type="button" @click="editNote(note)">
          Изменить
        </button>

        <button type="button" @click="removeNote(note.id)">
          Удалить
        </button>
      </li>
    </ul>
  </main>
</template>

<style scoped>
main {
  max-width: 700px;
  margin: 40px auto;
  padding: 0 16px;
  font-family: Arial, sans-serif;
}

input,
textarea {
  display: block;
  box-sizing: border-box;
  width: 100%;
  margin-bottom: 12px;
  padding: 8px;
  font: inherit;
}

button {
  margin-right: 8px;
  padding: 6px 12px;
  cursor: pointer;
}

form {
  margin-bottom: 32px;
}

ul {
  list-style: none;
  padding: 0;
}

li {
  border-top: 1px solid #ccc;
  padding: 16px 0;
}

.note-content {
  white-space: pre-wrap;
  overflow-wrap: anywhere;
}

[role='alert'] {
  color: #b00020;
}
</style>
