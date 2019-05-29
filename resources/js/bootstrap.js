import { getCookieValue } from './util'

window.axios = require('axios')

// Ajaxリクエストであることを示すヘッダーを付与する
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'
window.axios.defaults.headers.common['X-CSRF-TOKEN'] = window.Laravel.csrfToken

window.axios.interceptors.request.use(config => {
  // クッキーからトークンを取り出してヘッダーに添付する
  config.headers['X-XSRF-TOKEN'] = getCookieValue('XSRF-TOKEN')

  return config
})

let token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
  window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
  console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

window.axios.interceptors.response.use(
  response => response,
  error => error.response || error
)
