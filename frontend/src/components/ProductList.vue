<script setup lang="ts">
import { ref } from 'vue'

interface Product {
  id: number
  name: string
  price: number
}

const props = defineProps<{ initialProducts: Product[]; apiUrl: string }>()

const products = ref<Product[]>(props.initialProducts)
const loading = ref(false)
const error = ref<string | null>(null)

async function reload(): Promise<void> {
  loading.value = true
  error.value = null
  try {
    const res = await fetch(props.apiUrl, { headers: { Accept: 'application/json' } })
    if (!res.ok) throw new Error(`HTTP ${res.status}`)
    products.value = await res.json()
  } catch (e) {
    error.value = e instanceof Error ? e.message : String(e)
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <section class="product-list">
    <p class="badge">Vue 3 đã mount aaa ✔</p>
    <ul>
      <li v-for="p in products" :key="p.id">{{ p.name }} — {{ p.price.toLocaleString('vi-VN') }} ₫</li>
    </ul>
    <button :disabled="loading" @click="reload">{{ loading ? 'Đang tải…' : 'Tải lại từ API' }}</button>
    <p v-if="error" class="error">Lỗi: {{ error }}</p>
  </section>
</template>
