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
                  <v-form v-model="valid" ref="form">
                    <v-layout wrap>
                      <v-flex xs8 sm8 md8>
                        <v-text-field
                          prepend-icon="account_box"
                          v-model="userItem.name"
                          label="名前*"
                          :rules="[rules.required]"
                          clearable
                          required
                        ></v-text-field>
                      </v-flex>
                      <v-flex xs8 sm8 md8>
                        <v-text-field
                          prepend-icon="mail"
                          v-model="userItem.email"
                          label="email*"
                          :rules="[rules.required, rules.email]"
                          clearable
                          required
                        ></v-text-field>
                      </v-flex>

                      <v-flex xs5 sm5 md5>
                        <div v-if="!isUserExist">
                          <v-text-field
                            prepend-icon="lock"
                            label="Password"
                            type="password"
                            v-model="userItem.password"
                            :rules="rules.passwordRules"
                            required
                          ></v-text-field>
                        </div>
                      </v-flex>
                      <v-flex xs5 sm5 md5>
                        <div v-if="!isUserExist">
                          <v-text-field
                            prepend-icon="lock"
                            label="Password Confirmation"
                            type="password"
                            v-model="userItem.password_confirmation"
                            :rules="rules.passwordConfirmRules"
                            required
                          ></v-text-field>
                        </div>
                      </v-flex>

                      <v-flex xs5 sm5 md5>
                        <v-select
                          v-model="userItem.usertype_id"
                          :rules="[rules.required]"
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
                          :rules="[rules.required]"
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
                          :rules="[rules.required, rules.naturalNumber]"
                          type="Number"
                          step="1"
                          min="0"
                          label="一日の勤務時間*"
                        ></v-text-field>
                      </v-flex>
                      <v-flex xs5 sm5 md5>
                        <v-text-field
                          v-model="userItem.maxworktime_month"
                          :rules="[rules.required, rules.naturalNumber]"
                          type="Number"
                          min="0"
                          step="1"
                          label="月の上限勤務時間*"
                        ></v-text-field>
                      </v-flex>
                      <v-flex xs5 sm5 md5>
                        <v-text-field
                          v-model="userItem.workingtime_min"
                          :rules="[rules.required, rules.naturalNumber]"
                          type="Number"
                          min="0"
                          step="1"
                          label="勤務時間下限*"
                        ></v-text-field>
                      </v-flex>
                      <v-flex xs5 sm5 md5>
                        <v-text-field
                          v-model="userItem.workingtime_max"
                          :rules="[rules.required, rules.naturalNumber]"
                          type="Number"
                          min="0"
                          step="1"
                          label="勤務時間上限*"
                        ></v-text-field>
                      </v-flex>
                      <v-flex xs5 sm5 md5>
                        <v-text-field
                          v-model="userItem.hiredate"
                          :rules="[rules.required]"
                          type="date"
                          label="入社日*"
                        ></v-text-field>
                      </v-flex>
                      <v-flex xs5 sm5 md5>
                        <v-text-field
                          v-model="userItem.paid_holiday"
                          :rules="[rules.required, rules.naturalNumber]"
                          type="Number"
                          min="0"
                          step="1"
                          label="有給日数*"
                        ></v-text-field>
                      </v-flex>
                      <v-flex xs5 sm5 md5>
                        <v-select
                          v-model="userItem.is_admin"
                          :rules="[rules.requiredBoolean]"
                          item-text="text"
                          item-value="value"
                          :items="isadmin"
                          label="管理者権限*"
                          required
                        ></v-select>
                      </v-flex>
                      <v-flex xs5 sm5 md5>
                        <v-select
                          v-model="userItem.is_deleted"
                          :rules="[rules.requiredBoolean]"
                          item-text="text"
                          item-value="value"
                          :items="isdelete"
                          label="ユーザ状態*"
                          required
                        ></v-select>
                      </v-flex>
                    </v-layout>
                  </v-form>
                </v-container>
                <small>*indicates required field</small>
              </v-card-text>
              <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn @click="close">Cancel</v-btn>
                <v-btn @click="clear">Clear</v-btn>
                <v-btn :class="{ red: !valid, green: valid }" color="success" @click="save">Save</v-btn>
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
            <td>{{ props.item.email }}</td>
            <td>{{ usertypes.find(x => x.value === props.item.usertype_id).text }}</td>
            <td>{{ workingtimetypes.find(x => x.value === props.item.workingtime_type).text }}</td>
            <td class="text-xs-right">{{ props.item.worktime_day }} h</td>
            <td class="text-xs-right">{{ props.item.maxworktime_month }} h</td>
            <td
              class="text-xs-right"
            >{{ props.item.workingtime_min }} h 〜 {{ props.item.workingtime_max }} h</td>
            <td class="text-xs-right">{{ props.item.hiredate }}</td>
            <td class="text-xs-right">{{ props.item.paid_holiday }} 日</td>
            <td>{{ isadmin.find(x => x.value === props.item.is_admin).text }}</td>
            <td>{{ isdelete.find(x => x.value === props.item.is_deleted).text }}</td>
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
      isdelete: [{ text: "有効", value: false }, { text: "無効", value: true }],
      rules: {
        required: value => !!value || "This field is required.", //
        requiredBoolean: value =>
          typeof value !== "undefined" || "This field is required.", //
        naturalNumber: value =>
          (value > 0 && Number.isInteger(parseFloat(value))) ||
          "Natural Number only.",
        email: value => {
          const pattern = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
          return pattern.test(value) || "Not an email address."; // .test() はJSの正規表現のパターンマッチングで使用。https://developer.mozilla.org/ja/docs/Web/JavaScript/Reference/Global_Objects/RegExp/test
        },
        passwordRules: [
          value => !!value || "Password is required",
          value =>
            (value && value.length >= 6) ||
            "Password must be more than 6 characters"
        ],
        passwordConfirmRules: [
          value => !!value || "Password Confirm is required",
          value =>
            value === this.userItem.password ||
            "Password confirm is equal to password"
        ]
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
        { text: "勤務時間下限上限", sortable: false },
        { text: "入社日", value: "hiredate" },
        { text: "有給日数", value: "paid_holiday" },
        { text: "管理者権限", value: "is_admin" },
        { text: "ユーザ状態", value: "is_deleted" },
        { text: "編集", value: 0, sortable: false },
        { text: "削除", value: 0, sortable: false }
      ],
      pagination: { rowsPerPage: -1 },
      valid: false,
      users: [],
      isUserExist: false,
      userItem: null,
      userItemDefault: {
        id: null,
        name: null,
        email: "@e3sys.co.jp",
        password: null,
        password_confirmation: null,
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
      this.userItemDefault.hiredate = moment().format("YYYY-MM-DD");
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
    async deleteItem(item) {
      if (this.users.indexOf(item) > -1) {
        this.userItem = Object.assign({}, item);
        confirm("Are you sure you want to delete this item?") &&
          (await this.delete()) &&
          (await this.fetchUsers());
      }
    },

    /** データ登録・編集 */
    async save() {
      if (this.$refs.form.validate()) {
        // データ登録・編集
        this.isUserExist ? await this.edit() : await this.store();
        // モーダルクローズ
        this.close();
        // ユーザ一覧取得
        this.fetchUsers();
      }
    },

    /** 入力データクリア */
    clear() {
      this.$refs.form.reset();
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
    }
  },
  watch: {
    $route: {
      async handler() {
        await this.fetchUsers();
      },
      immediate: true
    },

    dialog(val) {
      val || this.close();
    }
  }
};
</script>
