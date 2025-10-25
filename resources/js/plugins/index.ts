import './leaflet'
import pinia from './pinia'
import axios from './axios'
import type { App } from 'vue'

export function registerPlugins(app: App) {
  app
    .use(pinia)
    .use(axios)
}
