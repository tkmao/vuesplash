<style scoped>
.holiday {
  background: lightgray;
}
.is_submitted {
  color: red;
  font-weight: bold;
}
.half {
  width: 48%;
  display: inline-block;
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

              <div
                v-loading="loadingFlg"
                element-loading-text="Loading..."
                element-loading-spinner="loadingSpinner"
                element-loading-background="loadingBackground"
              >
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
              </div>

              <div
                v-loading="loadingFlg"
                element-loading-text="Loading..."
                element-loading-spinner="loadingSpinner"
                element-loading-background="loadingBackground"
              >
                <v-tabs v-model="active" color="cyan" dark slider-color="yellow">
                  <v-tab v-for="tab in tabs" :key="tab">{{ tab }}</v-tab>
                  <v-tab-item>
                    <v-card flat>
                      <v-card-text>
                        基本勤務日数：{{ this.basicWorkDay }} 日
                        <br>
                        社員数：{{ this.workschedules.length }} 人
                        <br>
                        週報提出：{{ peopleSubmit() }} 人
                      </v-card-text>

                      <v-card-title>
                        <v-toolbar dark color="teal">
                          <v-toolbar-title>検索</v-toolbar-title>
                          <v-text-field
                            v-model="searchWeeklyReport"
                            append-icon="search"
                            label="社員名 or プロジェクト etc.."
                            single-line
                            hide-details
                          ></v-text-field>
                        </v-toolbar>
                      </v-card-title>
                      <v-card-text>
                        <v-data-table
                          :headers="weeklyReportHeaders"
                          :items="weeklyReportsValidUser()"
                          hide-actions
                          :pagination.sync="pagination"
                          class="elevation-1"
                          :search="searchWeeklyReport"
                        >
                          <v-progress-linear v-slot:progress color="blue" indeterminate></v-progress-linear>
                          <template v-slot:items="props">
                            <td width="2%">{{ props.item.id }}</td>
                            <td width="7%">{{ props.item.name }}</td>
                            <td width="5%">{{ props.item.weekly_report.project.code }}</td>
                            <td width="15%">{{ props.item.weekly_report.project.name }}</td>
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
                          <template v-slot:no-results>
                            <v-alert
                              :value="true"
                              color="error"
                              icon="warning"
                            >"{{ searchWeeklyReport }}" と一致するデータは存在していません。</v-alert>
                          </template>
                        </v-data-table>
                      </v-card-text>
                    </v-card>
                  </v-tab-item>
                  <v-tab-item>
                    <v-card flat>
                      <v-card-text>
                        基本勤務日数：{{ this.basicWorkDay }} 日
                        <br>
                        社員数：{{ this.workschedules.length }} 人
                      </v-card-text>
                      <v-card-text>※ 当月累計</v-card-text>

                      <v-card-title>
                        <v-toolbar dark color="teal">
                          <v-toolbar-title>検索</v-toolbar-title>
                          <v-text-field
                            v-model="searchworkSchedule"
                            append-icon="search"
                            label="社員名 etc.."
                            single-line
                            hide-details
                          ></v-text-field>
                        </v-toolbar>
                      </v-card-title>
                      <v-card-text>
                        <v-data-table
                          :headers="workScheduleHeaders"
                          :items="workschedulesValidUser()"
                          hide-actions
                          :pagination.sync="pagination"
                          class="elevation-1"
                          :search="searchworkSchedule"
                        >
                          <v-progress-linear v-slot:progress color="blue" indeterminate></v-progress-linear>
                          <template v-slot:items="props">
                            <td width="3%">{{ props.item.id }}</td>
                            <td width="10%">{{ props.item.name }}</td>
                            <td width="5%">グラフ</td>
                            <td class="text-xs-right" width="5%">{{ props.item.worktimeSum }} h</td>
                            <div v-if="isContracted(props.item.workingtimeType)">
                              <td width="5%">
                                {{ props.item.workingtimeMin }} h 〜 {{ props.item.workingtimeMax }} h
                                <br>
                                （{{ workingtimetypes.find(x => x.value === props.item.workingtimeType).text }}）
                              </td>
                            </div>
                            <div v-else>
                              <td>現在未契約</td>
                            </div>
                            <td class="text-xs-right" width="5%">{{ props.item.shortageTime }} h</td>
                            <td class="text-xs-right" width="5%">{{ props.item.overTime }} h</td>
                            <td class="text-xs-right" width="5%">{{ props.item.WorktingDay }} 日</td>
                            <td class="text-xs-right" width="5%">{{ props.item.AbsenceDay }} 日</td>
                            <td class="text-xs-right" width="5%">{{ props.item.OverDay }} 日</td>
                          </template>
                          <template v-slot:no-results>
                            <v-alert
                              :value="true"
                              color="error"
                              icon="warning"
                            >"{{ searchworkSchedule }}" と一致するデータは存在していません。</v-alert>
                          </template>
                        </v-data-table>
                      </v-card-text>
                    </v-card>
                  </v-tab-item>
                  <v-tab-item>
                    <div>
                      <v-card-text>
                        総勤務時間：{{ this.grossAllProjectWorktime }} 時間
                        <br>
                        社員数：{{ this.workschedules.length }} 人
                        <br>
                        平均勤務時間：{{ averageWorktime() }} 時間
                      </v-card-text>

                      <v-btn
                        color="success"
                        @click="headerSortByProjectCode()"
                      >{{ buttonNameProjectCode() }}</v-btn>
                      <v-btn
                        color="success"
                        @click="headerSortByWorktime()"
                      >{{ buttonNameWorktime() }}</v-btn>

                      <v-expansion-panel v-model="panel" expand>
                        <v-expansion-panel-content
                          v-for="(projectWorktimes, i) in projectWorktimesHeader"
                          :key="i"
                        >
                          <template v-slot:header>
                            <table border="1">
                              <tr>
                                <td align="center" nowrap width="5%">{{i + 1}}</td>
                                <td align="left" width="10%">{{ projectWorktimes.project_code }}</td>
                                <td align="left" width="65%">{{ projectWorktimes.project_name }}</td>
                                <td align="right" width="10%">{{ projectWorktimes.worktime }} 時間</td>
                                <td
                                  align="right"
                                  width="10%"
                                >{{ percentageOfAllProjectwork(projectWorktimes.worktime) }} %</td>
                              </tr>
                            </table>
                          </template>
                          <v-card>
                            <v-card-text class="grey lighten-3">
                              <v-data-table
                                :headers="tableheaders"
                                :items="projectParticipants(projectWorktimes.project_id)"
                                hide-actions
                                :pagination.sync="pagination"
                                class="elevation-1"
                              >
                                <template v-slot:items="props">
                                  <td class="text-xs-right" width="5%">{{ props.item.user_id }}</td>
                                  <td width="10%">{{ props.item.user_name }}</td>
                                  <td class="text-xs-right" width="10%">{{ props.item.worktime }} h</td>
                                  <td class="text-xs-right" width="10%">{{ props.item.percent }} %</td>
                                </template>
                              </v-data-table>
                            </v-card-text>
                          </v-card>
                        </v-expansion-panel-content>
                      </v-expansion-panel>
                    </div>
                  </v-tab-item>
                  <v-tab-item>
                    <v-flex xs10>
                      <v-toolbar dark color="teal">
                        <v-toolbar-title>検索</v-toolbar-title>
                        <v-autocomplete
                          v-model="targetProjectId"
                          :loading="loadingProject"
                          :items="projects"
                          item-value="id"
                          item-text="name"
                          :search-input.sync="searchProject"
                          @change="createProjectWorklistDoughnut()"
                          cache-items
                          class="mx-3"
                          flat
                          hide-no-data
                          hide-details
                          label="プロジェクトコード/名"
                          solo-inverted
                        ></v-autocomplete>
                      </v-toolbar>
                    </v-flex>
                    <div class="half">
                      <div v-if="canCreateDoughnutAllProject()">
                        <doughnut-chart :chart-data="allProjectDoughnutcollection"></doughnut-chart>
                      </div>
                      <div v-else>
                        <v-alert :value="true" type="warning">表示できるデータがありません。条件を変えて表示してください</v-alert>
                      </div>
                    </div>
                    <div class="half">
                      <div v-if="canCreateDoughnutProject()">
                        <doughnut-chart :chart-data="projectDoughnutcollection"></doughnut-chart>
                      </div>
                      <div v-else>
                        <v-alert :value="true" type="warning">表示できるデータがありません。条件を変えて表示してください</v-alert>
                      </div>
                    </div>
                  </v-tab-item>
                  <v-tab-item>
                    <v-flex xs10>
                      <v-toolbar dark color="teal">
                        <v-toolbar-title>社員選択/検索</v-toolbar-title>
                        <v-autocomplete
                          v-model="targetUserId"
                          :loading="loadingUser"
                          :items="users"
                          item-value="id"
                          item-text="name"
                          :search-input.sync="searchUser"
                          @change="createWorktimeGraph()"
                          cache-items
                          class="mx-3"
                          flat
                          hide-no-data
                          hide-details
                          label="ユーザ名"
                          solo-inverted
                        ></v-autocomplete>
                      </v-toolbar>
                    </v-flex>
                    <div class="half">
                      <line-chart :chart-data="datacollection"></line-chart>
                    </div>
                  </v-tab-item>
                </v-tabs>
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
import LineChart from "../LineChart.js";
import DoughnutChart from "../DoughnutChart.js";

export default {
  components: { LineChart, DoughnutChart },
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
      tabs: [
        "週報内容",
        "勤務時間内容",
        "プロジェクト割合",
        "PJグラフ",
        "勤務表グラフ"
      ],
      panel: [false, false],
      active: null,
      searchProject: null,
      loadingProject: false,
      searchUser: null,
      loadingUser: false,
      isAscProjectCode: false,
      isAscWorktime: false,
      targetDate: 0,
      targetWeek: 0,
      targetProjectId: 1,
      targetUserId: 1,
      basicWorkDay: 0,
      grossAllProjectWorktime: 0,
      oldestWorkdate: null,
      weekList: [],
      searchProject: "",
      searchworkSchedule: "",
      searchWeeklyReport: "",
      workingtimetypes: [
        { text: "勤務日数により変動", value: 1 },
        { text: "固定勤務時間", value: 2 }
      ],
      tableheaders: [
        { text: "ID", value: "user_id" },
        { text: "社員名", value: "user_name" },
        { text: "勤務時間(※)", value: "worktime" },
        { text: "割合(%)", value: "percent" }
      ],
      workScheduleHeaders: [
        { text: "ID", value: "id" },
        { text: "社員名", value: "name", sortable: false },
        { text: "グラフ", sortable: false },
        { text: "勤務時間(※)", value: "worktimeSum" },
        { text: "基本勤務時間", sortable: false },
        { text: "当月残り勤務時間", value: "shortageTime" },
        { text: "超過時間(※)", value: "overTime" },
        { text: "出勤日数(※)", value: "WorktingDay" },
        { text: "欠勤日数(※)", value: "AbsenceDay" },
        { text: "超過日数(※)", value: "OverDay" }
      ],
      weeklyReportHeaders: [
        { text: "ID", value: "id" },
        { text: "社員名", value: "name", sortable: false },
        {
          text: "PJ CD",
          value: "weekly_report.project.code"
        },
        {
          text: "PJ名",
          value: "weekly_report.project.name",
          sortable: false
        },
        {
          text: "来週の作業",
          value: "weekly_report.nextweek_schedule",
          sortable: false
        },
        {
          text: "今月の休暇",
          value: "weekly_report.site_information",
          sortable: false
        },
        {
          text: "現場情報",
          value: "weekly_report.thismonth_dayoff",
          sortable: false
        },
        { text: "所感", value: "naweekly_report.opinion", sortable: false },
        { text: "提出状況", value: "weekly_report.is_subumited" }
      ],
      users: [],
      holidays: [],
      projects: [],
      workschedules: [],
      weeklyreports: [],
      worktimes: [0],
      projectWorktimesHeader: [],
      projectWorktimesDetail: [],
      allUserWorktimes: [],
      datacollection: {
        labels: [0],
        datasets: [
          {
            backgroundColor: "rgba(255,100,100,0.1)",
            data: [0],
            label: "default"
          }
        ]
      },
      // ドーナツグラフ（全プロジェクト）
      allProjectDoughnutcollection: {
        labels: ["labels"],
        datasets: [
          {
            data: [0],
            backgroundColor: ["rgba(255, 0, 0, 0)"]
          }
        ]
      },
      // ドーナツグラフ（各プロジェクト）
      projectDoughnutcollection: {
        labels: ["labels"],
        datasets: [
          {
            data: [0],
            backgroundColor: ["rgba(255, 0, 0, 0)"]
          }
        ]
      },
      pagination: { rowsPerPage: -1, sortBy: "worktime", descending: true },
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
      this.targetDate = moment().startOf("isoweek");
      const targetWeek = this.targetDate.clone();
      this.targetWeek = targetWeek.format("ggggWW");
    },

    /** 週報リスト */
    weeklyReportsValidUser() {
      let targetDate = this.targetDate.clone();
      const weekStartDate = targetDate.format("YYYY-MM-DD");
      const weekEndDate = targetDate.endOf("isoweek").format("YYYY-MM-DD");

      return this.weeklyreports.filter(function(value1, index1, array1) {
        return (
          value1.user_contract.find(function(element) {
            return (
              (element.startdate <= weekStartDate &&
                weekStartDate <= element.enddate) ||
              (element.startdate <= weekEndDate &&
                weekEndDate <= element.enddate)
            );
          }) !== undefined
        );
      });
    },

    /** 勤務表リスト */
    workschedulesValidUser() {
      let targetDate = this.targetDate.clone();
      const monthStartDate = targetDate
        .endOf("isoweek")
        .startOf("month")
        .format("YYYY-MM-DD");
      const monthEndDate = targetDate
        .endOf("isoweek")
        .endOf("month")
        .format("YYYY-MM-DD");

      return this.workschedules.filter(function(value1, index1, array1) {
        return (
          value1.user_contract.find(function(element) {
            return (
              (element.startdate <= monthStartDate &&
                monthStartDate <= element.enddate) ||
              (element.startdate <= monthEndDate &&
                monthEndDate <= element.enddate)
            );
          }) !== undefined
        );
      });
    },

    /** 線グラフ作成(勤務時間用) */
    createWorktimeGraph() {
      // 実勤務時間データ作成
      const worktimes = this.allUserWorktimes.find(
        x => x.user_id === this.targetUserId
      ).worktimes;

      const datasetWorktimes = worktimes.map(function(value1, index1, array1) {
        return worktimes
          .filter(function(value2, index2, array2) {
            return index2 <= index1;
          })
          .reduce(function(total3, data3, index3) {
            return index3 === 0 ? 0 : total3 + data3;
          });
      });

      // 勤務時間下限・上限データ作成
      const lengthDay = this.allUserWorktimes[0].worktimes.length;
      const min = this.workschedules.find(x => x.id === this.targetUserId)
        .workingtimeMin;
      const max = this.workschedules.find(x => x.id === this.targetUserId)
        .workingtimeMax;

      const datasetWorktimeMin = new Array(lengthDay)
        .fill(0)
        .map(function(value, index, array) {
          return (min * ((index + 1) / lengthDay)).toFixed(1);
        });

      const datasetWorktimeMax = new Array(lengthDay)
        .fill(0)
        .map(function(value, index, array) {
          return (max * ((index + 1) / lengthDay)).toFixed(1);
        });

      let datasets = [];
      // 実勤務時間
      datasets.push({
        label: this.users.find(x => x.id === this.targetUserId).name, // 社員名
        backgroundColor: "rgba(255,100,100,0.1)",
        data: datasetWorktimes // 勤務時間加算したもの
      });
      // 勤務時間下限
      datasets.push({
        label: "勤務時間下限",
        backgroundColor: "rgba(0,0,255,0.1)",
        data: datasetWorktimeMin
      });

      // 勤務時間上限
      datasets.push({
        label: "勤務時間上限",
        backgroundColor: "rgba(0,255,0,0.1)",
        data: datasetWorktimeMax
      });

      // Lineグラフ用データ代入
      this.datacollection = {
        labels: worktimes.map(function(value, index, array) {
          return index + 1;
        }), // 横軸
        datasets: datasets
      };
    },

    /** ドーナツグラフ作成(プロジェクト時間用) */
    createAllProjectWorklistDoughnut() {
      const projectWorktimesHeader = this.projectWorktimesHeader;
      const doughnutData = projectWorktimesHeader.sort(function(a, b) {
        return a.worktime < b.worktime ? 1 : -1;
      });

      let dColors = [];
      for (let i = 0; i < doughnutData.length; i++) {
        let code = i * 5;
        dColors.push("rgba(255," + code + "," + code + ",0.4)");
      }

      this.allProjectDoughnutcollection = {
        labels: doughnutData.map(y => y.project_code),
        datasets: [
          {
            data: doughnutData.map(y => y.worktime),
            backgroundColor: dColors
          }
        ]
      };
    },

    /** ドーナツグラフ作成(プロジェクト時間用) */
    createProjectWorklistDoughnut() {
      const projectWorktimesDetail = this.projectWorktimesDetail;
      const doughnutData = projectWorktimesDetail
        .filter(x => x.project_id === this.targetProjectId)
        .sort(function(a, b) {
          return a.worktime < b.worktime ? 1 : -1;
        });

      let dColors = [];
      for (let i = 0; i < doughnutData.length; i++) {
        let code = i * 5;
        dColors.push("rgba(255," + code + "," + code + ",0.4)");
      }

      this.projectDoughnutcollection = {
        labels: doughnutData.map(y => y.user_name),
        datasets: [
          {
            data: doughnutData.map(y => y.worktime),
            backgroundColor: dColors
          }
        ]
      };
    },

    /** 契約情報は存在するか */
    isContracted(workingtimeType) {
      return workingtimeType ? true : false;
    },

    /** ドーナツグラフを表示できるか確認（全プロジェクト） */
    canCreateDoughnutAllProject(date) {
      return this.allProjectDoughnutcollection.datasets[0].data.length !== 0
        ? this.allProjectDoughnutcollection.datasets[0].data.reduce((a, b) =>
            a > b ? a : b
          ) === 0
          ? false
          : true
        : false;
    },

    /** ドーナツグラフを表示できるか確認（各プロジェクト） */
    canCreateDoughnutProject(date) {
      return this.projectDoughnutcollection.datasets[0].data.length !== 0
        ? this.projectDoughnutcollection.datasets[0].data.reduce((a, b) =>
            a > b ? a : b
          ) === 0
          ? false
          : true
        : false;
    },

    /** 日付変換 */
    dateformat(date) {
      return moment(date).format("DD(ddd)");
    },

    /** 週番号から日付に変換 */
    weekNumberToDate(weekNumber) {
      const year = weekNumber.substr(0, 4);
      const weak = weekNumber.substr(4, 2);
      return moment(year)
        .add(weak - 1, "weeks")
        .startOf("isoweek");
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

    /** 休日チェック */
    isHoliday(date) {
      return moment(date).day() % 6 === 0 ||
        this.holidays.find(p => p.date === date)
        ? true
        : false;
    },

    /** プロジェクト参加者（特定のプロジェクトIDを引っ張る） */
    projectParticipants(project_id) {
      return this.projectWorktimesDetail.filter(function(item, index) {
        return item.project_id == project_id;
      });
    },

    /** プロジェクトコードでソート（ヘッダー） */
    headerSortByProjectCode() {
      this.isAscProjectCode = !this.isAscProjectCode;
      const asc = this.isAscProjectCode;
      this.projectWorktimesHeader.sort(function(a, b) {
        return a.project_code > b.project_code ? (asc ? -1 : 1) : asc ? 1 : -1;
      });
    },

    /** プロジェクト時間でソート（ヘッダー） */
    headerSortByWorktime() {
      this.isAscWorktime = !this.isAscWorktime;
      const asc = this.isAscWorktime;
      this.projectWorktimesHeader.sort(function(a, b) {
        return a.worktime > b.worktime ? (asc ? -1 : 1) : asc ? 1 : -1;
      });
    },

    /** プロジェクトコードボタン名
     */
    buttonNameProjectCode() {
      return this.isAscProjectCode ? "PJ CD(昇順)" : "PJ CD(降順)";
    },

    /** プロジェクト時間ボタン名
     */
    buttonNameWorktime() {
      return this.isAscWorktime ? "時間(昇順)" : "時間(降順)";
    },

    /** 週報提出チェック */
    isSubmitted(is_subumited) {
      return is_subumited ? "提出済" : "未提出";
    },

    /** 平均勤務時間 */
    averageWorktime() {
      return this.workschedules.length === 0
        ? 0
        : (this.grossAllProjectWorktime / this.workschedules.length).toFixed(1);
    },

    /** 総プロジェクト時間割合 */
    percentageOfAllProjectwork(worktime) {
      return this.grossAllProjectWorktime === 0
        ? 0
        : ((worktime / this.grossAllProjectWorktime) * 100).toFixed(1);
    },

    /** 週報提出人数 */
    peopleSubmit() {
      return this.weeklyreports.length === 0
        ? 0
        : this.weeklyreports.reduce(function(total, data, index) {
            return (
              (index === 1
                ? total.weekly_report.is_subumited
                  ? 1
                  : 0
                : total) + (data.weekly_report.is_subumited ? 1 : 0)
            );
          });
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
      this.weekList = weekList.sort(function(a, b) {
        return a.week_number > b.week_number ? -1 : 1;
      });
    },

    /** 勤務表データ作成 */
    createWorkSchedule: function(responseData) {
      responseData.forEach((val_1, idx_1, arr_1) => {
        let work_schedule = [];
        if (val_1.work_schedule.length === 0) {
          let startDate = this.targetDate.clone();
          let endDate = this.targetDate.clone();
          startDate.endOf("isoweek").startOf("month");
          endDate.endOf("isoweek").endOf("month");

          // 当月分のデータが存在しない場合、デフォルト値で作成
          while (startDate.unix() <= endDate.unix()) {
            work_schedule.push({
              id: null,
              user_id: val_1.id, //
              week_number: startDate.format("ggggWW"), //
              workdate: startDate.format("YYYY-MM-DD"), //
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
                  project_id: 1, //
                  worktime: 0, //
                  project: {
                    id: 1,
                    code: "00000", //
                    name: "未設定", //
                    company_id: null,
                    category_id: null,
                    status_id: null,
                    user_id: null,
                    is_deleted: null
                  }
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
    async fetchUsers() {
      const response = await axios.get(`/api/user/getall`);

      if (response.status !== OK) {
        this.$store.commit("error/setCode", response.status);
        return false;
      }

      // ユーザデータ
      this.users = response.data;
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
      this.loadingFlg = true;

      const targetDate = this.targetDate.clone();
      const response = await axios.post(`/api/workschedule/getalluser`, {
        yearmonth: targetDate.endOf("isoweek").format("YYYYMM")
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

      // 勤務情報再計算
      this.culBasicWorkDay();
      // 基本勤務日数計算
      this.culBasicWorktimeAMonth();
      // 勤務時間計算
      this.culWorktimes();
      // ドーナツグラフ作成(ALL)
      this.createAllProjectWorklistDoughnut();
      // ドーナツグラフ作成
      this.createProjectWorklistDoughnut();
      // 線グラフ
      this.createWorktimeGraph();

      this.loadingFlg = false;
    },

    /** 全ユーザ週報データ取得 */
    async fetchWeeklyReport() {
      const targetDate = this.targetDate.clone();
      const response = await axios.post(`/api/weeklyreport/getalluser`, {
        targetDate: targetDate.format("YYYY-MM-DD"),
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
      const startDate = this.targetDate.clone();
      const endDate = this.targetDate.clone();
      startDate.endOf("isoweek").startOf("month");
      endDate.endOf("isoweek").endOf("month");

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
      const targetDate = this.targetDate.clone();
      const weekStartDate = targetDate.format("YYYY-MM-DD");
      const weekEndDate = targetDate.endOf("isoweek").format("YYYY-MM-DD");

      /** 今月の勤務時間数 */
      this.workschedules.forEach((val_1, idx_1, arr_1) => {
        const user = val_1.user_contract.find(function(element) {
          return (
            (element.startdate <= weekStartDate &&
              weekStartDate <= element.enddate) ||
            (element.startdate <= weekEndDate && weekEndDate <= element.enddate)
          );
        });

        if (!user) {
          this.$set(this.workschedules[idx_1], "workingtimeType", null);
          this.$set(this.workschedules[idx_1], "workingtimeMin", null);
          this.$set(this.workschedules[idx_1], "workingtimeMax", null);
        } else if (user.workingtime_type === 1) {
          this.$set(
            this.workschedules[idx_1],
            "workingtimeType",
            user.workingtime_type
          );
          this.$set(
            this.workschedules[idx_1],
            "workingtimeMin",
            this.basicWorkDay * user.worktime_day
          );
          this.$set(
            this.workschedules[idx_1],
            "workingtimeMax",
            val_1.workingtimeMin + user.maxworktime_month
          );
        } else if (user.workingtime_type === 2) {
          this.$set(
            this.workschedules[idx_1],
            "workingtimeType",
            user.workingtime_type
          );
          this.$set(
            this.workschedules[idx_1],
            "workingtimeMin",
            user.workingtime_min
          );
          this.$set(
            this.workschedules[idx_1],
            "workingtimeMax",
            user.workingtime_max
          );
        }
      });
    },

    /** 全ユーザ合計勤務時間計算 */
    culWorktimes: function() {
      let projectWorktime = [];
      this.workschedules.forEach((val_1, idx_1, arr_1) => {
        val_1.work_schedule.forEach((val_2, idx_2, arr_2) => {
          val_2.project_work.forEach((val_3, idx_3, arr_3) => {
            // プロジェクト時間リスト作成
            projectWorktime.push({
              user_id: val_1.id,
              user_name: val_1.name,
              workdate: val_2.workdate,
              week_number: val_2.week_number,
              project_id: val_3.project_id,
              project_code: val_3.project.code,
              project_name: val_3.project.name,
              worktime: val_3.worktime
            });
          });
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
          this.culWorktingDay(this.allUserWorktimes[idx_1].worktimes)
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

      // プロジェクト毎にgroup化
      // プロジェクト毎にgroup化 ヘッダー
      this.projectWorktimesHeader = projectWorktime.reduce(
        (result, current) => {
          const element = result.find(p => p.project_id === current.project_id);
          if (element) {
            element.count++; // count
            element.worktime += current.worktime; // sum
          } else {
            result.push({
              project_id: current.project_id,
              project_code: current.project_code,
              project_name: current.project_name,
              count: 1,
              worktime: current.worktime
            });
          }
          return result;
        },
        []
      );

      // project_code でソート
      this.projectWorktimesHeader.sort(function(a, b) {
        return a.project_code < b.project_code ? -1 : 1;
      });

      // 総プロジェクト時間計算
      this.grossAllProjectWorktime = this.culGrossAllProjectWorktime();

      // プロジェクト毎にgroup化 詳細
      this.projectWorktimesDetail = projectWorktime.reduce(
        (result, current) => {
          const element = result.find(
            p =>
              p.project_id === current.project_id &&
              p.user_id === current.user_id
          );
          if (element) {
            element.count++; // count
            element.worktime += current.worktime; // sum
          } else {
            result.push({
              project_id: current.project_id,
              project_code: current.project_code,
              project_name: current.project_name,
              user_id: current.user_id,
              user_name: current.user_name,
              count: 1,
              worktime: current.worktime
            });
          }
          return result;
        },
        []
      );

      // プロジェクト毎にgroup化 詳細 パーセント表示追加
      this.projectWorktimesDetail = this.projectWorktimesDetail.map(item => {
        return {
          project_id: item.project_id,
          project_code: item.project_code,
          project_name: item.project_name,
          user_id: item.user_id,
          user_name: item.user_name,
          count: item.count,
          worktime: item.worktime,
          percent:
            this.grossAllProjectWorktime === 0
              ? 0
              : (
                  (item.worktime /
                    this.projectWorktimesHeader.find(function(item2, index2) {
                      return item2.project_id == item.project_id;
                    }).worktime) *
                  100
                ).toFixed(1)
        };
      });

      // project_id でソート
      this.projectWorktimesDetail.sort(function(a, b) {
        return a.project_id < b.project_id ? -1 : 1;
      });
    },

    /** 総プロジェクト時間計算 */
    culGrossAllProjectWorktime() {
      return this.projectWorktimesHeader.length === 1
        ? this.projectWorktimesHeader[0].worktime
        : this.projectWorktimesHeader.reduce(function(total, data, index) {
            return (
              (index === 1 ? total.worktime : total) +
              (index === 1 ? 0 : parseFloat(data.worktime))
            );
          });
    },

    /** 出勤日数計算 */
    culWorktingDay(worktimes) {
      return worktimes.reduce(function(total, data, index) {
        return (index === 1 && total > 0 ? 1 : total) + (data > 0 ? 1 : 0);
      });
    },

    /** 対象週select */
    changeTargetWeek: function() {
      this.targetDate = this.weekNumberToDate(this.targetWeek);
      const targetWeek = this.targetDate.clone();
      this.targetWeek = targetWeek.format("ggggWW");
      this.fetchWorkSchedules();
      this.fetchWeeklyReport();
    }
  },
  watch: {
    $route: {
      async handler() {
        await this.fetchUsers();
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
