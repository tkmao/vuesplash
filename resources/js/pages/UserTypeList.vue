<template>
  <div class="usertype-list">
    <v-app id="inspire">
      <v-container grid-list-md text-xs-left fluid>
        <v-layout row wrap>
          <v-flex xs12>
            <div>
              <v-toolbar flat color="white">
                <v-toolbar-title>ユーザタイプ管理</v-toolbar-title>
                <v-divider class="mx-2" inset vertical></v-divider>
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
                            <v-flex xs5 sm5 md5>
                              <v-text-field
                                v-model="userTypeItem.name"
                                :rules="[rules.required]"
                                type="text"
                                label="ユーザタイプ名*"
                                required
                              ></v-text-field>
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
              <v-card flat>
                <v-card-title>
                  <v-toolbar dark color="teal">
                    <v-toolbar-title>ユーザタイプ検索</v-toolbar-title>
                    <v-text-field
                      v-model="searchUserType"
                      append-icon="search"
                      label="ユーザタイプ etc.."
                      single-line
                      hide-details
                    ></v-text-field>
                  </v-toolbar>
                </v-card-title>
                <v-card-text>
                  <v-data-table
                    :headers="userTypeHeaders"
                    :items="userTypes"
                    hide-actions
                    :pagination.sync="pagination"
                    class="elevation-1"
                    :search="searchUserType"
                  >
                    <v-progress-linear v-slot:progress color="blue" indeterminate></v-progress-linear>
                    <template v-slot:items="props">
                      <td width="3%">{{ props.item.id }}</td>
                      <td width="20%">{{ props.item.name }}</td>
                      <td
                        width="30%"
                      >{{ isdelete.find(x => x.value === props.item.is_deleted).text }}</td>
                      <td class="justify-center">
                        <v-icon small @click="editItem(props.item)">edit</v-icon>
                      </td>
                      <td class="justify-center">
                        <v-icon small @click="deleteItem(props.item)">delete</v-icon>
                      </td>
                    </template>
                    <template v-slot:no-results>
                      <v-alert
                        :value="true"
                        color="error"
                        icon="warning"
                      >"{{ searchUserType }}" と一致するデータは存在していません。</v-alert>
                    </template>
                  </v-data-table>
                </v-card-text>
              </v-card>
            </div>
          </v-flex>
        </v-layout>
      </v-container>
    </v-app>
  </div>
</template>

<script>
import { OK } from "../util";
import Pagination from "../components/Pagination.vue";
import { constants } from "crypto";

export default {
  components: {
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
      active: null,
      dialog: false,
      rules: {
        required: value => !!value || "必須入力項目です"
      },
      isdelete: [{ text: "有効", value: false }, { text: "無効", value: true }],
      userTypeHeaders: [
        { text: "ID", align: "left", value: "id" },
        { text: "ユーザタイプ名", value: "name" },
        { text: "状態", value: "is_deleted" },
        { text: "編集", value: 0, sortable: false },
        { text: "削除", value: 0, sortable: false }
      ],
      searchUserType: "",
      pagination: { rowsPerPage: -1 },
      valid: false,
      userTypes: [],
      isUserTypeExist: false,
      userTypeItem: {},
      userTypeItemDefault: {
        id: null,
        name: null,
        is_deleted: null
      }
    };
  },
  created() {
    this.initialize();
  },
  computed: {
    formTitle() {
      return this.isUserTypeExist ? "ユーザタイプ編集" : "ユーザタイプ作成";
    }
  },
  methods: {
    /** 初期化 */
    initialize() {
      this.userTypeItem = Object.assign({}, this.userTypeItemDefault);
    },

    /** ユーザタイプ一覧取得 */
    async fetchUserTypes() {
      const response = await axios.get(`/api/usertype/getall`);

      if (response.status !== OK) {
        this.$store.commit("error/setCode", response.status);
        return false;
      }

      this.userTypes = response.data;
    },

    /** ユーザタイプデータ登録 */
    async store() {
      const response = await axios.post(`/api/usertype/store`, {
        userType: this.userTypeItem
      });

      if (response.status !== OK) {
        this.$store.commit("error/setCode", response.status);
        return false;
      }
    },

    /** ユーザタイプデータ編集 */
    async edit() {
      const response = await axios.post(`/api/usertype/edit`, {
        userType: this.userTypeItem
      });

      if (response.status !== OK) {
        this.$store.commit("error/setCode", response.status);
        return false;
      }
    },

    /** ユーザタイプデータ削除 */
    async delete() {
      const response = await axios.post(`/api/usertype/delete`, {
        userTypeId: this.userTypeItem.id
      });

      if (response.status !== OK) {
        this.$store.commit("error/setCode", response.status);
        return false;
      }
    },

    /** 編集画面オープン */
    editItem(item) {
      this.isUserTypeExist = this.userTypes.indexOf(item) > -1 ? true : false;
      this.userTypeItem = Object.assign({}, item);
      this.dialog = true;
    },

    /** ユーザタイプ削除アラート開く */
    async deleteItem(item) {
      if (this.userTypes.indexOf(item) > -1) {
        this.userTypeItem = Object.assign({}, item);
        if (confirm("Are you sure you want to delete this item?")) {
          await this.delete();
          this.fetchUserTypes();
        }
      }
    },

    /** ユーザタイプ登録・編集 */
    async save() {
      if (this.$refs.form.validate()) {
        // データ登録・編集
        this.isUserTypeExist ? await this.edit() : await this.store();
        // モーダルクローズ
        this.close();
        // ユーザタイプ一覧取得
        this.fetchUserTypes();
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
        this.userTypeItem = Object.assign({}, this.userTypeItemDefault);
        this.isUserTypeExist = false;
      }, 300);
    }
  },
  watch: {
    $route: {
      async handler() {
        await this.fetchUserTypes();
      },
      immediate: true
    },

    dialog(val) {
      val || this.close();
    }
  }
};
</script>
