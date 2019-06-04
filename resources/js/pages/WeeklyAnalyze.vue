<style scoped>
.holiday {
  background: lightgray;
}
.is_submitted {
  color: red;
  font-weight: bold;
}
</style>

<template>
  <div class="weekly-analyze">
    <v-app id="inspire">
      <v-container grid-list-md text-xs-left fluid>
        <v-layout row wrap>
          <v-flex xs12>
            <div>
              <v-toolbar flat color="white">
                <v-toolbar-title>週報管理</v-toolbar-title>
                <v-divider class="mx-2" inset vertical></v-divider>
                <v-spacer></v-spacer>
              </v-toolbar>

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

              <v-tabs v-model="active" color="cyan" dark slider-color="yellow">
                <v-tab v-for="tab in tabs" :key="tab">{{ tab }}</v-tab>
                <v-tab-item>
                  <v-card flat>
                    <v-card-text>基本勤務日数：{{ this.basicWorkDay }} 日</v-card-text>
                    <v-card-text>
                      <v-data-table
                        :headers="weeklyReportHeaders"
                        :items="weeklyreports"
                        :pagination.sync="pagination"
                        class="elevation-1"
                      >
                        <template v-slot:items="props">
                          <td width="2%">{{ props.item.id }}</td>
                          <td width="7%">{{ props.item.name }}</td>
                          <td
                            width="20%"
                          >{{ props.item.weekly_report.project.code }} : {{ props.item.weekly_report.project.name }}</td>
                          <td width="20%">{{ props.item.weekly_report.nextweek_schedule }}</td>
                          <td width="20%">{{ props.item.weekly_report.site_information }}</td>
                          <td width="13%">{{ props.item.weekly_report.thismonth_dayoff }}</td>
                          <td width="15%">{{ props.item.weekly_report.opinion }}</td>
                          <td width="3%">
                            <font
                              :class="{ is_submitted: !props.item.weekly_report.is_subumited }"
                            >{{ isSubmitted(props.item.weekly_report.is_subumited) }}</font>
                          </td>
                        </template>
                      </v-data-table>
                    </v-card-text>
                  </v-card>
                </v-tab-item>
                <v-tab-item>
                  <v-card flat>
                    <v-card-text>基本勤務日数：{{ this.basicWorkDay }} 日</v-card-text>
                    <v-card-text>
                      <v-data-table
                        :headers="workScheduleHeaders"
                        :items="workschedules"
                        :pagination.sync="pagination"
                        class="elevation-1"
                      >
                        <template v-slot:items="props">
                          <td width="3%">{{ props.item.id }}</td>
                          <td width="5%">{{ props.item.name }}</td>
                          <td width="7%">グラフ</td>
                          <td width="7%">{{ props.item.worktimeSum }} h</td>
                          <td
                            width="10%"
                          >{{ props.item.workingtimeMin }} h 〜 {{ props.item.workingtimeMax }} h</td>
                          <td width="10%">{{ props.item.shortageTime }} h</td>
                          <td width="7%">{{ props.item.overTime }} h</td>
                          <td width="7%">{{ props.item.WorktingDay }} 日</td>
                          <td width="7%">{{ props.item.AbsenceDay }} 日</td>
                          <td width="7%">{{ props.item.OverDay }} 日</td>
                        </template>
                      </v-data-table>
                    </v-card-text>
                  </v-card>
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
      tabs: ["週報内容", "勤務時間内容"],
      active: null,
      targetDate: 0,
      targetWeek: 0,
      basicWorkDay: 0,
      oldestWorkdate: null,
      weekList: [],
      workScheduleHeaders: [
        { text: "ID", value: "id" },
        { text: "ユーザ名", sortable: false },
        { text: "勤務時間グラフ", sortable: false },
        { text: "勤務時間(当月累計)", sortable: false },
        { text: "基本勤務時間", sortable: false },
        { text: "当月残り勤務時間", sortable: false },
        { text: "超過時間(当月累計)", sortable: false },
        { text: "出勤日数(当月累計)" },
        { text: "欠勤日数(当月累計)" },
        { text: "超過日数(当月累計)" }
      ],
      weeklyReportHeaders: [
        { text: "ID", value: "id" },
        { text: "ユーザ名", sortable: false },
        { text: "プロジェクト名", sortable: false },
        { text: "来週の作業", sortable: false },
        { text: "今月の休暇", sortable: false },
        { text: "現場情報", sortable: false },
        { text: "所感", sortable: false },
        { text: "提出状況", value: "weekly_report.is_subumited" }
      ],
      user: [],
      projects: [],
      holidays: [],
      workschedules: [],
      weeklyreports: [],
      worktimes: [0],
      projectWorktimes: [],
      allUserWorktimes: [],
      allUserProjectWorktimes: [],
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

    /** 週報提出チェック */
    isSubmitted(is_subumited) {
      return is_subumited ? "提出済" : "未提出";
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

    /** 休日チェック */
    isHoliday(date) {
      return moment(date).day() % 6 === 0 || this.holidays[date] ? true : false;
    },

    /** 勤務表データ作成 */
    createWorkSchedule: function(responseData) {
      responseData.forEach((val_1, idx_1, arr_1) => {
        let work_schedule = [];
        if (val_1.work_schedule.length === 0) {
          const startDate = this.targetDate
            .endOf("isoweek")
            .startOf("month")
            .clone();
          const endDate = this.targetDate
            .endOf("isoweek")
            .endOf("month")
            .clone();

          // 当月分のデータが存在しない場合、デフォルト値で作成
          while (startDate.unix() <= endDate.unix()) {
            work_schedule.push({
              id: null,
              user_id: this.user.id,
              week_number: startDate.format("ggggWW"),
              workdate: startDate.format("YYYY-MM-DD"),
              is_paid_holiday: false,
              starttime_hh: null,
              starttime_mm: null,
              endtime_hh: null,
              endtime_mm: null,
              breaktime: null,
              breaktime_midnight: null,
              project_work: [
                {
                  work_schedule_id: null,
                  project_id: 1,
                  worktime: 0
                }
              ],
              detail: null
            });
            // 1日加算
            startDate.add(1, "days");
          }

          responseData[idx_1].work_schedule = work_schedule;
        }
      });

      return responseData;
    },

    /** 週報データ作成 */
    createWeeklyReport: function(responseData) {
      responseData.forEach((val_1, idx_1, arr_1) => {
        if (val_1.weekly_report === null) {
          responseData[idx_1].weekly_report = {
            id: null,
            project_id: null,
            user_id: null,
            week_number: null,
            nextweek_schedule: null, //
            site_information: null, //
            thismonth_dayoff: null, //
            opinion: null, //
            is_subumited: false, //
            project: [
              {
                id: null,
                code: null, //
                name: null, //
                category_id: null,
                company_id: null,
                user_id: null,
                status_id: null,
                is_deleted: null
              }
            ]
          };
        }
      });

      return responseData;
    },

    /** 全ユーザデータ取得 */
    async fetchUser() {
      const response = await axios.get(`/api/user/get`);

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

    /** 全ユーザ勤務表データ取得 */
    async fetchWorkSchedules() {
      const response = await axios.post(`/api/workschedule/getalluser`, {
        yearmonth: this.targetDate.endOf("isoweek").format("YYYYMM")
      });

      if (response.status !== OK) {
        this.$store.commit("error/setCode", response.status);
        return false;
      }

      // 全ユーザの勤務表データ
      this.workschedules = this.createWorkSchedule(response.data);
      // 全ユーザの勤務時間初期化
      this.allUserWorktimes = this.workschedules.map(item => {
        return {
          user_id: item["id"],
          worktimes: Array(this.workschedules[0].work_schedule.length).fill(0)
        };
      });

      // 全ユーザのプロジェクト時間
      this.allUserProjectWorktimes = this.workschedules.map(item => {
        return {
          user_id: item["id"],
          worktimes: item["work_schedule"].map(item2 => {
            return item2["project_work"];
          })
        };
      });

      this.projectWorktimes = this.workschedules.map(item => {
        return item["project_work"];
      });

      // 勤務情報再計算
      this.culBasicWorkDay();
      // 基本勤務日数計算
      this.culBasicWorktimeAMonth();
      // 勤務時間計算
      this.culWorktimes();
    },

    /** 全ユーザ週報データ取得 */
    async fetchWeeklyReport() {
      const response = await axios.post(`/api/weeklyreport/getalluser`, {
        weekNumber: this.targetWeek
      });

      if (response.status !== OK) {
        this.$store.commit("error/setCode", response.status);
        return false;
      }
      // 週報データ
      this.weeklyreports = this.createWeeklyReport(response.data);
    },

    /** 基本勤務日数計算 */
    culBasicWorkDay() {
      this.basicWorkDay = 0;
      const startDate = this.targetDate
        .endOf("isoweek")
        .startOf("month")
        .clone();
      const endDate = this.targetDate
        .endOf("isoweek")
        .endOf("month")
        .clone();

      // 1日ずつインクリメントして配列へpush
      while (startDate.unix() <= endDate.unix()) {
        !this.isHoliday(startDate.format("YYYY-MM-DD"))
          ? this.basicWorkDay++
          : null;
        startDate.add(1, "days");
      }
    },

    /** 基本勤務時間作成 */
    culBasicWorktimeAMonth() {
      /** 今月の勤務時間数 */
      this.workschedules.forEach((val_1, idx_1, arr_1) => {
        if (val_1.workingtime_type === 1) {
          this.$set(
            this.workschedules[idx_1],
            "workingtimeMin",
            this.basicWorkDay * val_1.worktime_day
          );
          this.$set(
            this.workschedules[idx_1],
            "workingtimeMax",
            val_1.workingtimeMin + val_1.maxworktime_month
          );
        } else if (val_1.workingtime_type === 2) {
          this.$set(
            this.workschedules[idx_1],
            "workingtimeMin",
            val_1.workingtime_min
          );
          this.$set(
            this.workschedules[idx_1],
            "workingtimeMax",
            val_1.workingtime_max
          );
        }
      });
    },

    /** 全ユーザ合計勤務時間計算 */
    culWorktimes: function() {
      this.workschedules.forEach((val_1, idx_1, arr_1) => {
        val_1.work_schedule.forEach((val_2, idx_2, arr_2) => {
          // 各ユーザの1日の勤務時間の計算
          this.$set(
            this.allUserWorktimes[idx_1].worktimes,
            idx_2,
            getWorktime(
              val_2.starttime_hh,
              val_2.starttime_mm,
              val_2.endtime_hh,
              val_2.endtime_mm,
              val_2.breaktime,
              val_2.breaktime_midnight
            )
          );
        });
        // 1月の合計勤務時間計算
        this.$set(
          this.workschedules[idx_1],
          "worktimeSum",
          this.allUserWorktimes[idx_1].worktimes.reduce(function(total, data) {
            return total + data;
          })
        );
        // 超過時間計算
        this.$set(
          this.workschedules[idx_1],
          "overTime",
          this.workschedules[idx_1].worktimeSum >
            this.workschedules[idx_1].workingtimeMax
            ? this.workschedules[idx_1].worktimeSum -
                this.workschedules[idx_1].workingtimeMax
            : 0
        );
        // 不足時間計算
        this.$set(
          this.workschedules[idx_1],
          "shortageTime",
          this.workschedules[idx_1].worktimeSum <
            this.workschedules[idx_1].workingtimeMin
            ? this.workschedules[idx_1].workingtimeMin -
                this.workschedules[idx_1].worktimeSum
            : 0
        );
        // 出勤日数計算
        this.$set(
          this.workschedules[idx_1],
          "WorktingDay",
          this.WorktingDay(this.allUserWorktimes[idx_1].worktimes)
        );
        // 欠勤日数計算
        this.$set(
          this.workschedules[idx_1],
          "AbsenceDay",
          this.workschedules[idx_1].WorktingDay < this.basicWorkDay
            ? this.basicWorkDay - this.workschedules[idx_1].WorktingDay
            : 0
        );
        // 超過日数計算
        this.$set(
          this.workschedules[idx_1],
          "OverDay",
          this.workschedules[idx_1].WorktingDay > this.basicWorkDay
            ? this.workschedules[idx_1].WorktingDay - this.basicWorkDay
            : 0
        );
      });
    },

    /** 出勤日数計算 */
    WorktingDay(worktimes) {
      return worktimes.reduce(function(total, data, index) {
        return (index === 1 && total > 0 ? 1 : total) + (data > 0 ? 1 : 0);
      });
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
