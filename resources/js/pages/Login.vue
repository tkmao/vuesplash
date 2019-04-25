<template>
  <div class="container--small">
    <ul class="tab">
      <li class="tab__item" :class="{'tab__item--active': tab === 1 }" @click="tab = 1">Login</li>
      <li class="tab__item" :class="{'tab__item--active': tab === 2 }" @click="tab = 2">Register</li>
    </ul>
    <div class="panel" v-show="tab === 1">
      <form class="form" @submit.prevent="login">
        <!-- エラーメッセージ表示欄 -->
        <div v-if="loginErrors" class="errors">
          <ul v-if="loginErrors.email">
            <li v-for="msg in loginErrors.email" :key="msg">{{ msg }}</li>
          </ul>
          <ul v-if="loginErrors.password">
            <li v-for="msg in loginErrors.password" :key="msg">{{ msg }}</li>
          </ul>
        </div>
        <!-- ログインフォーム表示欄 -->
        <label for="login-email">Email</label>
        <input type="text" class="form__item" id="login-email" v-model="loginForm.email">
        <label for="login-password">Password</label>
        <input type="password" class="form__item" id="login-password" v-model="loginForm.password">
        <div class="form__button">
          <button type="submit" class="button button--inverse">login</button>
        </div>
      </form>
    </div>
    <div class="panel" v-show="tab === 2">
      <form class="form" @submit.prevent="register">
        <!-- エラーメッセージ表示欄 -->
        <div v-if="registerErrors" class="errors">
          <ul v-if="registerErrors.name">
            <li v-for="msg in registerErrors.name" :key="msg">{{ msg }}</li>
          </ul>
          <ul v-if="registerErrors.email">
            <li v-for="msg in registerErrors.email" :key="msg">{{ msg }}</li>
          </ul>
          <ul v-if="registerErrors.password">
            <li v-for="msg in registerErrors.password" :key="msg">{{ msg }}</li>
          </ul>
        </div>
        <!-- ログインユーザ登録フォーム表示欄 -->
        <label for="username">Name</label>
        <input type="text" class="form__item" id="username" v-model="registerForm.name">
        <label for="email">Email</label>
        <input type="text" class="form__item" id="email" v-model="registerForm.email">
        <label for="password">Password</label>
        <input type="password" class="form__item" id="password" v-model="registerForm.password">
        <label for="password-confirmation">Password (confirm)</label>
        <input
          type="password"
          class="form__item"
          id="password-confirmation"
          v-model="registerForm.password_confirmation"
        >
        <div class="form__button">
          <button type="submit" class="button button--inverse">register</button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
// mapState をインポート
// mapState は名前の通り、コンポーネントの算出プロパティとストアのステートをマッピングする関数
import { mapState } from "vuex";

export default {
  data() {
    return {
      tab: 1,
      loginForm: {
        email: "",
        password: ""
      },
      registerForm: {
        name: "",
        email: "",
        password: "",
        password_confirmation: ""
      }
    };
  },
  // 通信失敗の場合、つまり apiStatus が false の場合にはトップページへの移動処理を行わないように制御を加えます。
  computed: {
    /*
    apiStatus() {
      return this.$store.state.auth.apiStatus;
    },
    loginErrors() {
      return this.$store.state.auth.loginErrorMessages;
    }
    */
    // mapState は名前の通り、コンポーネントの算出プロパティとストアのステートをマッピングする関数
    ...mapState({
      apiStatus: state => state.auth.apiStatus,
      loginErrors: state => state.auth.loginErrorMessages,
      registerErrors: state => state.auth.registerErrorMessages
    })
  },
  methods: {
    async login() {
      // authストアのloginアクションを呼び出す
      await this.$store.dispatch("auth/login", this.loginForm);

      // apiStatus が成功（true）だった場合のみトップページに移動
      if (this.apiStatus) {
        // トップページに移動する
        this.$router.push("/");
      }
    },
    async register() {
      // authストアのresigterアクションを呼び出す
      // 第一引数はアクションの名前
      // 第二引数にはフォームの入力値
      await this.$store.dispatch("auth/register", this.registerForm);

      // apiStatus が成功（true）だった場合のみトップページに移動
      if (this.apiStatus) {
        // トップページに移動する
        this.$router.push("/");
      }
    },
    // ログインページを表示するタイミング、つまり created ライフサイクルフックでエラーをクリアしましょう。
    // これを外すと、エラーが表示された状態でナビバーの左上のリンクから別のページに移動して、またログインページに戻ってくると以前のエラーが表示されたままになる
    clearError() {
      this.$store.commit("auth/setLoginErrorMessages", null);
      this.$store.commit("auth/setRegisterErrorMessages", null);
    }
  },
  created() {
    this.clearError();
  }
};
</script>