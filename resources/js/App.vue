<template>
  <div>
    <header>
      <Navbar/>
    </header>
    <main>
      <!-- <div class="container"> -->
      <div>
        <Message/>
        <RouterView/>
      </div>
    </main>
    <Footer/>
  </div>
</template>

<script>
import Message from "./components/Message.vue";
import Navbar from "./components/Navbar.vue";
import Footer from "./components/Footer.vue";
import { NOT_FOUND, UNAUTHORIZED, INTERNAL_SERVER_ERROR } from "./util";

export default {
  components: {
    Message,
    Navbar,
    Footer
  },
  computed: {
    errorCode() {
      return this.$store.state.error.code;
    }
  },
  watch: {
    // $route() が実行された時に、storeのエラーコードの値が更新された時に実行される
    errorCode: {
      async handler(val) {
        if (val === INTERNAL_SERVER_ERROR) {
          this.$router.push("/500");
        } else if (val === UNAUTHORIZED) {
          await axios.get("/api/refresh-token");
          this.$store.commit("auth/setUser", null);
          this.$router.push("/login");
        } else if (val === NOT_FOUND) {
          this.$router.push("/not-found");
        } else if (!val) {
          await axios.get("/api/refresh-token");
          this.$store.commit("auth/setUser", null);
          this.$router.push("/login");
        }
      },
      immediate: true
    },
    // ページ遷移を検知する
    $route() {
      this.$store.commit("error/setCode", null);
    }
  }
};
</script>
