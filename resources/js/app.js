import './bootstrap'
import Vue from 'vue'
import router from './router'
import store from './store' // ★　追加
import App from './App.vue'

// ここはリロード時にセッション状態を保つために必要
const createApp = async () => {
  await store.dispatch('auth/currentUser')

  new Vue({
    el: '#app',
    router,
    store,
    components: { App },
    template: '<App />'
  })
}

createApp()
