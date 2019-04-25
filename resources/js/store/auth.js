// ステータスコードをインポート
import { OK, UNPROCESSABLE_ENTITY } from '../util'

const state = {
    user: null,
    apiStatus: null,
    loginErrorMessages: null,  // エラーメッセージを入れる loginErrorMessages ステート
    registerErrorMessages: null
}

const getters = {
    check: state => !!state.user,
    username: state => state.user ? state.user.name : ''
}

const mutations = {
    setUser(state, user) {
        state.user = user
    },
    setApiStatus(state, status) {
        state.apiStatus = status
    },
    setLoginErrorMessages(state, messages) {
        state.loginErrorMessages = messages
    },
    setRegisterErrorMessages(state, messages) {
        state.registerErrorMessages = messages
    }
}

const actions = {
    // 会員登録
    async register(context, data) {
        context.commit('setApiStatus', null)
        const response = await axios.post('/api/register', data)

        if (response.status === CREATED) {
            context.commit('setApiStatus', true)
            context.commit('setUser', response.data)
            return false
        }

        context.commit('setApiStatus', false)
        if (response.status === UNPROCESSABLE_ENTITY) {
            context.commit('setRegisterErrorMessages', response.data.errors)
        } else {
            context.commit('error/setCode', response.status, { root: true })
        }
    },

    // ログイン
    async login(context, data) {
        // const response = await axios.post('/api/login', data)
        // context.commit('setUser', response.data)

        // apiStatus を通信結果によって更新。最初はnull
        context.commit('setApiStatus', null)
        // 通信エラーの取得。async/await を用いて非同期処理を書くと、非同期処理が成功した場合も失敗した場合も同じ変数に結果を代入できる
        // この場合、someAsyncTask が失敗すると result には error.response が代入される
        // これによって、レスポンスコードによって後続の処理を分岐させることができる
        const response = await axios.post('/api/login', data).catch(err => err.response || err)

        if (response.status === OK) {
            // 成功したらtrue
            context.commit('setApiStatus', true)
            context.commit('setUser', response.data)
            return false
        }

        // 失敗だったらfalse
        context.commit('setApiStatus', false)
        // 別モジュールのミューテーションを呼び出す
        // 通信に失敗した場合に error モジュールの setCode ミューテーション（ステートを更新するためのメソッド（同期処理））を commit していますが、
        // あるストアモジュールから別のモジュールのミューテーションを commit する場合は第三引数に { root: true } を追加します。
        /* 
        ステータスコードが UNPROCESSABLE_ENTITY の場合の分岐を追加
        バリデーションエラーの場合はルートコンポーネントに制御を渡さず、
        ページコンポーネント内でエラーの表示を行う必要があるので、
        ステータスコードが UNPROCESSABLE_ENTITY の場合は error/setCode ミューテーションを呼びません。
        代わりに loginErrorMessages にエラーメッセージをセットします。
        */
        if (response.status === UNPROCESSABLE_ENTITY) {
            context.commit('setLoginErrorMessages', response.data.errors)
        } else {
            context.commit('error/setCode', response.status, { root: true })
        }
    },

    // ログアウト
    async logout(context) {
        context.commit('setApiStatus', null)
        const response = await axios.post('/api/logout')

        if (response.status === OK) {
            context.commit('setApiStatus', true)
            context.commit('setUser', null)
            return false
        }

        context.commit('setApiStatus', false)
        context.commit('error/setCode', response.status, { root: true })
    },

    // ログインユーザーチェック
    async currentUser(context) {
        context.commit('setApiStatus', null)
        const response = await axios.get('/api/user')
        const user = response.data || null

        if (response.status === OK) {
            context.commit('setApiStatus', true)
            context.commit('setUser', user)
            return false
        }

        context.commit('setApiStatus', false)
        context.commit('error/setCode', response.status, { root: true })
    }
}

export default {
    namespaced: true,
    state,
    getters,
    mutations,
    actions
}