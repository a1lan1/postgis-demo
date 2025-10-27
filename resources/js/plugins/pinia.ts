import { piniaAxiosPlugin } from '@/plugins/axios'
import { createPinia } from 'pinia'
import piniaPersist from 'pinia-plugin-persistedstate'

const pinia = createPinia()

pinia
  .use(piniaPersist)
  .use(piniaAxiosPlugin)

export default pinia
