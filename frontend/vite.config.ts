import fs from 'node:fs'
import { fileURLToPath, URL } from 'node:url'
import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'

// Chứng chỉ sinh bởi service local-dev-certs (Task 09; chỉ có trong WSL, Windows sẽ bỏ qua)
const CERT_DIR = process.env.DEV_CERT_DIR ?? '/etc/ssl/local-dev'
const keyPath = `${CERT_DIR}/local-dev-key.pem`
const certPath = `${CERT_DIR}/local-dev.pem`
const hasCert = fs.existsSync(keyPath) && fs.existsSync(certPath)

export default defineConfig({
  plugins: [vue()],
  resolve: { alias: { '@': fileURLToPath(new URL('./src', import.meta.url)) } },

  server: {
    host: true,
    port: 5173,
    strictPort: true,
    origin: `${hasCert ? 'https' : 'http'}://localhost:5173`,
    https: hasCert ? { key: fs.readFileSync(keyPath), cert: fs.readFileSync(certPath) } : undefined,
    cors: { origin: /^https?:\/\/([\w-]+\.)*localhost(:\d+)?$/ },
  },

  build: {
    outDir: '../backend/DistributionPackages/MKintai.App/Resources/Public/app',
    emptyOutDir: true,
    cssCodeSplit: false,
    rollupOptions: {
      input: 'src/main.ts',
      output: {
        entryFileNames: 'app.js',
        chunkFileNames: 'chunks/[name]-[hash].js',
        assetFileNames: (asset) => {
          const name = asset.names?.[0] ?? asset.name ?? ''
          return name.endsWith('.css') ? 'app.css' : 'assets/[name]-[hash][extname]'
        },
      },
    },
  },
})
