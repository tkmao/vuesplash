<template>
  <div class="user-list">
    <v-app id="inspire">
      <v-container grid-list-md text-xs-left fluid>
        <v-layout row wrap>
          <v-flex xs12>
            <div>
              <v-tabs v-model="active" color="cyan" dark slider-color="yellow">
                <v-tab v-for="tab in tabs" :key="tab">{{ tab }}</v-tab>
                <v-tab-item>
                  <v-card flat>
                    <v-toolbar flat color="white">
                      <v-toolbar-title>社員一覧</v-toolbar-title>
                      <v-divider class="mx-2" inset vertical></v-divider>
                      <!-- v-spacer  -->
                      <v-spacer></v-spacer>
                      <v-dialog v-model="dialog" max-width="700px">
                        <template v-slot:activator="{ on }">
                          <v-btn color="primary" dark class="mb-12" v-on="on">新規ユーザ作成</v-btn>
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
                            <v-btn
                              :class="{ red: !valid, green: valid }"
                              color="success"
                              @click="save"
                            >Save</v-btn>
                          </v-card-actions>
                        </v-card>
                      </v-dialog>
                    </v-toolbar>
                    <v-card-text>
                      <v-data-table
                        :headers="allUserHeaders"
                        :items="users"
                        :pagination.sync="pagination"
                        class="elevation-1"
                      >
                        <template v-slot:items="props">
                          <td class="text-xs-right">{{ props.item.id }}</td>
                          <td>{{ props.item.name }}</td>
                          <td>{{ props.item.email }}</td>
                          <td>{{ usertype(props.item.id) }}</td>
                          <td>{{ workingtimeType(props.item.id) }}</td>
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
                    </v-card-text>
                  </v-card>
                </v-tab-item>
                <v-tab-item>
                  <v-toolbar flat color="white">
                    <v-toolbar-title>社員契約管理</v-toolbar-title>
                    <v-divider class="mx-2" inset vertical></v-divider>
                    <!-- v-spacer  -->
                    <v-spacer></v-spacer>
                    <v-card-title>
                      社員選択 :
                      <v-autocomplete
                        v-model="userContractItem.user_id"
                        :loading="loadingUser"
                        :items="users"
                        item-value="id"
                        item-text="name"
                        :search-input.sync="searchUser"
                        @change="changeUser()"
                        cache-items
                        class="mx-3"
                        flat
                        hide-no-data
                        hide-details
                        label="ユーザ名"
                        solo-inverted
                      ></v-autocomplete>
                    </v-card-title>
                    <v-dialog v-model="dialogContract" max-width="700px">
                      <template v-slot:activator="{ on }">
                        <v-btn color="primary" dark class="mb-12" v-on="on">新規契約作成</v-btn>
                      </template>
                      <v-card>
                        <v-card-title>
                          <span class="headline">{{ formTitle }}</span>
                        </v-card-title>

                        <v-card-text>
                          <v-container grid-list-md>
                            <v-form v-model="valid" ref="form">
                              <v-layout wrap>
                                <v-flex xs5 sm5 md5>
                                  <v-select
                                    v-model="userContractItem.usertype_id"
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
                                    v-model="userContractItem.workingtime_type"
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
                                    v-model="userContractItem.worktime_day"
                                    :rules="[rules.required, rules.naturalNumber]"
                                    type="Number"
                                    step="1"
                                    min="0"
                                    label="一日の勤務時間*"
                                  ></v-text-field>
                                </v-flex>
                                <v-flex xs5 sm5 md5>
                                  <v-text-field
                                    v-model="userContractItem.maxworktime_month"
                                    :rules="[rules.required, rules.naturalNumber]"
                                    type="Number"
                                    min="0"
                                    step="1"
                                    label="月の上限勤務時間*"
                                  ></v-text-field>
                                </v-flex>
                                <v-flex xs5 sm5 md5>
                                  <v-text-field
                                    v-model="userContractItem.workingtime_min"
                                    :rules="[rules.required, rules.naturalNumber]"
                                    type="Number"
                                    min="0"
                                    step="1"
                                    label="勤務時間下限*"
                                  ></v-text-field>
                                </v-flex>
                                <v-flex xs5 sm5 md5>
                                  <v-text-field
                                    v-model="userContractItem.workingtime_max"
                                    :rules="[rules.required, rules.naturalNumber]"
                                    type="Number"
                                    min="0"
                                    step="1"
                                    label="勤務時間上限*"
                                  ></v-text-field>
                                </v-flex>
                                <v-flex xs5 sm5 md5>
                                  <v-text-field
                                    v-model="userContractItem.startdate"
                                    :rules="[rules.required]"
                                    type="date"
                                    min="0"
                                    step="1"
                                    label="契約有効開始日*"
                                  ></v-text-field>
                                </v-flex>
                                <v-flex xs5 sm5 md5>
                                  <v-text-field
                                    v-model="userContractItem.enddate"
                                    :rules="[rules.required, rules.contract]"
                                    type="date"
                                    min="0"
                                    step="1"
                                    label="契約有効終了日*"
                                  ></v-text-field>
                                </v-flex>
                              </v-layout>
                            </v-form>
                          </v-container>
                          <small>*indicates required field</small>
                        </v-card-text>
                        <v-card-actions>
                          <v-spacer></v-spacer>
                          <v-btn @click="closeContract">Cancel</v-btn>
                          <v-btn @click="clear">Clear</v-btn>
                          <v-btn
                            :class="{ red: !valid, green: valid }"
                            color="success"
                            @click="saveContract"
                          >Save</v-btn>
                        </v-card-actions>
                      </v-card>
                    </v-dialog>
                  </v-toolbar>
                  <v-card-text>
                    社員名 : {{ this.user.name }}
                    <br>
                    メールアドレス : {{ this.user.email }}
                    <br>
                    入社日 : {{ this.user.hiredate }}
                    <br>
                    有給日数 : {{ this.user.paid_holiday }} 日
                    <br>
                    <!--
                    権限 : {{ isadmin.find(x => x.value === this.user.is_admin).text }}
                    <br>
                    状態 : {{ isdelete.find(x => x.value === this.user.is_deleted).text }}
                    -->
                  </v-card-text>
                  <v-card-text>
                    <v-data-table
                      :headers="userHeaders"
                      :items="user.user_contract"
                      :pagination.sync="pagination"
                      class="elevation-1"
                    >
                      <template v-slot:items="props">
                        <td class="text-xs-right">{{ props.item.startdate }}</td>
                        <td class="text-xs-right">{{ props.item.enddate }}</td>
                        <td class="text-xs-right">{{ props.item.user_type.name }}</td>
                        <td>{{ workingtimeTypeSummary(props.item) }}</td>
                        <td class="justify-center">
                          <!-- 編集ボタン -->
                          <v-icon small @click="editContractItem(props.item)">edit</v-icon>
                        </td>
                        <td class="justify-center">
                          <!-- 削除ボタン -->
                          <v-icon small @click="deleteContractItem(props.item)">delete</v-icon>
                        </td>
                      </template>
                    </v-data-table>
                  </v-card-text>
                </v-tab-item>
              </v-tabs>
            </div>
          </v-flex>
        </v-layout>
      </v-container>
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
  data() {
    return {
      tabs: ["社員一覧", "社員契約管理"],
      active: null,
      dialog: false,
      dialogContract: false,
      searchUser: "",
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
        { text: "一般", value: false },
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
        contract: value =>
          value > this.userContractItem.startdate ||
          "契約終了日は契約開始日よりも未来にして下さい",
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
      allUserHeaders: [
        { text: "ID", align: "left", value: "id" },
        { text: "名前", value: "name" },
        { text: "email", value: "email" },
        { text: "役職", value: "usertype_id" },
        { text: "勤務形態・勤務時間", sortable: false },
        { text: "入社日", value: "hiredate" },
        { text: "有給日数", value: "paid_holiday" },
        { text: "権限", value: "is_admin" },
        { text: "状態", value: "is_deleted" },
        { text: "編集", value: 0, sortable: false },
        { text: "削除", value: 0, sortable: false }
      ],
      userHeaders: [
        { text: "契約有効開始日", value: "startdate" },
        { text: "契約有効終了日", value: "enddate" },
        { text: "役職", value: "usertype_id" },
        { text: "勤務形態", value: "workingtime_type" },
        { text: "編集", value: 0, sortable: false },
        { text: "削除", value: 0, sortable: false }
      ],
      pagination: { rowsPerPage: -1 },
      valid: false,
      users: [],
      user: [
        {
          id: null,
          name: "",
          email: "",
          paid_holiday: 0,
          hiredate: "2015-09-01",
          is_admin: false,
          is_deleted: false,
          user_contract: [
            {
              id: 4,
              user_id: 2,
              usertype_id: 5,
              workingtime_type: 1,
              worktime_day: 8,
              maxworktime_month: 20,
              workingtime_min: 162,
              workingtime_max: 190,
              startdate: "2015-09-01",
              enddate: "2019-02-28",
              user_type: {
                id: 5,
                name: "インターン",
                is_deleted: 0
              }
            }
          ]
        }
      ],
      loadingUser: false,
      isUserExist: false,
      userItem: null,
      userContractItem: null,
      userItemDefault: {
        id: null,
        name: null,
        email: "@e3sys.co.jp",
        password: null,
        password_confirmation: null,
        hiredate: "2019-06-01",
        paid_holiday: 10,
        is_admin: false,
        is_deleted: false,
        user_contract: [
          {
            user_id: 1,
            usertype_id: 2,
            workingtime_type: 1,
            worktime_day: 8,
            maxworktime_month: 20,
            workingtime_min: 160,
            workingtime_max: 180,
            startdate: "2015-09-01",
            enddate: "2030-12-31",
            user_type: {
              id: 2,
              name: "正社員",
              is_deleted: 0
            }
          }
        ]
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
  computed: {
    formTitle() {
      return this.isUserExist ? "ユーザ編集" : "ユーザ新規作成";
    }
  },
  methods: {
    /** 初期化 */
    initialize() {
      this.userItemDefault.hiredate = moment().format("YYYY-MM-DD");
      this.userItem = Object.assign({}, this.userItemDefault);
      this.userContractItem = Object.assign(
        {},
        this.userItemDefault.user_contract[0]
      );
    },

    /** ユーザ契約情報 */
    userContract(targetDate, userId) {
      return this.users
        .find(function(element) {
          return element.id === userId;
        })
        .user_contract.find(function(element) {
          return (
            element.startdate <= targetDate && targetDate <= element.enddate
          );
        });
    },

    /** ユーザタイプ */
    usertype(userId) {
      return this.userContract(this.userItemDefault.hiredate, userId).user_type
        .name;
    },

    /** 勤務形態・勤務時間 */
    workingtimeType(userId) {
      return this.workingtimeTypeSummary(
        this.userContract(this.userItemDefault.hiredate, userId)
      );
    },

    /** 勤務形態・勤務時間 */
    workingtimeTypeSummary(userContract) {
      if (userContract.workingtime_type === 1) {
        return (
          "勤務日数により変動 :" +
          "1日" +
          userContract.worktime_day +
          " h、月の上限勤務時間: + " +
          userContract.maxworktime_month +
          " h"
        );
      } else if (userContract.workingtime_type === 2) {
        return (
          "固定勤務時間 :" +
          userContract.workingtime_min +
          " h 〜 " +
          userContract.workingtime_max +
          " h"
        );
      }
    },

    /** ユーザデータ変更 */
    changeUser() {
      const userId = this.userContractItem.user_id;
      this.user = this.users.find(function(element) {
        return element.id === userId;
      });
    },

    /** ユーザ一覧取得 */
    async fetchUsers() {
      console.log("fetchUsers");
      const response = await axios.get(`/api/user/getall`);

      if (response.status !== OK) {
        this.$store.commit("error/setCode", response.status);
        return false;
      }

      this.users = response.data;
      this.changeUser();
    },

    /** ユーザデータ登録 */
    async store() {
      console.log("userItem", this.userItem);
      const response = await axios.post(`/api/user/store`, {
        user: this.userItem
      });

      if (response.status !== OK) {
        this.$store.commit("error/setCode", response.status);
        return false;
      }
    },

    /** ユーザ契約データ登録 */
    async storeContract() {
      console.log("storeContract userContractItem", this.userContractItem);
      const response = await axios.post(`/api/usercontract/store`, {
        userContract: this.userContractItem
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

    /** ユーザ契約データ編集 */
    async editContract() {
      const response = await axios.post(`/api/usercontract/edit`, {
        userContract: this.userContractItem
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

    /** ユーザ契約データ削除 */
    async deleteContract() {
      const response = await axios.post(`/api/usercontract/delete`, {
        userContractId: this.userContractItem.id
      });

      if (response.status !== OK) {
        this.$store.commit("error/setCode", response.status);
        return false;
      }
    },

    /** 編集画面オープン */
    editItem(item) {
      console.log("editItem", item);
      this.isUserExist = this.users.indexOf(item) > -1 ? true : false;
      this.userItem = Object.assign({}, item);
      this.dialog = true;
    },

    /** 編集契約画面オープン */
    editContractItem(itemContract) {
      this.isUserExist =
        this.user.user_contract.indexOf(itemContract) > -1 ? true : false;
      this.userContractItem = Object.assign({}, itemContract);
      this.dialogContract = true;
    },

    /** ユーザ削除アラート開く */
    async deleteItem(item) {
      if (this.users.indexOf(item) > -1) {
        this.userItem = Object.assign({}, item);
        if (confirm("Are you sure you want to delete this item?")) {
          await this.delete();
          this.fetchUsers();
        }
      }
    },

    /** ユーザ契約削除アラート開く */
    async deleteContractItem(itemContract) {
      if (this.user.user_contract.indexOf(itemContract) > -1) {
        this.userContractItem = Object.assign({}, itemContract);
        if (confirm("Are you sure you want to delete this item?")) {
          await this.deleteContract();
          this.fetchUsers();
        }
      }
    },

    /** ユーザ登録・編集 */
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

    /** ユーザ契約登録・編集 */
    async saveContract() {
      if (this.$refs.form.validate()) {
        // ユーザ契約登録・編集
        this.isUserExist
          ? await this.editContract()
          : await this.storeContract();
        // モーダルクローズ
        this.closeContract();
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
        this.userContractItem = Object.assign(
          {},
          this.userItemDefault.user_contract[0]
        );
        this.isUserExist = false;
      }, 300);
    },

    /** モーダルクローズ */
    closeContract() {
      // モーダルクローズ
      this.dialogContract = false;
      // モーダルをクローズするタイミングで、デフォルト値に変更される値を確認できないようにするために、少し送らせて値を変更
      setTimeout(() => {
        this.userContractItem = Object.assign(
          {},
          this.userItemDefault.user_contract[0]
        );
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
    },

    dialogContract(val) {
      val || this.closeContract();
    }
  }
};
</script>
