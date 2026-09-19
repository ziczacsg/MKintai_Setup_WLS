import { createApp, type Component } from 'vue'
import './assets/main.css'
import ProductList from './components/ProductList.vue'

const registry: Record<string, Component> = { ProductList }

function mountAll(): void {
  document.querySelectorAll<HTMLElement>('[data-vue-component]').forEach((el) => {
    const name = el.dataset.vueComponent ?? ''
    const component = registry[name]
    if (!component) {
      console.warn(`[vue] Không tìm thấy component "${name}"`)
      return
    }

    let props: Record<string, unknown> = {}
    try {
      props = JSON.parse(el.dataset.props ?? '{}')
    } catch (e) {
      console.error(`[vue] data-props của "${name}" không phải JSON hợp lệ`, e)
    }

    createApp(component, props).mount(el)
  })
}

mountAll()
