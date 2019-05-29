<template>
  <div class="photo-list-test">
    <v-app id="inspire">
      <!--<div class="grid">-->
      <div>
        <!--
      <User
        class="grid__item"
        v-for="user in users"
        :key="user.id"
        :item="user"
        @like="onLikeClick"
      />
        -->

        <v-toolbar flat color="white">
          <v-toolbar-title>ユーザリストtest</v-toolbar-title>
          <v-divider class="mx-2" inset vertical></v-divider>
          <!-- v-spacer  -->
          <v-spacer></v-spacer>

          <v-dialog v-model="dialog" max-width="700px">
            <template v-slot:activator="{ on }">
              <v-btn color="primary" dark class="mb-12" v-on="on">新規作成</v-btn>
            </template>
            <v-card>
              <v-card-title>
                <span class="headline">{{ formTitle }}</span>
              </v-card-title>

              <v-card-text>
                <v-container grid-list-md>
                  <v-layout wrap>
                    <v-flex xs8 sm8 md8>
                      <v-text-field
                        v-model="editedItem.name"
                        label="名前*"
                        :rules="[rules.required]"
                        clearable
                        required
                      ></v-text-field>
                    </v-flex>
                    <v-flex xs8 sm8 md8>
                      <v-text-field
                        v-model="editedItem.email"
                        label="email*"
                        :rules="[rules.required, rules.email]"
                        clearable
                        required
                      ></v-text-field>
                    </v-flex>
                    <v-flex xs5 sm5 md5>
                      <v-select
                        v-model="editedItem.usertype_id"
                        item-text="label"
                        item-value="value"
                        :items="usertypes"
                        label="ユーザタイプID*"
                        required
                      ></v-select>
                    </v-flex>
                    <v-flex xs5 sm5 md5>
                      <v-select
                        v-model="editedItem.workingtime_type"
                        item-text="label"
                        item-value="value"
                        :items="workingtimetypes"
                        label="勤務形態*"
                        required
                      ></v-select>
                    </v-flex>
                    <v-flex xs5 sm5 md5>
                      <v-text-field
                        v-model="editedItem.worktime_day"
                        type="Number"
                        min="0"
                        label="一日の勤務時間"
                      ></v-text-field>
                    </v-flex>
                    <v-flex xs5 sm5 md5>
                      <v-text-field
                        v-model="editedItem.maxworktime_month"
                        :rules="[rules.naturalNumber]"
                        type="Number"
                        min="0"
                        label="月の上限勤務時間"
                      ></v-text-field>
                    </v-flex>
                    <v-flex xs5 sm5 md5>
                      <v-text-field
                        v-model="editedItem.workingtime_min"
                        type="Number"
                        min="0"
                        label="勤務時間下限"
                      ></v-text-field>
                    </v-flex>
                    <v-flex xs5 sm5 md5>
                      <v-text-field
                        v-model="editedItem.workingtime_max"
                        type="Number"
                        min="0"
                        label="勤務時間上限"
                      ></v-text-field>
                    </v-flex>
                    <v-flex xs5 sm5 md5>
                      <v-text-field v-model="editedItem.hiredate" type="date" label="入社日"></v-text-field>
                    </v-flex>
                    <v-flex xs5 sm5 md5>
                      <v-text-field
                        v-model="editedItem.paid_holiday"
                        type="Number"
                        min="0"
                        label="有給日数"
                      ></v-text-field>
                    </v-flex>
                    <v-flex xs5 sm5 md5>
                      <v-select
                        v-model="editedItem.is_admin"
                        item-text="label"
                        item-value="value"
                        :items="isadmin"
                        label="管理者フラグ*"
                        required
                      ></v-select>
                    </v-flex>
                    <v-flex xs5 sm5 md5>
                      <v-select
                        v-model="editedItem.is_deleted"
                        item-text="label"
                        item-value="value"
                        :items="isdelete"
                        label="削除フラグ*"
                        required
                      ></v-select>
                    </v-flex>
                  </v-layout>
                </v-container>
                <small>*indicates required field</small>
              </v-card-text>
              <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn @click="close">Cancel</v-btn>
                <v-btn color="success" @click="save">Save</v-btn>
              </v-card-actions>
            </v-card>
          </v-dialog>
        </v-toolbar>

        <v-data-table :headers="headers" :items="users" class="elevation-1">
          <template v-slot:items="props">
            <td class="text-xs-right">{{ props.item.id }}</td>
            <td>{{ props.item.name }}</td>
            <td class="text-xs-right">{{ props.item.email }}</td>
            <td v-if="props.item.usertype_id == 1">マネージャー</td>
            <td v-else-if="props.item.usertype_id == 2">正社員</td>
            <td v-else-if="props.item.usertype_id == 3">契約社員</td>
            <td v-else-if="props.item.usertype_id == 4">アルバイト</td>
            <td v-else-if="props.item.usertype_id == 5">インターン</td>
            <td class="text-xs-right">{{ props.item.workingtime_type }}</td>
            <td class="text-xs-right">{{ props.item.worktime_day }}</td>
            <td class="text-xs-right">{{ props.item.maxworktime_month }}</td>
            <td class="text-xs-right">{{ props.item.workingtime_min }}</td>
            <td class="text-xs-right">{{ props.item.workingtime_max }}</td>
            <td class="text-xs-right">{{ props.item.hiredate }}</td>
            <td class="text-xs-right">{{ props.item.paid_holiday }}</td>
            <td class="text-xs-right">{{ props.item.is_admin }}</td>
            <td class="text-xs-right">{{ props.item.is_deleted }}</td>
            <td class="justify-center">
              <!-- 編集ボタン -->
              <v-icon small @click="editItem(props.item)">edit</v-icon>
            </td>
            <td class="justify-center">
              <!-- 削除ボタン -->
              <v-icon small @click="deleteItem(props.item)">delete</v-icon>
            </td>
          </template>
        </v-data-table>

        <!--
      <div class="example-modal-window">
        <p>ボタンを押すとモーダルウィンドウが開きます</p>
        <button @click="openModal">開く</button>

        <User @close="closeModal" v-if="modal">
          <p>Vue.js Modal Window!</p>
          <div>
            <input v-model="message">
          </div>
          <template slot="footer">
            <button @click="doSend">送信</button>
          </template>
        </User>
      </div>
        -->
      </div>
      <!--<Pagination :current-page="currentPage" :last-page="lastPage"/>-->
    </v-app>
  </div>
</template>

<script>
import { OK } from "../util";
import Photo from "../components/Photo.vue";
import User from "../components/User.vue";
import Pagination from "../components/Pagination.vue";
import { constants } from "crypto";

export default {
  components: {
    Photo,
    User,
    Pagination
  },
  props: {
    page: {
      type: Number,
      required: false,
      default: 1
    }
  },
  computed: {
    formTitle() {
      return this.editedIndex === -1 ? "ユーザ新規作成" : "ユーザ編集";
    }
  },
  data() {
    return {
      dialog: false,
      photos: [],
      usertypes: [
        { label: "マネージャー", value: 1 },
        { label: "正社員", value: 2 },
        { label: "契約社員", value: 3 },
        { label: "アルバイト", value: 4 },
        { label: "インターン", value: 5 }
      ],
      workingtimetypes: [
        { label: "勤務日数により変動", value: 1 },
        { label: "固定勤務時間", value: 2 }
      ],
      isadmin: [
        { label: "一般ユーザ", value: false },
        { label: "管理者", value: true }
      ],
      isdelete: [
        { label: "active", value: false },
        { label: "not active", value: true }
      ],
      rules: {
        required: value => !!value || "This field is required.",
        naturalNumber: value => value > 0 || "Number only.",
        email: value => {
          const pattern = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
          return pattern.test(value) || "Not an email address.";
        }
      },
      headers: [
        {
          text: "ID",
          align: "left",
          value: "id"
        },
        { text: "名前", value: "name" },
        { text: "email", value: "email" },
        { text: "ユーザタイプID", value: "usertype_id" },
        { text: "勤務形態", value: "workingtime_type" },
        { text: "一日の勤務時間", value: "worktime_day" },
        { text: "月の上限勤務時間", value: "maxworktime_month" },
        { text: "勤務時間下限", value: "workingtime_min" },
        { text: "勤務時間上限", value: "workingtime_max" },
        { text: "入社日", value: "hiredate" },
        { text: "有給日数", value: "paid_holiday" },
        { text: "管理者フラグ", value: "is_admin" },
        { text: "削除フラグ", value: "is_deleted" },
        { text: "編集", value: 0, sortable: false },
        { text: "削除", value: 0, sortable: false }
      ],
      users: [],
      editedIndex: -1,
      editedItem: {
        id: "",
        name: "",
        email: "",
        usertype_id: null,
        workingtime_type: null,
        worktime_day: null,
        maxworktime_month: null,
        workingtime_min: null,
        workingtime_max: null,
        hiredate: null,
        paid_holiday: null,
        is_admin: false,
        is_deleted: false
      },
      defaultItem: {
        id: "",
        name: "",
        email: "test@e3sys.co.jp",
        usertype_id: 2,
        workingtime_type: 1,
        worktime_day: 8,
        maxworktime_month: 20,
        workingtime_min: 160,
        workingtime_max: 180,
        hiredate: "2019-05-01",
        paid_holiday: 10,
        is_admin: false,
        is_deleted: false
      },
      currentPage: 0,
      lastPage: 0,
      modal: false,
      message: ""
    };
  },
  created() {
    this.initialize();
  },
  methods: {
    async fetchPhotos() {
      const response = await axios.get(`/api/photos/?page=${this.page}`);

      if (response.status !== OK) {
        this.$store.commit("error/setCode", response.status);
        return false;
      }

      //this.currentPage = response.data.current_page;
      //this.lastPage = response.data.last_page;
      this.users = response.data;
    },
    onLikeClick({ id, liked }) {
      console.log("onLikeClick");
      if (!this.$store.getters["auth/check"]) {
        alert("いいね機能を使うにはログインしてください。");
        return false;
      }

      if (liked) {
        this.unlike(id);
      } else {
        this.like(id);
      }
    },
    async like(id) {
      console.log("like");
      const response = await axios.put(`/api/photos/${id}/like`);

      if (response.status !== OK) {
        this.$store.commit("error/setCode", response.status);
        return false;
      }

      this.photos = this.photos.map(photo => {
        if (photo.id === response.data.photo_id) {
          photo.likes_count += 1;
          photo.liked_by_user = true;
        }
        return photo;
      });
    },
    async unlike(id) {
      console.log("unlike");
      const response = await axios.delete(`/api/photos/${id}/like`);

      if (response.status !== OK) {
        this.$store.commit("error/setCode", response.status);
        return false;
      }

      this.photos = this.photos.map(photo => {
        if (photo.id === response.data.photo_id) {
          photo.likes_count -= 1;
          photo.liked_by_user = false;
        }
        return photo;
      });
    },
    async storeuser(editedItem) {
      console.log("storeuser");
      const response = await axios.post(`/api/user/${editedItem.id}/store`, {
        id: editedItem.id,
        name: editedItem.name,
        email: editedItem.email,
        usertype_id: editedItem.usertype_id,
        workingtime_type: editedItem.workingtime_type,
        worktime_day: editedItem.worktime_day,
        maxworktime_month: editedItem.maxworktime_month,
        workingtime_min: editedItem.workingtime_min,
        workingtime_max: editedItem.workingtime_max,
        hiredate: editedItem.hiredate,
        paid_holiday: editedItem.paid_holiday,
        is_admin: editedItem.is_admin,
        is_deleted: editedItem.is_deleted
      });

      if (response.status !== OK) {
        this.$store.commit("error/setCode", response.status);
        return false;
      }
    },
    openModal() {
      console.log("openModal");
      this.modal = true;
    },
    closeModal() {
      console.log("closeModal");
      this.modal = false;
    },
    doSend() {
      console.log("doSend");
      if (this.message.length > 0) {
        alert(this.message);
        this.message = "";
        this.closeModal();
      } else {
        alert("メッセージを入力してください");
      }
    },
    initialize() {
      console.log("initialize");
      this.editedItem = Object.assign({}, this.defaultItem);
    },

    editItem(item) {
      console.log("editItem");
      this.editedIndex = this.users.indexOf(item);
      this.editedItem = Object.assign({}, item);
      this.dialog = true;
    },

    deleteItem(item) {
      console.log("deleteItem");
      const index = this.users.indexOf(item);
      confirm("Are you sure you want to delete this item?") &&
        this.users.splice(index, 1);
    },

    close() {
      console.log("close");
      this.dialog = false;
      setTimeout(() => {
        this.editedItem = Object.assign({}, this.defaultItem);
        this.editedIndex = -1;
      }, 300);
    },

    save() {
      console.log("save");
      if (this.editedIndex > -1) {
        Object.assign(this.users[this.editedIndex], this.editedItem);
        this.storeuser(this.editedItem);
      } else {
        this.users.push(this.editedItem);
      }
      this.close();
    }
  },
  watch: {
    $route: {
      async handler() {
        console.log("handler");
        await this.fetchPhotos();
      },
      immediate: true
    },
    dialog(val) {
      console.log("dialog", val);
      val || this.close();
    }
  }
};
</script>
