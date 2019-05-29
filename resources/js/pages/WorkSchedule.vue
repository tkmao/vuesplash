<style scoped>
.holiday {
  background: lightgray;
}
</style>


<template>
  <div class="work-schedule">
    <v-app id="inspire">
      <div>
        <v-toolbar flat color="white">
          <v-toolbar-title>勤務表 ({{ this.targetDate.format("YYYY") }}年{{ this.targetDate.format("MM") }}月)</v-toolbar-title>
          <v-divider class="mx-2" inset vertical></v-divider>
          <v-spacer></v-spacer>
        </v-toolbar>

        <p>総勤務時間：{{ this.worktimeSum }} 時間</p>

        <v-btn color="info" @click="changeMonth(-1)">先月</v-btn>
        <v-btn color="info" @click="changeMonth(1)">次月</v-btn>
        <br>
        <v-btn color="success" @click="addProject()">プロジェクト追加</v-btn>
        <div v-for="(projectWorktime, index) in projectWorktimes[0]" :key="projectWorktime.key">
          <p>プロジェクト{{ index + 1 }}</p>
          <v-select
            v-on="changeSelected(index)"
            :items="projects"
            item-value="id"
            item-text="name"
            label="プロジェクト"
            box
            return-object
          ></v-select>
        </div>

        <v-btn color="success" @click="store()">勤務表登録</v-btn>
        <v-data-table
          :headers="this.tableheaders"
          :items="workschedules"
          :rows-per-page-items="[]"
          :pagination.sync="pagination"
          class="elevation-1"
        >
          <template v-slot:items="props">
            <td
              width="5%"
              v-bind:class="{ holiday: holiday(props.item.workdate) }"
            >{{ dateformat(props.item.workdate) }}</td>
            <td width="3%" v-bind:class="{ holiday: holiday(props.item.workdate) }">
              <v-checkbox v-model="props.item.is_paid_holiday"></v-checkbox>
            </td>
            <td width="10%" v-bind:class="{ holiday: holiday(props.item.workdate) }">
              <div style="display:flex;">
                <v-text-field v-model="props.item.starttime_hh" type="Number" min="0" max="30"></v-text-field>:
                <v-text-field
                  v-model="props.item.starttime_mm"
                  type="Number"
                  min="0"
                  max="45"
                  step="15"
                ></v-text-field>
              </div>
            </td>
            <td width="10%" v-bind:class="{ holiday: holiday(props.item.workdate) }">
              <div style="display:flex;">
                <v-text-field v-model="props.item.endtime_hh" type="Number" min="0" max="30"></v-text-field>:
                <v-text-field
                  v-model="props.item.endtime_mm"
                  type="Number"
                  min="0"
                  max="45"
                  step="15"
                ></v-text-field>
              </div>
            </td>
            <td width="7%" v-bind:class="{ holiday: holiday(props.item.workdate) }">
              <v-text-field v-model="props.item.breaktime" type="Number" min="0" step="0.25"></v-text-field>
            </td>
            <td width="7%" v-bind:class="{ holiday: holiday(props.item.workdate) }">
              <v-text-field
                v-model="props.item.breaktime_midnight"
                type="Number"
                min="0"
                step="0.25"
              ></v-text-field>
            </td>
            <td
              width="7%"
              v-bind:class="{ holiday: holiday(props.item.workdate) }"
            >{{ worktime_day(props.index) }}</td>
            <td
              width="7%"
              v-bind:class="{ holiday: holiday(props.item.workdate) }"
            >{{ pj_worktime_day(props.index) }}</td>
            <td
              width="7%"
              v-bind:class="{ holiday: holiday(props.item.workdate) }"
              v-for="projectWorktime in projectWorktimes[props.index]"
              :key="projectWorktime.key"
            >
              <v-text-field v-model="projectWorktime.worktime" type="Number" min="0" step="0.25"></v-text-field>
            </td>
            <td width="30%" v-bind:class="{ holiday: holiday(props.item.workdate) }">
              <v-textarea solo rows="2" v-model="props.item.detail" name="detail" label="詳細" value></v-textarea>
            </td>
          </template>
        </v-data-table>
      </div>
    </v-app>
  </div>
</template>

<script>
import { OK } from "../util";
import { getWorktime } from "../util";
import { constants } from "crypto";

export default {
  components: {},
  props: {
    page: {
      type: Number,
      required: false,
      default: 1
    }
  },
  created() {
    this.initialize();
  },
  data() {
    return {
      targetDate: 0,
      tableheaders: [],
      projects: [],
      holidays: [],
      workschedules: [],
      worktimes: [0],
      projectWorktimes: [],
      worktimeSum: 0,
      selected: [],
      pagination: { rowsPerPage: 200 },
      rules: {
        required: value => !!value || "This field is required."
      }
    };
  },
  computed: {
    formTitle() {
      return 1;
    }
  },
  methods: {
    /** 初期処理 */
    initialize() {
      this.targetDate = moment();
    },

    /** 日付変換 */
    dateformat(date) {
      return moment(date).format("DD(ddd)");
    },

    /** 休日チェック */
    holiday(date) {
      if (moment(date).day() % 6 === 0 || this.holidays[date]) {
        return true;
      }
      return false;
    },

    /** 月次移動 */
    changeMonth: function(index) {
      this.targetDate = this.targetDate.add("months", index);
      this.fetchWorkSchedules();
    },

    /** 休日データ取得 */
    async fetchHolidays() {
      const response = await axios.get(`/api/holiday/getall`);

      if (response.status !== OK) {
        this.$store.commit("error/setCode", response.status);
        return false;
      }

      // 休日データ
      let holidays = [];

      response.data.forEach((val_1, idx_1, arr_1) => {
        holidays[arr_1[idx_1].date] = [arr_1[idx_1].name];
      });

      this.holidays = holidays;
    },

    /** プロジェクトデータ取得 */
    async fetchProjects() {
      const response = await axios.get(`/api/project/getall`);

      if (response.status !== OK) {
        this.$store.commit("error/setCode", response.status);
        return false;
      }

      // プロジェクトデータ
      let projects = [];

      response.data.forEach((val_1, idx_1, arr_1) => {
        projects[idx_1] = {
          id: arr_1[idx_1].id,
          code: arr_1[idx_1].code,
          name: arr_1[idx_1].code + " : " + arr_1[idx_1].name
        };
      });

      this.projects = projects;
    },

    /** 勤務表データ取得 */
    async fetchWorkSchedules() {
      const response = await axios.post(`/api/workschedule/get`, {
        yearmonth: this.targetDate.format("YYYYMM")
      });

      if (response.status !== OK) {
        this.$store.commit("error/setCode", response.status);
        return false;
      }

      // 勤務表データ
      this.workschedules = response.data;
      // 勤務時間初期化
      this.worktimes = Array(response.data.length).fill(0);

      // プロジェクト勤務時間データ作成
      let projectWorktimes = [];
      let selected = [];
      let isFirst = true;

      // プロジェクト時間の計算
      response.data.forEach((val_1, idx_1, arr_1) => {
        // 1日のプロジェクト時間初期化
        let projectWorktime = [];

        // 1日のプロジェクト時間データ取得
        arr_1[idx_1].project_work.forEach((val_2, idx_2, arr_2) => {
          projectWorktime[idx_2] = {
            work_schedule_id: arr_2[idx_2].work_schedule_id,
            project_id: arr_2[idx_2].project_id,
            worktime: arr_2[idx_2].worktime
          };

          if (isFirst) {
            selected[idx_2] = { project_id: arr_2[idx_2].project_id };
          }
        });
        isFirst = false;
        this.selected = selected;

        // 1日のプロジェクト時間一件も存在しなければ、データ作成
        if (projectWorktime.length == 0) {
          projectWorktime[0] = {
            work_schedule_id: null,
            project_id: null,
            worktime: 0
          };
        }

        // 一日のプロジェクト時間データ格納
        projectWorktimes[idx_1] = projectWorktime;
        // 一日のプロジェクト時間を勤務表データに投入
        this.workschedules[idx_1].project_work = projectWorktime;
      });
      console.log("this.selected", this.selected);
      console.log("this.selected[0]", this.selected[0].project_id);
      console.log("this.selected[1]", this.selected[1].project_id);
      console.log("this.selected[2]", this.selected[2].project_id);
      console.log("this.projects", this.projects);
      console.log("this.projects[0]", this.projects[0]);
      console.log("this.projects[0].id", this.projects[0].id);
      console.log("this.projects[0].code", this.projects[0].code);
      console.log("this.projects[0].name", this.projects[0].name);

      // リアクティブデータ代入
      this.projectWorktimes = projectWorktimes;
      this.tableheaders = this.createtableheaders(projectWorktimes[0].length);
    },

    /** 勤務表データ登録 */
    async store() {
      const response = await axios.post(`/api/workschedule/store`, {
        workschedules: this.workschedules,
        projectWorktimes: this.projectWorktimes,
        projects: this.projects
      });

      if (response.status !== OK) {
        this.$store.commit("error/setCode", response.status);
        return false;
      }
    },

    /** テーブルのヘッダー情報 */
    createtableheaders: function(projectMaxCount) {
      let tableheaders = [
        { text: "日付", sortable: false },
        { text: "有給", sortable: false },
        { text: "開始時間", sortable: false },
        { text: "終了時間", sortable: false },
        { text: "休憩時間(h)", sortable: false },
        { text: "深夜休憩時間", sortable: false },
        { text: "勤務時間", sortable: false },
        { text: "PJ合計時間", sortable: false }
      ];

      for (let i = 1; i <= projectMaxCount; i++) {
        tableheaders[tableheaders.length] = {
          text: "PJ時間" + i,
          sortable: false
        };
      }
      tableheaders[tableheaders.length] = { text: "内容", sortable: false };

      return tableheaders;
    },

    /** 勤務時間計算 */
    worktime_day: function(index) {
      // リアクティブデータコピー
      let worktime_array = this.worktimes;
      // 1日の勤務時間計算
      worktime_array[index] = getWorktime(
        this.workschedules[index].starttime_hh,
        this.workschedules[index].starttime_mm,
        this.workschedules[index].endtime_hh,
        this.workschedules[index].endtime_mm,
        this.workschedules[index].breaktime,
        this.workschedules[index].breaktime_midnight
      );

      // リアクティブデータに登録
      this.worktimes = worktime_array;
      // 1月の合計勤務時間計算
      this.worktimeSum = this.worktimes.reduce(function(total, data) {
        return total + data;
      });

      return this.worktimes[index];
    },

    /** プロジェクト時間計算 */
    pj_worktime_day: function(index) {
      // プロジェクト時間を勤務表データに投入
      this.workschedules[index].project_work = this.projectWorktimes[index];

      // 1日のプロジェクト合計時間の計算
      let project_worktime = 0;
      this.projectWorktimes[index].forEach((val_2, idx_2, arr_2) => {
        project_worktime = project_worktime + parseFloat(val_2.worktime);
      });

      return project_worktime;
    },

    /** プロジェクト追加 */
    addProject: function() {
      // プロジェクト時間の計算
      this.workschedules.forEach((val_1, idx_1, arr_1) => {
        // 追加のプロジェクトデータを作成
        let data = {
          work_schedule_id: val_1.project_work[0].work_schedule_id,
          project_id: val_1.project_work[0].project_id,
          worktime: 0
        };

        // 一日のプロジェクト時間データ格納
        this.projectWorktimes[idx_1].splice(val_1.project_work.length, 0, data);
        // 一日のプロジェクト時間を勤務表データに投入
        this.workschedules[idx_1].project_work = this.projectWorktimes[idx_1];
      });

      // テーブルヘッダー変更
      this.tableheaders = this.createtableheaders(
        this.projectWorktimes[0].length
      );
    },

    /** プロジェクトselect */
    changeSelected: function(index) {
      console.log(this.selected, this.selected);
      console.log("selected");
    }
  },
  watch: {
    $route: {
      async handler() {
        await this.fetchHolidays();
        await this.fetchProjects();
        await this.fetchWorkSchedules();
      },
      immediate: true
    }
  }
};
</script>
