import Vue from 'vue'
import VueRouter from 'vue-router'

import PhotoList from './pages/PhotoList.vue'
import Login from './pages/Login.vue'

// ルート定義にナビゲーションガードを追加
import store from './store' // ★　追加

// システムエラーページ
import SystemError from './pages/errors/System.vue'

// VueRouter プラグインの使用を宣言
Vue.use(VueRouter)

const routes = [
  {
    path: '/',
    component: PhotoList
  },
  {
    path: '/login',
    component: Login,
    beforeEnter(to, from, next) {
      if (store.getters['auth/check']) {
        next('/')
      } else {
        next()
      }
    }
  },
  {
    path: '/500',
    component: SystemError
  }
]

const router = new VueRouter({
  mode: 'history',
  routes
})

export default router
