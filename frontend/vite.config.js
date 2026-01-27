import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'

// Config do Vite: sobe o dev server para fora do container e injeta a URL da API com fallback
export default defineConfig({
  plugins: [vue()],
  server: {
    host: '0.0.0.0',
    port: 5173
  },
  // Vite vai pegar a variavel do env e injetar no build do container node
  define: {
    'VITE_API_URL': JSON.stringify(process.env.VITE_API_URL || 'http://localhost:8080')
  }
})
