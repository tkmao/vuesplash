<template>
  <div class="user-list">
    <v-app id="inspire">
      <div>
        <v-toolbar flat color="white">
          <v-toolbar-title>ユーザリスト</v-toolbar-title>
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
                    <p v-if="errors.length">
    <b>Please correct the following error(s):</b>
    <ul>
      <li v-for="error in errors" :key="error">{{ error }}</li>
    </ul>
  </p>
                  <v-layout wrap>
                    <v-flex xs8 sm8 md8>
                      <v-text-field
                        v-model="userItem.name"
                        label="名前*"
                        :rules="[rules.required]"
                        clearable
                        required
                      ></v-text-field>
                    </v-flex>
                    <v-flex xs8 sm8 md8>
                      <v-text-field
                        v-model="userItem.email"
                        label="email*"
                        :rules="[rules.required, rules.email]"
                        clearable
                        required
                      ></v-text-field>
                    </v-flex>
                    <v-flex xs5 sm5 md5>
                      <v-select
                        v-model="userItem.usertype_id"
                        item-text="text"
                        item-value="value"
                        :items="usertypes"
                        label="ユーザタイプID*"
                        required
                      ></v-select>
                    </v-flex>
                    <v-flex xs5 sm5 md5>
                      <v-select
                        v-model="userItem.workingtime_type"
                        item-text="text"
                        item-value="value"
                        :items="workingtimetypes"
                        label="勤務形態*"
                        required
                      ></v-select>
                    </v-flex>
                    <v-flex xs5 sm5 md5>
                      <v-text-field
                        v-model="userItem.worktime_day"
                        type="Number"
                        min="0"
                        label="一日の勤務時間"
                      ></v-text-field>
                    </v-flex>
                    <v-flex xs5 sm5 md5>
                      <v-text-field
                        v-model="userItem.maxworktime_month"
                        :rules="[rules.naturalNumber]"
                        type="Number"
                        min="0"
                        label="月の上限勤務時間"
                      ></v-text-field>
                    </v-flex>
                    <v-flex xs5 sm5 md5>
                      <v-text-field
                        v-model="userItem.workingtime_min"
                        type="Number"
                        min="0"
                        label="勤務時間下限"
                      ></v-text-field>
                    </v-flex>
                    <v-flex xs5 sm5 md5>
                      <v-text-field
                        v-model="userItem.workingtime_max"
                        type="Number"
                        min="0"
                        label="勤務時間上限"
                      ></v-text-field>
                    </v-flex>
                    <v-flex xs5 sm5 md5>
                      <v-text-field v-model="userItem.hiredate" type="date" label="入社日"></v-text-field>
                    </v-flex>
                    <v-flex xs5 sm5 md5>
                      <v-text-field
                        v-model="userItem.paid_holiday"
                        type="Number"
                        min="0"
                        label="有給日数"
                      ></v-text-field>
                    </v-flex>
                    <v-flex xs5 sm5 md5>
                      <v-select
                        v-model="userItem.is_admin"
                        item-text="text"
                        item-value="value"
                        :items="isadmin"
                        label="管理者フラグ*"
                        required
                      ></v-select>
                    </v-flex>
                    <v-flex xs5 sm5 md5>
                      <v-select
                        v-model="userItem.is_deleted"
                        item-text="text"
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

        <v-data-table
          :headers="headers"
          :items="users"
          :pagination.sync="pagination"
          class="elevation-1"
        >
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
      </div>
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
      return this.isUserExist ? "ユーザ編集" : "ユーザ新規作成";
    }
  },
  data() {
    return {
      dialog: false,
      usertypes: [
        { text: "マネージャー", value: 1 },
        { text: "正社員", value: 2 },
        { text: "契約社員", value: 3 },
        { text: "アルバイト", value: 4 },
        { text: "インターン", value: 5 }
      ],
      workingtimetypes: [
        { text: "勤務日数により変動", value: 1 },
        { text: "固定勤務時間", value: 2 }
      ],
      isadmin: [
        { text: "一般ユーザ", value: false },
        { text: "管理者", value: true }
      ],
      isdelete: [
        { text: "active", value: false },
        { text: "not active", value: true }
      ],
      rules: {
        required: value => !!value || "This field is required.",
        naturalNumber: value => value > 0 || "Number only.",
        email: value => {
          const pattern = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
          return pattern.test(value) || "Not an email address."; // .test() はJSの正規表現のパターンマッチングで使用。https://developer.mozilla.org/ja/docs/Web/JavaScript/Reference/Global_Objects/RegExp/test
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
      pagination: { rowsPerPage: -1 },
      errors: [],
      users: [],
      isUserExist: false,
      userItem: null,
      userItemDefault: {
        id: null,
        name: null,
        email: "@e3sys.co.jp",
        usertype_id: 2,
        workingtime_type: 1,
        worktime_day: 8,
        maxworktime_month: 20,
        workingtime_min: 160,
        workingtime_max: 180,
        hiredate: "2019-06-01",
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
    /** 初期化 */
    initialize() {
      this.userItem = Object.assign({}, this.userItemDefault);
    },

    /** ユーザ一覧取得 */
    async fetchUsers() {
      const response = await axios.get(`/api/user/getall`);

      if (response.status !== OK) {
        this.$store.commit("error/setCode", response.status);
        return false;
      }

      this.users = response.data;
    },

    /** ユーザデータ登録 */
    async store() {
      console.log("userItem", this.userItem);
      console.log("rules", this.rules);
      const response = await axios.post(`/api/user/store`, {
        user: this.userItem
      });

      if (response.status !== OK) {
        this.$store.commit("error/setCode", response.status);
        return false;
      }
    },

    /** ユーザデータ編集 */
    async edit() {
      console.log("userItem", this.userItem);
      const response = await axios.post(`/api/user/edit`, {
        user: this.userItem
      });

      if (response.status !== OK) {
        this.$store.commit("error/setCode", response.status);
        return false;
      }
    },

    /** ユーザデータ削除 */
    async delete() {
      console.log("userItem", this.userItem);
      const response = await axios.post(`/api/user/delete`, {
        userId: this.userItem.id
      });

      if (response.status !== OK) {
        this.$store.commit("error/setCode", response.status);
        return false;
      }
    },

    /** 編集画面オープン */
    editItem(item) {
      this.isUserExist = this.users.indexOf(item) > -1 ? true : false;
      this.userItem = Object.assign({}, item);
      this.dialog = true;
    },

    /** ユーザ削除アラート開く */
    deleteItem(item) {
      confirm("Are you sure you want to delete this item?") &&
        this.delete() &&
        this.fetchUsers();
    },

    /** モーダルクローズ */
    close() {
      // モーダルクローズ
      this.dialog = false;
      // モーダルをクローズするタイミングで、デフォルト値に変更される値を確認できないようにするために、少し送らせて値を変更
      setTimeout(() => {
        this.userItem = Object.assign({}, this.userItemDefault);
        this.isUserExist = false;
      }, 300);
    },

    /** データ登録・編集 */
    save() {
      console.log("save");
      if (this.checkValidation()) {
        // データ登録・編集
        this.isUserExist ? this.edit() : this.store();
        // モーダルクローズ
        this.close();
        // ユーザ一覧取得
        this.fetchUsers();
      } else {
        console.log('false');
      }
    },

    /** バリデーション */
    checkValidation() {
      console.log("checkValidation", this.userItem);
      return false;
    }
  },
  watch: {
    $route: {
      async handler() {
        console.log("handler");
        await this.fetchUsers();
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
