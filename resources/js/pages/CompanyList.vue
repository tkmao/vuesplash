<template>
  <div class="company-list">
    <v-app id="inspire">
      <v-container grid-list-md text-xs-left fluid>
        <v-layout row wrap>
          <v-flex xs12>
            <div>
              <v-toolbar flat color="white">
                <v-toolbar-title>企業管理</v-toolbar-title>
                <v-divider class="mx-2" inset vertical></v-divider>
                <v-spacer></v-spacer>
                <v-dialog v-model="dialog" max-width="700px">
                  <template v-slot:activator="{ on }">
                    <v-btn color="primary" dark class="mb-12" v-on="on">新規企業作成</v-btn>
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
                                v-model="companyItem.name"
                                :rules="[rules.required]"
                                type="text"
                                label="企業名*"
                                required
                              ></v-text-field>
                            </v-flex>
                            <v-flex xs5 sm5 md5>
                              <v-text-field
                                v-model="companyItem.zipcode"
                                :rules="[rules.zipcode]"
                                type="text"
                                label="郵便番号"
                                required
                              ></v-text-field>
                            </v-flex>
                            <v-flex xs5 sm5 md5>
                              <v-text-field
                                v-model="companyItem.address"
                                type="text"
                                label="住所"
                                required
                              ></v-text-field>
                            </v-flex>
                            <v-flex xs5 sm5 md5>
                              <v-text-field
                                v-model="companyItem.phone"
                                :rules="[rules.phoneNumber]"
                                type="text"
                                label="電話番号"
                                required
                              ></v-text-field>
                            </v-flex>
                            <v-flex xs5 sm5 md5>
                              <v-text-field
                                v-model="companyItem.fax"
                                :rules="[rules.phoneNumber]"
                                type="text"
                                label="fax番号"
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
                <div
                  v-loading="loadingFlg"
                  element-loading-text="Loading..."
                  element-loading-spinner="loadingSpinner"
                  element-loading-background="loadingBackground"
                >
                  <v-card-title>
                    <v-toolbar dark color="teal">
                      <v-toolbar-title>企業検索</v-toolbar-title>
                      <v-text-field
                        v-model="searchCompany"
                        append-icon="search"
                        label="企業名、住所、電話番号、fax etc.."
                        single-line
                        hide-details
                      ></v-text-field>
                    </v-toolbar>
                  </v-card-title>
                  <v-card-text>
                    <v-data-table
                      :headers="companyHeaders"
                      :items="companies"
                      hide-actions
                      :pagination.sync="pagination"
                      class="elevation-1"
                      :search="searchCompany"
                    >
                      <v-progress-linear v-slot:progress color="blue" indeterminate></v-progress-linear>
                      <template v-slot:items="props">
                        <td width="3%">{{ props.item.id }}</td>
                        <td width="20%">{{ props.item.name }}</td>
                        <td width="10%">{{ props.item.zipcode }}</td>
                        <td width="40%">{{ props.item.address }}</td>
                        <td width="10%">{{ props.item.phone }}</td>
                        <td width="10%">{{ props.item.fax }}</td>
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
                        >"{{ searchCompany }}" と一致するデータは存在していません。</v-alert>
                      </template>
                    </v-data-table>
                  </v-card-text>
                </div>
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
      loadingFlg: false,
      loadingText: "Loading...",
      loadingSpinner: "el-icon-loading",
      loadingBackground: "rgba(255, 255, 255, 0.8)",
      active: null,
      dialog: false,
      isdelete: [{ text: "有効", value: false }, { text: "無効", value: true }],
      rules: {
        required: value => !!value || "必須入力項目です",
        zipcode: value => {
          const pattern = /^\d{3}-?\d{4}$/;
          return value == null
            ? true
            : pattern.test(value) || "郵便番号のフォーマットではありません"; // .test() はJSの正規表現のパターンマッチングで使用。https://developer.mozilla.org/ja/docs/Web/JavaScript/Reference/Global_Objects/RegExp/test
        },
        phoneNumber: value => {
          const pattern = /^(0[5-9]0[0-9]{8}|0[1-9][1-9][0-9]{7})$/;
          const phone = value
            ? value.replace(/[━.*‐.*―.*－.*\-.*ー.*\-]/gi, "")
            : null;
          return value == null
            ? true
            : pattern.test(phone) || "電話番号のフォーマットに合わせてください";
        }
      },
      companyHeaders: [
        { text: "ID", align: "left", value: "id" },
        { text: "企業名", value: "name" },
        { text: "郵便番号", value: "zipcode" },
        { text: "住所", value: "address" },
        { text: "電話番号", value: "phone" },
        { text: "fax", value: "fax" },
        { text: "編集", value: 0, sortable: false },
        { text: "削除", value: 0, sortable: false }
      ],
      searchCompany: "",
      pagination: { rowsPerPage: -1 },
      valid: false,
      companies: [],
      isCompanyExist: false,
      companyItem: {},
      companyItemDefault: {
        id: null,
        name: null,
        zipcode: null,
        address: null,
        phone: null,
        fax: null,
        is_deleted: false
      }
    };
  },
  created() {
    this.initialize();
  },
  computed: {
    formTitle() {
      return this.isCompanyExist ? "企業編集" : "企業新規作成";
    }
  },
  methods: {
    /** 初期化 */
    initialize() {
      this.companyItem = Object.assign({}, this.companyItemDefault);
    },

    /** 企業一覧取得 */
    async fetchCompanies() {
      const response = await axios.get(`/api/company/getall`);

      if (response.status !== OK) {
        this.$store.commit("error/setCode", response.status);
        return false;
      }

      this.companies = response.data;
    },

    /** 企業データ登録 */
    async store() {
      const response = await axios.post(`/api/company/store`, {
        company: this.companyItem
      });

      if (response.status !== OK) {
        this.$store.commit("error/setCode", response.status);
        return false;
      }
    },

    /** 企業データ編集 */
    async edit() {
      const response = await axios.post(`/api/company/edit`, {
        company: this.companyItem
      });

      if (response.status !== OK) {
        this.$store.commit("error/setCode", response.status);
        return false;
      }
    },

    /** 企業データ削除 */
    async delete() {
      const response = await axios.post(`/api/company/delete`, {
        companyId: this.companyItem.id
      });

      if (response.status !== OK) {
        this.$store.commit("error/setCode", response.status);
        return false;
      }
    },

    /** 編集画面オープン */
    editItem(item) {
      this.isCompanyExist = this.companies.indexOf(item) > -1 ? true : false;
      this.companyItem = Object.assign({}, item);
      this.dialog = true;
    },

    /** 企業削除アラート開く */
    async deleteItem(item) {
      if (this.companies.indexOf(item) > -1) {
        this.companyItem = Object.assign({}, item);
        if (confirm("Are you sure you want to delete this item?")) {
          await this.delete();
          this.fetchCompanies();
        }
      }
    },

    /** 企業登録・編集 */
    async save() {
      if (this.$refs.form.validate()) {
        // データ登録・編集
        this.isCompanyExist ? await this.edit() : await this.store();
        // モーダルクローズ
        this.close();
        // 企業一覧取得
        this.fetchCompanies();
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
        this.companyItem = Object.assign({}, this.companyItemDefault);
        this.isCompanyExist = false;
      }, 300);
    }
  },
  watch: {
    $route: {
      async handler() {
        this.loadingFlg = true;
        await this.fetchCompanies();
        this.loadingFlg = false;
      },
      immediate: true
    },

    dialog(val) {
      val || this.close();
    }
  }
};
</script>
