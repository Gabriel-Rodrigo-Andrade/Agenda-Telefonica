import axios from 'axios'

export const api = axios.create({
  // Usa a URL injetada pelo Vite; fallback para localhost:8080 se vier null/undefined
  baseURL: VITE_API_URL ?? 'http://localhost:8080/'
})