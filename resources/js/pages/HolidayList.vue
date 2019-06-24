<template>
  <div class="holiday-list">
    <v-app id="inspire">
      <v-container grid-list-md text-xs-left fluid>
        <v-layout row wrap>
          <v-flex xs12>
            <div>
              <v-toolbar flat color="white">
                <v-toolbar-title>休日管理</v-toolbar-title>
                <v-divider class="mx-2" inset vertical></v-divider>
                <v-spacer></v-spacer>
                <v-dialog v-model="dialog" max-width="700px">
                  <template v-slot:activator="{ on }">
                    <v-btn color="primary" dark class="mb-12" v-on="on">新規休日作成</v-btn>
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
                                v-model="holidayItem.date"
                                :rules="[rules.required]"
                                type="date"
                                label="日付*"
                                required
                              ></v-text-field>
                            </v-flex>
                            <v-flex xs5 sm5 md5>
                              <v-text-field
                                v-model="holidayItem.name"
                                :rules="[rules.required]"
                                type="text"
                                label="祝日名*"
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
                    <v-toolbar-title>休日検索</v-toolbar-title>
                    <v-text-field
                      v-model="searchHoliday"
                      append-icon="search"
                      label="休日 etc.."
                      single-line
                      hide-details
                    ></v-text-field>
                  </v-toolbar>
                </v-card-title>
                <v-card-text>
                  <v-data-table
                    :headers="holidayHeaders"
                    :items="holidays"
                    hide-actions
                    :pagination.sync="pagination"
                    class="elevation-1"
                    :search="searchHoliday"
                  >
                    <v-progress-linear v-slot:progress color="blue" indeterminate></v-progress-linear>
                    <template v-slot:items="props">
                      <td width="3%">{{ props.item.id }}</td>
                      <td width="20%">{{ props.item.date }}</td>
                      <td width="30%">{{ props.item.name }}</td>
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
                      >"{{ searchHoliday }}" と一致するデータは存在していません。</v-alert>
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
      holidayHeaders: [
        { text: "ID", align: "left", value: "id" },
        { text: "日付", value: "date" },
        { text: "祝日名", value: "name" },
        { text: "編集", value: 0, sortable: false },
        { text: "削除", value: 0, sortable: false }
      ],
      searchHoliday: "",
      pagination: { sortBy: "date", rowsPerPage: -1 },
      valid: false,
      holidays: [],
      isHolidayExist: false,
      holidayItem: {},
      holidayItemDefault: {
        id: null,
        date: null,
        name: null
      }
    };
  },
  created() {
    this.initialize();
  },
  computed: {
    formTitle() {
      return this.isHolidayExist ? "休日編集" : "休日作成";
    }
  },
  methods: {
    /** 初期化 */
    initialize() {
      this.holidayItem = Object.assign({}, this.holidayItemDefault);
    },

    /** 休日一覧取得 */
    async fetchHolidays() {
      const response = await axios.get(`/api/holiday/getall`);

      if (response.status !== OK) {
        this.$store.commit("error/setCode", response.status);
        return false;
      }

      this.holidays = response.data;
    },

    /** 休日データ登録 */
    async store() {
      const response = await axios.post(`/api/holiday/store`, {
        holiday: this.holidayItem
      });

      if (response.status !== OK) {
        this.$store.commit("error/setCode", response.status);
        return false;
      }
    },

    /** 休日データ編集 */
    async edit() {
      const response = await axios.post(`/api/holiday/edit`, {
        holiday: this.holidayItem
      });

      if (response.status !== OK) {
        this.$store.commit("error/setCode", response.status);
        return false;
      }
    },

    /** 休日データ削除 */
    async delete() {
      const response = await axios.post(`/api/holiday/delete`, {
        holidayId: this.holidayItem.id
      });

      if (response.status !== OK) {
        this.$store.commit("error/setCode", response.status);
        return false;
      }
    },

    /** 編集画面オープン */
    editItem(item) {
      this.isHolidayExist = this.holidays.indexOf(item) > -1 ? true : false;
      this.holidayItem = Object.assign({}, item);
      this.dialog = true;
    },

    /** 休日削除アラート開く */
    async deleteItem(item) {
      if (this.holidays.indexOf(item) > -1) {
        this.holidayItem = Object.assign({}, item);
        if (confirm("Are you sure you want to delete this item?")) {
          await this.delete();
          this.fetchHolidays();
        }
      }
    },

    /** 休日登録・編集 */
    async save() {
      if (this.$refs.form.validate()) {
        // データ登録・編集
        this.isHolidayExist ? await this.edit() : await this.store();
        // モーダルクローズ
        this.close();
        // 休日一覧取得
        this.fetchHolidays();
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
        this.holidayItem = Object.assign({}, this.holidayItemDefault);
        this.isHolidayExist = false;
      }, 300);
    }
  },
  watch: {
    $route: {
      async handler() {
        await this.fetchHolidays();
      },
      immediate: true
    },

    dialog(val) {
      val || this.close();
    }
  }
};
</script>
