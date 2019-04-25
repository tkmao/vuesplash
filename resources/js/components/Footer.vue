<template>
  <footer class="footer">
    <button v-if="isLogin" class="button button--link" @click="logout">Logout</button>
    <RouterLink v-else class="button button--link" to="/login">Login / Register</RouterLink>
  </footer>
</template>
<script>
// ログアウト機能のエラー対策
import { mapState, mapGetters } from "vuex";

export default {
  computed: {
    ...mapState({
      apiStatus: state => state.auth.apiStatus
    }),
    ...mapGetters({
      isLogin: "auth/check"
    })
  },
  methods: {
    async logout() {
      await this.$store.dispatch("auth/logout");

      // apiStatus が成功（true）だった場合のみログインページに移動
      if (this.apiStatus) {
        // ログインページに移動する
        this.$router.push("/login");
      }
    }
  }
};
</script>