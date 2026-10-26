import type { Note, NotePayload } from '../types/note'

const API_URL = '/api/v1/notes'

interface ApiResponse<T> {
  data: T
}

async function request<T>(
  url: string,
  options: RequestInit = {},
): Promise<T> {
  const response = await fetch(url, {
    ...options,
    headers: {
      Accept: 'application/json',
      ...(options.body ? { 'Content-Type': 'application/json' } : {}),
      ...options.headers,
    },
  })

  if (!response.ok) {
    throw new Error(`Ошибка запроса: ${response.status}`)
  }

  if (response.status === 204) {
    return undefined as T
  }

  return response.json() as Promise<T>
}

export async function getNotes(): Promise<Note[]> {
  const response = await request<ApiResponse<Note[]>>(API_URL)
  return response.data
}

export async function createNote(data: NotePayload): Promise<Note> {
  const response = await request<ApiResponse<Note>>(API_URL, {
    method: 'POST',
    body: JSON.stringify(data),
  })

  return response.data
}

export async function updateNote(
  id: number,
  data: NotePayload,
): Promise<Note> {
  const response = await request<ApiResponse<Note>>(`${API_URL}/${id}`, {
    method: 'PUT',
    body: JSON.stringify(data),
  })

  return response.data
}

export async function deleteNote(id: number): Promise<void> {
  await request<void>(`${API_URL}/${id}`, {
    method: 'DELETE',
  })
}
