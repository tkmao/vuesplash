import Vue from 'vue'
import Vuex from 'vuex'

import auth from './auth'
import error from './error' // error モジュール読み込み

Vue.use(Vuex)

const store = new Vuex.Store({
    modules: {
        auth,
        error    // error モジュール読み込み
    }
})

export default store

Vue.config.devtools = true;