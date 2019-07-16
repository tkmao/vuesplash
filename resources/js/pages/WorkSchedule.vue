<style scoped>
.holiday {
  background: lightgray;
}
.notsame {
  color: red;
}
</style>

<template>
  <div class="work-schedule">
    <v-app id="inspire">
      <v-container grid-list-md text-xs-left fluid>
        <v-layout row wrap>
          <v-flex xs12>
            <div>
              <div
                v-loading="loadingFlg"
                element-loading-text="Loading..."
                element-loading-spinner="loadingSpinner"
                element-loading-background="loadingBackground"
              >
                <v-toolbar flat color="white">
                  <v-toolbar-title>勤務表</v-toolbar-title>
                  <v-divider class="mx-2" inset vertical></v-divider>
                  <v-spacer>
                    <v-container fluid>
                      <v-layout align-center justify-start row wrap>
                        <v-flex xs3>
                          <span
                            style="font-size: 20px;"
                          >({{ this.targetDate.format("YYYY") }}年{{ this.targetDate.format("MM") }}月)</span>
                          <v-btn color="#ffe4b5" @click="changeMonth(-1)">
                            <v-icon>navigate_before</v-icon>前月
                          </v-btn>
                          <v-btn color="#ffe4b5" @click="changeMonth(1)">
                            翌月
                            <v-icon>navigate_next</v-icon>
                          </v-btn>
                        </v-flex>
                        <v-flex xs3>
                          <v-alert :value="this.isSubmitted" type="success">当月分の勤務表は提出済みです</v-alert>
                          <v-alert :value="!this.isSubmitted" type="warning">当月分の勤務表は未提出です</v-alert>
                        </v-flex>
                      </v-layout>
                    </v-container>
                  </v-spacer>
                </v-toolbar>

                <v-container fluid grid-list-md>
                  <v-layout row wrap>
                    <v-flex d-flex xs12 sm6 md4>
                      <table
                        border="1"
                        width="400"
                        cellspacing="1"
                        cellpadding="10"
                        bordercolor="#333333"
                      >
                        <tr>
                          <td bgcolor="#f0f8ff" width="40%">基本勤務日数</td>
                          <td bgcolor="#f0f8ff" width="60%">{{ this.basicWorkDay }} 日</td>
                        </tr>
                        <tr>
                          <td bgcolor="#f0f8ff" width="40%">出勤日数</td>
                          <td bgcolor="#f0f8ff" width="60%">{{ WorktingDay() }} 日</td>
                        </tr>
                        <tr>
                          <td bgcolor="#f0f8ff" width="40%">欠勤日数</td>
                          <td bgcolor="#f0f8ff" width="60%">{{ AbsenceDay() }} 日</td>
                        </tr>
                        <tr>
                          <td bgcolor="#f0f8ff" width="40%">今月の勤務時間</td>
                          <td
                            bgcolor="#f0f8ff"
                            width="60%"
                          >下限 {{ this.workingtimeMin }} h 〜 上限 {{ this.workingtimeMax }} h</td>
                        </tr>
                        <tr>
                          <td bgcolor="#f0f8ff" width="40%">総勤務時間</td>
                          <td bgcolor="#f0f8ff" width="60%">{{ this.worktimeSum }} 時間</td>
                        </tr>
                        <tr>
                          <td bgcolor="#f0f8ff" width="40%">不足時間</td>
                          <td bgcolor="#f0f8ff" width="60%">{{ ShortageTime() }} h</td>
                        </tr>
                        <tr>
                          <td bgcolor="#f0f8ff" width="40%">超過時間</td>
                          <td bgcolor="#f0f8ff" width="60%">{{ OverTime() }} h</td>
                        </tr>
                        <tr>
                          <td bgcolor="#f0f8ff" width="40%">残有給日数</td>
                          <td
                            bgcolor="#f0f8ff"
                            width="60%"
                          >{{ this.user.paid_holiday }} 日 (今月 {{ paidHolidayThisMonth() }} 日 使用)</td>
                        </tr>
                      </table>
                    </v-flex>
                    <v-flex d-flex xs12 sm6 md6>
                      <v-layout row wrap>
                        <v-flex xs10>
                          <div
                            v-for="(projectWorktime, index) in projectWorktimes[0]"
                            :key="projectWorktime.key"
                          >
                            <v-autocomplete
                              v-model="selected[index].project_id"
                              :loading="loadingProject"
                              :items="projects"
                              item-value="id"
                              item-text="name"
                              :search-input.sync="searchProject[index]"
                              @change="changeSelected(index)"
                              cache-items
                              class="mx-3"
                              flat
                              hide-no-data
                              hide-details
                              :label="projectIndex(index)"
                              outline
                            ></v-autocomplete>
                            <br />
                          </div>
                        </v-flex>
                        <v-flex xs6>
                          <v-btn fab color="success" @click="addProject()">
                            <v-icon>add_circle</v-icon>
                          </v-btn>プロジェクトを追加する
                        </v-flex>
                      </v-layout>
                    </v-flex>
                  </v-layout>
                </v-container>

                <v-container fluid>
                  <v-layout align-center justify-start row>
                    <v-flex xs2>
                      <v-btn
                        color="success"
                        :disabled="!isSameWorkingTimeAMonth()"
                        @click="save()"
                      >勤務表保存</v-btn>
                      <v-btn
                        color="info"
                        :disabled="!isSameWorkingTimeAMonth()"
                        @click="submit()"
                      >勤務表提出</v-btn>
                    </v-flex>
                    <v-flex xs5>
                      <v-alert
                        :value="isSameWorkingTimeAMonth()"
                        type="success"
                        outline
                      >勤務表の登録・提出が可能です</v-alert>
                      <v-alert
                        :value="!isSameWorkingTimeAMonth()"
                        type="error"
                        outline
                      >【登録・提出不可】総勤務時間と総プロジェクト時間が一致していません。</v-alert>
                    </v-flex>
                  </v-layout>
                </v-container>
              </div>

              <div
                v-loading="loadingFlg"
                element-loading-text="Loading..."
                element-loading-spinner="loadingSpinner"
                element-loading-background="loadingBackground"
              >
                <v-data-table
                  :headers="tableheaders"
                  :items="workschedules"
                  hide-actions
                  :pagination.sync="pagination"
                  class="elevation-1"
                >
                  <v-progress-linear v-slot:progress color="blue" indeterminate></v-progress-linear>
                  <template v-slot:items="props">
                    <td
                      width="3%"
                      :class="{ holiday: isHoliday(props.item.workdate) }"
                    >{{ dateformat(props.item.workdate) }}</td>
                    <td width="2%" :class="{ holiday: isHoliday(props.item.workdate) }">
                      <v-checkbox
                        v-model="props.item.is_paid_holiday"
                        :disabled="isHoliday(props.item.workdate)"
                        @change="changePaidHoliday(props.index)"
                      ></v-checkbox>
                    </td>
                    <td width="12%" :class="{ holiday: isHoliday(props.item.workdate) }">
                      <div style="display:flex;">
                        <v-text-field
                          v-model="props.item.starttime_hh"
                          type="Number"
                          min="0"
                          max="30"
                          :disabled="isPaidHoliday(props.index)"
                        ></v-text-field>:
                        <v-text-field
                          v-model="props.item.starttime_mm"
                          type="Number"
                          min="0"
                          max="45"
                          step="15"
                          :disabled="isPaidHoliday(props.index)"
                        ></v-text-field>
                      </div>
                    </td>
                    <td width="12%" :class="{ holiday: isHoliday(props.item.workdate) }">
                      <div style="display:flex;">
                        <v-text-field
                          v-model="props.item.endtime_hh"
                          type="Number"
                          min="0"
                          max="30"
                          :disabled="isPaidHoliday(props.index)"
                        ></v-text-field>:
                        <v-text-field
                          v-model="props.item.endtime_mm"
                          type="Number"
                          min="0"
                          max="45"
                          step="15"
                          :disabled="isPaidHoliday(props.index)"
                        ></v-text-field>
                      </div>
                    </td>
                    <td width="5%" :class="{ holiday: isHoliday(props.item.workdate) }">
                      <v-text-field
                        v-model="props.item.breaktime"
                        type="Number"
                        min="0"
                        step="0.25"
                        :disabled="isPaidHoliday(props.index)"
                      ></v-text-field>
                    </td>
                    <td width="5%" :class="{ holiday: isHoliday(props.item.workdate) }">
                      <v-text-field
                        v-model="props.item.breaktime_midnight"
                        type="Number"
                        min="0"
                        step="0.25"
                        :disabled="isPaidHoliday(props.index)"
                      ></v-text-field>
                    </td>
                    <td
                      width="5%"
                      :class="{ holiday: isHoliday(props.item.workdate) }"
                      class="text-xs-right"
                    >
                      <font
                        size="4"
                        :class="{ notsame: !isSameWorkingTime(props.index) }"
                      >{{ worktimeADay(props.index) }}</font>
                    </td>
                    <td
                      width="5%"
                      :class="{ holiday: isHoliday(props.item.workdate) }"
                      class="text-xs-right"
                    >
                      <font
                        size="4"
                        :class="{ notsame: !isSameWorkingTime(props.index) }"
                      >{{ PJWorktimeADay(props.index) }}</font>
                    </td>
                    <td
                      width="5%"
                      :class="{ holiday: isHoliday(props.item.workdate) }"
                      v-for="projectWorktime in projectWorktimes[props.index]"
                      :key="projectWorktime.key"
                    >
                      <v-text-field
                        v-model="projectWorktime.worktime"
                        type="Number"
                        min="0"
                        step="0.25"
                        :disabled="isPaidHoliday(props.index)"
                      ></v-text-field>
                    </td>
                    <td width="30%" :class="{ holiday: isHoliday(props.item.workdate) }">
                      <v-textarea
                        solo
                        rows="2"
                        v-model="props.item.detail"
                        name="detail"
                        label="詳細"
                        value
                      ></v-textarea>
                    </td>
                  </template>
                </v-data-table>
              </div>
              <div
                v-loading="loadingFlg"
                element-loading-text="Loading..."
                element-loading-spinner="loadingSpinner"
                element-loading-background="loadingBackground"
              >
                <v-container fluid>
                  <v-layout align-center justify-start row>
                    <v-flex xs2>
                      <v-btn
                        color="success"
                        :disabled="!isSameWorkingTimeAMonth()"
                        @click="save()"
                      >勤務表保存</v-btn>
                      <v-btn
                        color="info"
                        :disabled="!isSameWorkingTimeAMonth()"
                        @click="submit()"
                      >勤務表提出</v-btn>
                    </v-flex>
                    <v-flex xs5>
                      <v-alert
                        :value="isSameWorkingTimeAMonth()"
                        type="success"
                        outline
                      >勤務表の登録・提出が可能です</v-alert>
                      <v-alert
                        :value="!isSameWorkingTimeAMonth()"
                        type="error"
                        outline
                      >【登録・提出不可】総勤務時間と総プロジェクト時間が一致していません。</v-alert>
                    </v-flex>
                  </v-layout>
                </v-container>
              </div>
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
      loadingFlg: false,
      loadingText: "Loading...",
      loadingSpinner: "el-icon-loading",
      loadingBackground: "rgba(255, 255, 255, 0.8)",
      targetDate: 0,
      // basicWorkDay: 0,
      workingtimeMin: 0,
      workingtimeMax: 0,
      loadingProject: false,
      searchProject: null,
      tableheaders: [],
      user: [],
      isSubmitted: false,
      projects: [],
      holidays: [],
      workschedules: [],
      worktimes: [0],
      projectWorktimes: [],
      worktimeSum: 0,
      selected: [],
      pagination: { rowsPerPage: -1 },
      rules: {
        required: value => !!value || "This field is required."
      }
    };
  },
  computed: {
    formTitle() {
      return 1;
    },

    /** 基本勤務日数計算 */
    basicWorkDay() {
      let basicWorkDay = 0;
      let startDate = this.targetDate.clone();
      let endDate = this.targetDate.clone();
      startDate = startDate.startOf("month");
      endDate = endDate.endOf("month");

      // 1日ずつインクリメントして配列へpush
      while (startDate.unix() <= endDate.unix()) {
        !this.isHoliday(startDate.format("YYYY-MM-DD")) ? basicWorkDay++ : null;
        startDate.add(1, "days");
      }
      return basicWorkDay;
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

    /** プロジェクトタイトル取得 */
    projectIndex(index) {
      return "プロジェクト " + (index + 1);
    },

    /** 有給取得日チェック */
    isPaidHoliday(index) {
      return this.workschedules[index].is_paid_holiday;
    },

    /** 基本勤務日数計算 */
    // culBasicWorkDay() {
    //   this.basicWorkDay = 0;
    //   const startDate = this.targetDate.startOf("month").clone();
    //   const endDate = this.targetDate.endOf("month").clone();

    //   // 1日ずつインクリメントして配列へpush
    //   while (startDate.unix() <= endDate.unix()) {
    //     !this.isHoliday(startDate.format("YYYY-MM-DD"))
    //       ? this.basicWorkDay++
    //       : null;
    //     startDate.add(1, "days");
    //   }
    // },

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

    /** 今月の勤務時間数 */
    culBasicWorktimeAMonth() {
      const targetDate = this.targetDate.format("YYYY-MM-DD");
      const user = this.user.user_contract.find(function(element) {
        return element.startdate <= targetDate && targetDate <= element.enddate;
      });

      if (user.workingtime_type === 1) {
        this.workingtimeMin = this.basicWorkDay * user.worktime_day;
        this.workingtimeMax = this.workingtimeMin + user.maxworktime_month;
      } else if (user.workingtime_type === 2) {
        this.workingtimeMin = user.workingtime_min;
        this.workingtimeMax = user.workingtime_max;
      }
    },

    /** 不足時間 */
    ShortageTime() {
      return this.worktimeSum < this.workingtimeMin
        ? this.workingtimeMin - this.worktimeSum
        : 0;
    },

    /** 超過時間 */
    OverTime() {
      return this.worktimeSum > this.workingtimeMax
        ? this.worktimeSum - this.workingtimeMax
        : 0;
    },

    /** 今月の有給消化数 */
    paidHolidayThisMonth() {
      let paidHolidayThisMonth = 0;
      this.workschedules.forEach((val_1, idx_1, arr_1) => {
        val_1.is_paid_holiday ? paidHolidayThisMonth++ : null;
      });

      return paidHolidayThisMonth;
    },

    /** 休日チェック */
    isHoliday(date) {
      return moment(date).day() % 6 === 0 ||
        this.holidays.find(p => p.date === date)
        ? true
        : false;
    },

    /** 1日の勤務時間と1日のプロジェクト時間が一致しているか確認 */
    isSameWorkingTime(index) {
      let projectWorktime = 0;
      this.projectWorktimes[index].forEach((val_1, idx_1, arr_1) => {
        projectWorktime = projectWorktime + parseFloat(val_1.worktime);
      });

      return this.worktimes[index] === projectWorktime ? true : false;
    },

    /** 1月の勤務時間と1月のプロジェクト時間が一致しているか確認 */
    isSameWorkingTimeAMonth() {
      let canSubmit = false;
      if (this.projectWorktimes.length !== 0) {
        canSubmit = true;
        this.worktimes.forEach((val_1, idx_1, arr_1) => {
          this.isSameWorkingTime(idx_1) ? null : (canSubmit = false);
        });
      }

      return canSubmit;
    },

    /** 月次移動 */
    changeMonth: async function(index) {
      this.loadingFlg = true;
      const targetDate = this.targetDate.add(index, "months");
      this.targetDate = 0;
      this.targetDate = targetDate;
      await this.fetchWorkScheduleMonth();
      await this.fetchWorkSchedules();
      this.loadingFlg = false;
    },

    /** 勤務表データ作成 */
    createWorkSchedule: function(responseData) {
      if (responseData.length === 0) {
        responseData = [];
        const startDate = this.targetDate.startOf("month").clone();
        const endDate = this.targetDate.endOf("month").clone();

        // 当月分のデータが存在しない場合、デフォルト値で作成
        while (startDate.unix() <= endDate.unix()) {
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
          // 1日加算
          startDate.add(1, "days");
        }
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

    /** 勤務表提出状況データ取得 */
    async fetchWorkScheduleMonth() {
      const response = await axios.post(`/api/workschedulemonth/issubmitted`, {
        userId: this.$store.state.auth.user.id,
        yearmonth: this.targetDate.format("YYYYMM")
      });

      if (response.status !== OK) {
        this.$store.commit("error/setCode", response.status);
        return false;
      }

      // 勤務表提出状況データ
      this.isSubmitted = response.data === 1 ? true : false;
    },

    /** 休日データ取得 */
    async fetchHolidays() {
      const response = await axios.get(`/api/holiday/getall`);

      if (response.status !== OK) {
        this.$store.commit("error/setCode", response.status);
        return false;
      }

      // 休日データ
      this.holidays = response.data.map(item => {
        return {
          date: item.date,
          name: item.name
        };
      });
    },

    /** プロジェクトデータ取得 */
    async fetchProjects() {
      const response = await axios.get(`/api/project/getonlyactive`);

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

    /** 勤務表データ取得 */
    async fetchWorkSchedules() {
      const response = await axios.post(`/api/workschedule/get`, {
        userId: this.$store.state.auth.user.id,
        yearmonth: this.targetDate.format("YYYYMM")
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
      // プロジェクトコード
      this.selected = this.workschedules[0].project_work.map(item => {
        return { project_id: item["project_id"] };
      });
      // プロジェクト選択用の配列作成
      this.searchProject = Array(this.selected.length);
      // テーブルヘッダー作成
      this.tableheaders = this.createTableHeaders(
        this.workschedules[0].project_work.length
      );
      // 勤務情報再計算
      // this.culBasicWorkDay();
      this.culBasicWorktimeAMonth();
    },

    /** 勤務表登録 */
    async save() {
      this.store(false);
    },

    /** 勤務表提出 */
    async submit() {
      this.store(true);
    },

    /** 勤務表データ登録 */
    async store(submit) {
      const response = await axios.post(`/api/workschedule/store`, {
        workschedules: this.workschedules,
        submit: submit
      });

      if (response.status !== OK) {
        this.$store.commit("error/setCode", response.status);
        return false;
      }

      this.loadingFlg = true;
      // 勤務表提出状況データ取得
      await this.fetchWorkScheduleMonth();
      // 勤務表データ取得
      await this.fetchWorkSchedules();
      this.loadingFlg = false;
    },

    /** テーブルヘッダー */
    createTableHeaders: function(projectMaxCount) {
      let tableheaders = [
        { text: "日付", sortable: false },
        { text: "有給", sortable: false },
        { text: "開始時間", sortable: false },
        { text: "終了時間", sortable: false },
        { text: "休憩時間", sortable: false },
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
      tableheaders[tableheaders.length] = { text: "業務詳細", sortable: false };

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

    /** プロジェクト追加 */
    addProject: function() {
      // プロジェクト時間の計算
      this.workschedules.forEach((val_1, idx_1, arr_1) => {
        // 1日のプロジェクト時間データ格納
        this.projectWorktimes[idx_1].splice(val_1.project_work.length, 0, {
          work_schedule_id: val_1.project_work[0].work_schedule_id,
          project_id: 1,
          worktime: 0
        });
        // 1日のプロジェクト時間を勤務表データに投入
        this.workschedules[idx_1].project_work = this.projectWorktimes[idx_1];
      });

      // 選択済みプロジェクトidリスト（デフォルト：Project_id=1）
      this.selected.splice(this.selected.length, 0, { project_id: 1 });

      // テーブルヘッダー変更
      this.tableheaders = this.createTableHeaders(
        this.projectWorktimes[0].length
      );
    },

    /** プロジェクトselect */
    changeSelected: function(index) {
      this.projectWorktimes.forEach((val_1, idx_1, arr_1) => {
        // プロジェクト時間のリアクティブデータの変更
        this.$set(
          this.projectWorktimes[idx_1][index],
          "project_id",
          this.selected[index].project_id
        );

        // 勤務表のリアクティブデータの変更
        this.workschedules[idx_1].project_work = this.projectWorktimes[idx_1];
      });
    },

    /** 有給チェック */
    changePaidHoliday: function(index) {
      if (this.workschedules[index].is_paid_holiday) {
        this.$set(this.workschedules[index], "starttime_hh", null);
        this.$set(this.workschedules[index], "starttime_mm", null);
        this.$set(this.workschedules[index], "endtime_hh", null);
        this.$set(this.workschedules[index], "endtime_mm", null);
        this.$set(this.workschedules[index], "breaktime", null);
        this.$set(this.workschedules[index], "breaktime_midnight", null);
        this.projectWorktimes[index].forEach((val_1, idx_1, arr_1) => {
          this.$set(this.projectWorktimes[index][idx_1], "worktime", 0);
          // 勤務表のリアクティブデータの変更
          this.workschedules[index].project_work = this.projectWorktimes[index];
        });
      } else if (!this.isHoliday(this.workschedules[index].workdate)) {
        this.$set(this.workschedules[index], "starttime_hh", "09");
        this.$set(this.workschedules[index], "starttime_mm", "00");
        this.$set(this.workschedules[index], "endtime_hh", "18");
        this.$set(this.workschedules[index], "endtime_mm", "00");
        this.$set(this.workschedules[index], "breaktime", 1);
        this.$set(this.workschedules[index], "breaktime_midnight", 0);
        this.$set(this.projectWorktimes[index][0], "worktime", 8);
        // 勤務表のリアクティブデータの変更
        this.workschedules[index].project_work = this.projectWorktimes[index];
      }
    }
  },
  watch: {
    $route: {
      async handler() {
        this.loadingFlg = true;
        await this.fetchUser();
        await this.fetchWorkScheduleMonth();
        await this.fetchHolidays();
        await this.fetchProjects();
        await this.fetchWorkSchedules();
        this.loadingFlg = false;
      },
      immediate: true
    }
  }
};
</script>
