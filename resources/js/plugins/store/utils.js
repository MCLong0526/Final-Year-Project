import { getActivePinia } from 'pinia'

export function resetAllStoreState() {
  getActivePinia()._s.forEach(store => store.$reset())
}
