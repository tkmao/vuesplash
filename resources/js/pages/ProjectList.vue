<template>
  <div class="project-list">
    <v-app id="inspire">
      <v-container grid-list-md text-xs-left fluid>
        <v-layout row wrap>
          <v-flex xs12>
            <div>
              <v-toolbar flat color="white">
                <v-toolbar-title>プロジェクト管理</v-toolbar-title>
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
                                v-model="projectItem.code"
                                :rules="[rules.required, rules.code, rules.codeunique, rules.codelength]"
                                type="text"
                                label="コード*"
                                required
                              ></v-text-field>
                            </v-flex>
                            <v-flex xs5 sm5 md5>
                              <v-text-field
                                v-model="projectItem.name"
                                :rules="[rules.required]"
                                type="text"
                                label="プロジェクト名*"
                                required
                              ></v-text-field>
                            </v-flex>
                            <v-flex xs5 sm5 md5>
                              <v-select
                                v-model="projectItem.category_id"
                                :rules="[rules.required]"
                                item-text="name"
                                item-value="id"
                                :items="categories"
                                label="区分*"
                                required
                              ></v-select>
                            </v-flex>
                            <v-flex xs5 sm5 md5>
                              <v-autocomplete
                                v-model="projectItem.company_id"
                                :rules="[rules.required]"
                                :items="companies"
                                item-value="id"
                                item-text="name"
                                cache-items
                                hide-no-data
                                label="取引先企業* (検索可)"
                              ></v-autocomplete>
                            </v-flex>
                            <v-flex xs5 sm5 md5>
                              <v-autocomplete
                                v-model="projectItem.user_id"
                                :rules="[rules.required]"
                                :items="users"
                                item-value="id"
                                item-text="name"
                                cache-items
                                hide-no-data
                                label="担当者* (検索可)"
                              ></v-autocomplete>
                            </v-flex>
                            <v-flex xs5 sm5 md5>
                              <v-select
                                v-model="projectItem.status_id"
                                :rules="[rules.required]"
                                item-text="name"
                                item-value="id"
                                :items="projectStatuses"
                                label="ステータス*"
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
              <v-card flat>
                <div
                  v-loading="loadingFlg"
                  element-loading-text="Loading..."
                  element-loading-spinner="loadingSpinner"
                  element-loading-background="loadingBackground"
                >
                  <v-card-title>
                    <v-toolbar dark color="teal">
                      <v-toolbar-title>プロジェクト検索</v-toolbar-title>
                      <v-text-field
                        v-model="searchProject"
                        append-icon="search"
                        label="プロジェクトコード、プロジェクト名.."
                        single-line
                        hide-details
                      ></v-text-field>
                    </v-toolbar>
                  </v-card-title>
                  <v-card-text>
                    <v-data-table
                      :headers="projectHeaders"
                      :items="projects"
                      hide-actions
                      :pagination.sync="pagination"
                      class="elevation-1"
                      :search="searchProject"
                    >
                      <v-progress-linear v-slot:progress color="blue" indeterminate></v-progress-linear>
                      <template v-slot:items="props">
                        <td width="3%">{{ props.item.id }}</td>
                        <td width="5%">{{ props.item.code }}</td>
                        <td width="35%">{{ props.item.name }}</td>
                        <td
                          width="10%"
                        >{{ categories ? categories.find(x => x.id === props.item.category_id).name : "" }}</td>
                        <td
                          width="15%"
                        >{{ companies ? companies.find(x => x.id === props.item.company_id).name : "" }}</td>
                        <td
                          width="8%"
                        >{{ users ? users.find(x => x.id === props.item.user_id).name : "" }}</td>
                        <td
                          width="8%"
                        >{{ projectStatuses ? projectStatuses.find(x => x.id === props.item.status_id).name : "" }}</td>
                        <td
                          width="5%"
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
                        >"{{ searchProject }}" と一致するデータは存在していません。</v-alert>
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
      rules: {
        required: value => !!value || "必須入力項目です",
        code: value => {
          const pattern = /^[0-9]+$/;
          return pattern.test(value) || "プロジェクトコードは数字のみです"; // .test() はJSの正規表現のパターンマッチングで使用。https://developer.mozilla.org/ja/docs/Web/JavaScript/Reference/Global_Objects/RegExp/test
        },
        codeunique: value => {
          const existProjectCode = this.projects.find(function(element) {
            return element.code === value;
          });
          return !existProjectCode || "プロジェクトコードは使用済みです"; // .test() はJSの正規表現のパターンマッチングで使用。https://developer.mozilla.org/ja/docs/Web/JavaScript/Reference/Global_Objects/RegExp/test
        },
        codelength: value => {
          return (
            (value && value.length === 5) || "プロジェクトコードは5文字です"
          );
        }
      },
      isdelete: [{ text: "有効", value: false }, { text: "無効", value: true }],
      projectHeaders: [
        { text: "ID", align: "left", value: "id" },
        { text: "コード", value: "code" },
        { text: "プロジェクト名", value: "name" },
        { text: "区分", value: "category_id" },
        { text: "取引先企業", value: "company_id" },
        { text: "担当者", value: "user_id" },
        { text: "ステータス", value: "status_id" },
        { text: "状態", value: "is_deleted" },
        { text: "編集", value: 0, sortable: false },
        { text: "削除", value: 0, sortable: false }
      ],
      searchProject: "",
      pagination: { sortBy: "code", rowsPerPage: -1 },
      valid: false,
      users: [],
      companies: [],
      categories: [],
      projectStatuses: [],
      projects: [],
      isProjectExist: false,
      projectItem: {},
      projectItemDefault: {
        id: null,
        code: "00XXX",
        name: null,
        category_id: 1,
        company_id: 1,
        user_id: 1,
        status_id: 1,
        is_deleted: false
      }
    };
  },
  created() {
    this.initialize();
  },
  computed: {
    formTitle() {
      return this.isProjectExist ? "プロジェクト編集" : "プロジェクト作成";
    }
  },
  methods: {
    /** 初期化 */
    initialize() {
      this.projectItem = Object.assign({}, this.projectItemDefault);
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

    /** 企業一覧取得 */
    async fetchCompanies() {
      const response = await axios.get(`/api/company/getall`);

      if (response.status !== OK) {
        this.$store.commit("error/setCode", response.status);
        return false;
      }

      this.companies = response.data;
    },

    /** PJ区分一覧取得 */
    async fetchCategories() {
      const response = await axios.get(`/api/category/getall`);

      if (response.status !== OK) {
        this.$store.commit("error/setCode", response.status);
        return false;
      }

      this.categories = response.data;
    },

    /** PJステータス一覧取得 */
    async fetchProjectStatuses() {
      const response = await axios.get(`/api/projectstatus/getall`);

      if (response.status !== OK) {
        this.$store.commit("error/setCode", response.status);
        return false;
      }

      this.projectStatuses = response.data;
    },

    /** プロジェクト一覧取得 */
    async fetchProjects() {
      const response = await axios.get(`/api/project/getall`);

      if (response.status !== OK) {
        this.$store.commit("error/setCode", response.status);
        return false;
      }

      this.projects = response.data;
    },

    /** プロジェクトデータ登録 */
    async store() {
      const response = await axios.post(`/api/project/store`, {
        project: this.projectItem
      });

      if (response.status !== OK) {
        this.$store.commit("error/setCode", response.status);
        return false;
      }
    },

    /** プロジェクトデータ編集 */
    async edit() {
      const response = await axios.post(`/api/project/edit`, {
        project: this.projectItem
      });

      if (response.status !== OK) {
        this.$store.commit("error/setCode", response.status);
        return false;
      }
    },

    /** プロジェクトデータ削除 */
    async delete() {
      const response = await axios.post(`/api/project/delete`, {
        projectId: this.projectItem.id
      });

      if (response.status !== OK) {
        this.$store.commit("error/setCode", response.status);
        return false;
      }
    },

    /** 編集画面オープン */
    editItem(item) {
      this.isProjectExist = this.projects.indexOf(item) > -1 ? true : false;
      this.projectItem = Object.assign({}, item);
      this.dialog = true;
    },

    /** プロジェクト削除アラート開く */
    async deleteItem(item) {
      if (this.projects.indexOf(item) > -1) {
        this.projectItem = Object.assign({}, item);
        if (confirm("Are you sure you want to delete this item?")) {
          await this.delete();
          this.fetchProjects();
        }
      }
    },

    /** プロジェクト登録・編集 */
    async save() {
      if (this.$refs.form.validate()) {
        // データ登録・編集
        this.isProjectExist ? await this.edit() : await this.store();
        // モーダルクローズ
        this.close();
        // プロジェクト一覧取得
        this.fetchProjects();
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
        this.projectItem = Object.assign({}, this.projectItemDefault);
        this.isProjectExist = false;
      }, 300);
    }
  },
  watch: {
    $route: {
      async handler() {
        this.loadingFlg = true;
        await this.fetchUsers();
        await this.fetchCompanies();
        await this.fetchCategories();
        await this.fetchProjectStatuses();
        await this.fetchProjects();
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
