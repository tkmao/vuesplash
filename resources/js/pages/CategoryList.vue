<template>
  <div class="category-list">
    <v-app id="inspire">
      <v-container grid-list-md text-xs-left fluid>
        <v-layout row wrap>
          <v-flex xs12>
            <div>
              <v-toolbar flat color="white">
                <v-toolbar-title>PJ区分管理</v-toolbar-title>
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
                                v-model="categoryItem.name"
                                :rules="[rules.required]"
                                type="text"
                                label="PJ区分名*"
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
                    <v-toolbar-title>PJ区分検索</v-toolbar-title>
                    <v-text-field
                      v-model="searchCategory"
                      append-icon="search"
                      label="PJ区分名 etc.."
                      single-line
                      hide-details
                    ></v-text-field>
                  </v-toolbar>
                </v-card-title>
                <v-card-text>
                  <v-data-table
                    :headers="categoryHeaders"
                    :items="categories"
                    hide-actions
                    :pagination.sync="pagination"
                    class="elevation-1"
                    :search="searchCategory"
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
                      >"{{ searchCategory }}" と一致するデータは存在していません。</v-alert>
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
      categoryHeaders: [
        { text: "ID", align: "left", value: "id" },
        { text: "ユーザタイプ名", value: "name" },
        { text: "状態", value: "is_deleted" },
        { text: "編集", value: 0, sortable: false },
        { text: "削除", value: 0, sortable: false }
      ],
      searchCategory: "",
      pagination: { sortBy: "date", rowsPerPage: -1 },
      valid: false,
      categories: [],
      isCategoryExist: false,
      categoryItem: {},
      categoryItemDefault: {
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
      return this.isCategoryExist ? "PJ区分編集" : "PJ区分作成";
    }
  },
  methods: {
    /** 初期化 */
    initialize() {
      this.categoryItem = Object.assign({}, this.categoryItemDefault);
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

    /** PJ区分データ登録 */
    async store() {
      const response = await axios.post(`/api/category/store`, {
        category: this.categoryItem
      });

      if (response.status !== OK) {
        this.$store.commit("error/setCode", response.status);
        return false;
      }
    },

    /** PJ区分データ編集 */
    async edit() {
      const response = await axios.post(`/api/category/edit`, {
        category: this.categoryItem
      });

      if (response.status !== OK) {
        this.$store.commit("error/setCode", response.status);
        return false;
      }
    },

    /** PJ区分データ削除 */
    async delete() {
      const response = await axios.post(`/api/category/delete`, {
        categoryId: this.categoryItem.id
      });

      if (response.status !== OK) {
        this.$store.commit("error/setCode", response.status);
        return false;
      }
    },

    /** 編集画面オープン */
    editItem(item) {
      this.isCategoryExist = this.categories.indexOf(item) > -1 ? true : false;
      this.categoryItem = Object.assign({}, item);
      this.dialog = true;
    },

    /** PJ区分削除アラート開く */
    async deleteItem(item) {
      if (this.categories.indexOf(item) > -1) {
        this.categoryItem = Object.assign({}, item);
        if (confirm("Are you sure you want to delete this item?")) {
          await this.delete();
          this.fetchCategories();
        }
      }
    },

    /** PJ区分登録・編集 */
    async save() {
      if (this.$refs.form.validate()) {
        // データ登録・編集
        this.isCategoryExist ? await this.edit() : await this.store();
        // モーダルクローズ
        this.close();
        // PJ区分一覧取得
        this.fetchCategories();
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
        this.categoryItem = Object.assign({}, this.categoryItemDefault);
        this.isCategoryExist = false;
      }, 300);
    }
  },
  watch: {
    $route: {
      async handler() {
        await this.fetchCategories();
      },
      immediate: true
    },

    dialog(val) {
      val || this.close();
    }
  }
};
</script>
