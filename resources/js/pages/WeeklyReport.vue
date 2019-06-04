<style scoped>
.holiday {
  background: lightgray;
}
.notsame {
  color: red;
}
</style>

<template>
  <div class="weekly-report">
    <v-app id="inspire">
      <v-container grid-list-md text-xs-left>
        <v-layout row wrap>
          <v-flex xs12>
            <div>
              <v-toolbar flat color="white">
                <v-toolbar-title>週報</v-toolbar-title>
                <v-divider class="mx-2" inset vertical></v-divider>
                <v-spacer>{{weekformat(targetWeek)}}</v-spacer>
              </v-toolbar>
              <v-flex xs6>
                <v-alert :value="this.weeklyreport.is_subumited" type="success">当週分の週報は提出済みです</v-alert>
                <v-alert :value="!this.weeklyreport.is_subumited" type="warning">当週分の週報は未提出です</v-alert>
              </v-flex>

              <v-flex xs6>
                <v-select
                  v-model="targetWeek"
                  :items="weekList"
                  @change="changeTargetWeek()"
                  item-value="week_number"
                  item-text="text"
                  label="対象週"
                  box
                ></v-select>
              </v-flex>

              <p>基本勤務日数：{{ this.basicWorkDay }} 日</p>
              <p>出勤日数：{{ WorktingDay() }} 日</p>
              <p>欠勤日数：{{ AbsenceDay() }} 日</p>
              <p>総勤務時間：{{ this.worktimeSum }} 時間</p>

              <v-flex xs6>
                <v-select
                  v-model="weeklyreport.project_id"
                  :items="projects"
                  item-value="id"
                  item-text="name"
                  label="プロジェクト"
                  box
                ></v-select>
              </v-flex>

              <div
                v-for="(projectWorktime, index) in projectWorktimes[0]"
                :key="projectWorktime.key"
              >
                <p>プロジェクト{{ index + 1 }} {{ projects[projectWorktime.project_id -1].name }}</p>
              </div>

              <v-btn color="success" @click="save()">週報保存</v-btn>
              <v-btn color="info" @click="submit()">週報提出</v-btn>

              <v-data-table
                :headers="tableheaders"
                :items="workschedules"
                :rows-per-page-items="[]"
                :pagination.sync="pagination"
                class="elevation-1"
              >
                <template v-slot:items="props">
                  <td
                    width="5%"
                    :class="{ holiday: isHoliday(props.item.workdate) }"
                  >{{ dateformat(props.item.workdate) }}</td>
                  <td width="3%" :class="{ holiday: isHoliday(props.item.workdate) }">
                    <v-checkbox v-model="props.item.is_paid_holiday" disabled></v-checkbox>
                  </td>
                  <td width="10%" :class="{ holiday: isHoliday(props.item.workdate) }">
                    <div style="display:flex;">
                      <v-text-field v-model="props.item.starttime_hh" type="Number" disabled></v-text-field>:
                      <v-text-field v-model="props.item.starttime_mm" type="Number" disabled></v-text-field>
                    </div>
                  </td>
                  <td width="10%" :class="{ holiday: isHoliday(props.item.workdate) }">
                    <div style="display:flex;">
                      <v-text-field v-model="props.item.endtime_hh" type="Number" disabled></v-text-field>:
                      <v-text-field v-model="props.item.endtime_mm" type="Number" disabled></v-text-field>
                    </div>
                  </td>
                  <td width="7%" :class="{ holiday: isHoliday(props.item.workdate) }">
                    <v-text-field v-model="props.item.breaktime" type="Number" disabled></v-text-field>
                  </td>
                  <td width="7%" :class="{ holiday: isHoliday(props.item.workdate) }">
                    <v-text-field v-model="props.item.breaktime_midnight" type="Number" disabled></v-text-field>
                  </td>
                  <td width="7%" :class="{ holiday: isHoliday(props.item.workdate) }">
                    <font
                      :class="{ notsame: !isSameWorkingTime(props.index) }"
                    >{{ worktimeADay(props.index) }}</font>
                  </td>
                  <td width="7%" :class="{ holiday: isHoliday(props.item.workdate) }">
                    <font
                      :class="{ notsame: !isSameWorkingTime(props.index) }"
                    >{{ PJWorktimeADay(props.index) }}</font>
                  </td>
                  <td
                    width="7%"
                    :class="{ holiday: isHoliday(props.item.workdate) }"
                    v-for="projectWorktime in projectWorktimes[props.index]"
                    :key="projectWorktime.key"
                  >
                    <v-text-field v-model="projectWorktime.worktime" type="Number" disabled></v-text-field>
                  </td>
                  <td width="30%" :class="{ holiday: isHoliday(props.item.workdate) }">
                    <v-textarea solo rows="1" v-model="props.item.detail" disabled></v-textarea>
                  </td>
                </template>
              </v-data-table>
              <v-textarea outline rows="2" v-model="weeklyreport.nextweek_schedule" label="現場の情報"></v-textarea>
              <v-textarea outline rows="2" v-model="weeklyreport.site_information" label="現場の情報"></v-textarea>
              <v-textarea outline rows="2" v-model="weeklyreport.thismonth_dayoff" label="今月の休み"></v-textarea>
              <v-textarea outline rows="2" v-model="weeklyreport.opinion" label="所感"></v-textarea>
              <v-btn color="success" @click="save()">週報保存</v-btn>
              <v-btn color="info" @click="submit()">週報提出</v-btn>
            </div>
          </v-flex>
        </v-layout>
      </v-container>
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
      targetWeek: 0,
      basicWorkDay: 0,
      oldestWorkdate: null,
      weekList: [],
      tableheaders: [],
      user: [],
      projects: [],
      holidays: [],
      workschedules: [],
      weeklyreport: [],
      worktimes: [0],
      projectWorktimes: [],
      worktimeSum: 0,
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
      this.targetWeek = this.targetDate.format("ggggWW");
    },

    /** 日付変換 */
    dateformat(date) {
      return moment(date).format("DD(ddd)");
    },

    /** 週番号から日付に変換 */
    weekNumberToDate(weekNumber) {
      const year = weekNumber.substr(0, 4);
      const weak = weekNumber.substr(4, 2);
      return moment(year).add(weak - 1, "weeks");
    },

    /** 週報変換 */
    weekformat(weekNumber) {
      const year = weekNumber.substr(0, 4);
      const weak = weekNumber.substr(4, 2);
      const startDate = moment(year)
        .add(weak - 1, "weeks")
        .startOf("isoweek");
      const endDate = moment(year)
        .add(weak - 1, "weeks")
        .endOf("isoweek");

      return (
        startDate.format("YYYY-MM-DD (ddd)") +
        " 〜 " +
        endDate.format("YYYY-MM-DD (ddd)")
      );
    },

    /** 週報リスト作成 */
    async createWeekList(oldestWorkdate) {
      let weekList = [];
      const startDate = moment(oldestWorkdate);
      const endDate = moment().endOf("isoweek");

      // 1週ずつインクリメントして配列へpush
      while (startDate.unix() <= endDate.unix()) {
        weekList.push({
          week_number: startDate.format("ggggWW"),
          text: this.weekformat(startDate.format("ggggWW"))
        });
        startDate.add(1, "weeks");
      }

      // 週リスト
      this.weekList = weekList;
    },

    /** 基本勤務日数計算 */
    culBasicWorkDay() {
      this.basicWorkDay = 0;
      const year = this.targetDate.format("YYYY");
      const weaknumber = this.targetDate.format("WW");
      const startDate = moment(year)
        .add(weaknumber - 1, "weeks")
        .startOf("isoweek");

      for (let i = 0; i < 7; i++) {
        !this.isHoliday(startDate.format("YYYY-MM-DD"))
          ? this.basicWorkDay++
          : null;
        startDate.add(1, "days");
      }
    },

    /** 出勤日数計算 */
    WorktingDay() {
      return this.worktimes.reduce(function(total, data, index) {
        return (index === 1 && total > 0 ? 1 : total) + (data > 0 ? 1 : 0);
      });
    },

    /** 欠勤日数計算 */
    AbsenceDay() {
      return this.basicWorkDay - this.WorktingDay();
    },

    /** 休日チェック */
    isHoliday(date) {
      return moment(date).day() % 6 === 0 || this.holidays[date] ? true : false;
    },

    /** 1日の勤務時間と1日のプロジェクト時間が一致しているか確認 */
    isSameWorkingTime(index) {
      let projectWorktime = 0;
      this.projectWorktimes[index].forEach((val_1, idx_1, arr_1) => {
        projectWorktime = projectWorktime + parseFloat(val_1.worktime);
      });

      return this.worktimes[index] === projectWorktime ? true : false;
    },

    /** 勤務表データ作成 */
    createWorkSchedule: function(responseData) {
      if (responseData.length === 0) {
        responseData = [];
        const year = this.targetDate.format("YYYY");
        const weaknumber = this.targetDate.format("WW");
        const startDate = moment(year)
          .add(weaknumber - 1, "weeks")
          .startOf("isoweek");
        // 当月分のデータが存在しない場合、デフォルト値で作成
        for (let i = 0; i < 7; i++) {
          let isHoliday = this.isHoliday(startDate.format("YYYY-MM-DD"));

          responseData.push({
            id: null,
            user_id: this.user.id,
            week_number: startDate.format("ggggWW"),
            workdate: startDate.format("YYYY-MM-DD"),
            is_paid_holiday: false,
            starttime_hh: isHoliday ? null : "09",
            starttime_mm: isHoliday ? null : "00",
            endtime_hh: isHoliday ? null : "18",
            endtime_mm: isHoliday ? null : "00",
            breaktime: isHoliday ? null : 1,
            breaktime_midnight: isHoliday ? null : 0,
            project_work: [
              {
                work_schedule_id: null,
                project_id: 1,
                worktime: isHoliday ? 0 : 8
              }
            ],
            detail: null
          });
          startDate.add(1, "days");
        }
      }

      return responseData;
    },

    /** 週報データ作成 */
    createWeeklyReport: function(responseData) {
      if (responseData.length === 0) {
        responseData = {
          id: null,
          user_id: this.user.id,
          week_number: this.targetDate.format("ggggWW"),
          project_id: 1,
          nextweek_schedule: null,
          site_information: null,
          thismonth_dayoff: null,
          opinion: null,
          is_subumited: false
        };
      }

      return responseData;
    },

    /** ユーザデータ取得 */
    async fetchUser() {
      const response = await axios.post(`/api/user/get`, {
        userId: this.$store.state.auth.user.id
      });

      if (response.status !== OK) {
        this.$store.commit("error/setCode", response.status);
        return false;
      }

      // ユーザデータ
      this.user = response.data;
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
        holidays[arr_1[idx_1].date] = arr_1[idx_1].name;
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
      this.projects = response.data.map(item => {
        return {
          id: item.id,
          code: item.code,
          name: item.code + " : " + item.name
        };
      });
    },

    /** 最古の勤務表データ取得 */
    async fetchOldestWorkdate() {
      const response = await axios.post(`/api/workschedule/getoldestworkdate`, {
        userId: this.$store.state.auth.user.id
      });

      if (response.status !== OK) {
        this.$store.commit("error/setCode", response.status);
        return false;
      }

      // 最古の勤務表データ
      this.oldestWorkdate = response.data;
      // 週リスト作成
      this.createWeekList(this.oldestWorkdate);
    },

    /** 勤務表データ取得 */
    async fetchWorkSchedules() {
      const response = await axios.post(`/api/workschedule/getweek`, {
        userId: this.$store.state.auth.user.id,
        weekNumber: this.targetWeek
      });

      if (response.status !== OK) {
        this.$store.commit("error/setCode", response.status);
        return false;
      }

      // 勤務表データ
      this.workschedules = this.createWorkSchedule(response.data);
      // 勤務時間初期化
      this.worktimes = Array(this.workschedules.length).fill(0);
      // プロジェクト時間
      this.projectWorktimes = this.workschedules.map(item => {
        return item["project_work"];
      });
      // テーブルヘッダー作成
      this.tableheaders = this.createTableHeaders(
        this.workschedules[0].project_work.length
      );
      // 勤務情報再計算
      this.culBasicWorkDay();
    },

    /** 週報データ取得 */
    async fetchWeeklyReport() {
      const response = await axios.post(`/api/weeklyreport/get`, {
        userId: this.$store.state.auth.user.id,
        weekNumber: this.targetWeek
      });

      if (response.status !== OK) {
        this.$store.commit("error/setCode", response.status);
        return false;
      }
      // 週報データ
      this.weeklyreport = this.createWeeklyReport(response.data);
    },

    /** 週報登録 */
    async save() {
      this.weeklyreport.is_subumited = false;
      this.store();
    },

    /** 週報提出 */
    async submit() {
      this.weeklyreport.is_subumited = true;
      this.store();
    },

    /** 週報データ登録 */
    async store() {
      const response = await axios.post(`/api/weeklyreport/store`, {
        weeklyreport: this.weeklyreport
      });

      if (response.status !== OK) {
        this.$store.commit("error/setCode", response.status);
        return false;
      }

      // 週報データ更新
      this.fetchWeeklyReport();
    },

    /** テーブルヘッダー */
    createTableHeaders: function(projectMaxCount) {
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
    worktimeADay: function(index) {
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

    /** 1日のプロジェクト時間計算 */
    PJWorktimeADay: function(index) {
      // プロジェクト時間を勤務表データに投入
      this.workschedules[index].project_work = this.projectWorktimes[index];

      // 1日のプロジェクト合計時間の計算
      let projectWorktime = 0;
      this.projectWorktimes[index].forEach((val_2, idx_2, arr_2) => {
        projectWorktime = projectWorktime + parseFloat(val_2.worktime);
      });

      return projectWorktime;
    },

    /** 対象週select */
    changeTargetWeek: function() {
      this.targetDate = this.weekNumberToDate(this.targetWeek);
      this.targetWeek = this.targetDate.format("ggggWW");
      this.fetchWorkSchedules();
      this.fetchWeeklyReport();
    }
  },
  watch: {
    $route: {
      async handler() {
        await this.fetchUser();
        await this.fetchHolidays();
        await this.fetchProjects();
        await this.fetchOldestWorkdate();
        await this.fetchWorkSchedules();
        await this.fetchWeeklyReport();
      },
      immediate: true
    }
  }
};
</script>
