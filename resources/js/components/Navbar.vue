<template>
  <nav class="navbar">
    <RouterLink class="navbar__brand" to="/">Vuesplash</RouterLink>
    <div class="navbar__menu">
      <div v-if="isLogin" class="navbar__item">
        <RouterLink class="nav-link" to="/workschedule">勤務表</RouterLink>
        <RouterLink class="nav-link" to="/weeklyreport">週報</RouterLink>
        <RouterLink class="nav-link" to="/user">ユーザ一覧</RouterLink>
        <RouterLink class="nav-link" to="/project">プロジェクト一覧</RouterLink>
        <button class="button" @click="showForm = ! showForm">
          <i class="icon ion-md-add"></i>
          Submit a photo
        </button>
      </div>
      <span v-if="isLogin" class="navbar__item">{{ username }}</span>
      <div v-else class="navbar__item">
        <RouterLink class="button button--link" to="/login">Login / Register</RouterLink>
      </div>
    </div>
    <PhotoForm v-model="showForm"/>
  </nav>
</template>

<script>
import PhotoForm from "./PhotoForm.vue";

export default {
  components: {
    PhotoForm
  },
  data() {
    return {
      showForm: false
    };
  },
  computed: {
    isLogin() {
      return this.$store.getters["auth/check"];
    },
    username() {
      return this.$store.getters["auth/username"];
    }
  }
};
</script>
