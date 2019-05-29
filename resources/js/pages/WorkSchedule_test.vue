<template>
  <div class="work-schedule-test">
    <v-app id="inspire">
      <div>
        <v-toolbar flat color="white">
          <v-toolbar-title>勤務表</v-toolbar-title>
          <v-divider class="mx-2" inset vertical></v-divider>
          <v-spacer></v-spacer>
        </v-toolbar>

        <!-- <p>{{ worktime_sum }}</p> -->

        <v-data-table
          :headers="tableheaders"
          :items="workschedules"
          :rows-per-page-items="[]"
          :pagination.sync="pagination"
          class="elevation-1"
        >
          <template v-slot:items="props">
            <td width="10%">{{ props.item.workdate }}</td>
            <td width="3%">
              <v-checkbox v-model="props.item.is_paid_holiday"></v-checkbox>
            </td>
            <td width="15%">
              <div style="display:flex;">
                <input
                  v-model="starttimes_hh[props.index]"
                  type="Number"
                  min="0"
                  max="30"
                  maxlength="2"
                >:
                <input
                  v-model="starttimes_mm[props.index]"
                  type="Number"
                  min="0"
                  max="30"
                  maxlength="2"
                >
              </div>
            </td>
            <td width="15%">
              <div style="display:flex;">
                <input
                  v-model="endtimes_hh[props.index]"
                  type="Number"
                  min="0"
                  max="30"
                  maxlength="2"
                >:
                <input
                  v-model="endtimes_mm[props.index]"
                  type="Number"
                  min="0"
                  max="30"
                  maxlength="2"
                >
              </div>
            </td>
            <td width="7%">
              <input v-model="breaktimes[props.index]" type="Number" min="0" step="0.25">
            </td>
            <td width="7%">
              <input v-model="breaktimes_midnight[props.index]" type="Number" min="0" step="0.25">
            </td>
            <td width="7%">{{ worktime_day(props.index) }}</td>
            <td width="7%">{{ props.index }}</td>
            <td width="30%">
              <v-textarea
                solo
                v-model="props.item.detail"
                name="input-7-4"
                label="Solo textarea"
                value="詳細"
              ></v-textarea>
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
      workschedules: [],
      worktimes: [1, 2],
      starttimes_hh: [],
      starttimes_mm: [],
      endtimes_hh: [],
      endtimes_mm: [],
      breaktimes: [],
      breaktimes_midnight: [],
      yearmonth: "201905",
      pagination: { rowsPerPage: 200 },
      rules: {
        required: value => !!value || "This field is required."
      },
      tableheaders: [
        { text: "日付", sortable: false, value: "workdate" },
        { text: "有給", sortable: false, value: "is_paid_holiday" },
        { text: "開始時間", sortable: false, value: "starttime_hh" },
        { text: "終了時間", sortable: false, value: "endtime_hh" },
        { text: "休憩時間(h)", sortable: false, value: "breaktime" },
        { text: "深夜休憩時間", sortable: false, value: "breaktime_midnight" },
        { text: "勤務時間", sortable: false, value: this.worktime_day },
        { text: "PJ合計時間", sortable: false, value: "paid_holiday" },
        { text: "内容", sortable: false, value: "is_admin" }
      ]
    };
  },
  computed: {
    formTitle() {
      return 1;
    }
    /*
    worktime_sum: function() {
      console.log("test : ", this.worktimes);
      console.log(
        "test2 : ",
        this.worktimes.reduce(function(total, data) {
          return total + data;
        })
      );
      //let arrayworktime = this.worktimes;
      return this.worktimes.reduce(function(total, data) {
        return total + data;
      });
    }
    */
  },
  methods: {
    initialize() {
      const today = new Date();
      this.yearmonth =
        ("000" + String(today.getFullYear())).slice(-4) +
        ("0" + String(today.getMonth())).slice(-2);
      console.log("initialize yearmonth:", this.yearmonth);
    },

    async fetchWorkSchedules() {
      const response = await axios.get(
        `/api/workschedule/get/${this.yearmonth}`
      );

      if (response.status !== OK) {
        this.$store.commit("error/setCode", response.status);
        return false;
      }

      let worktime_array = Array(response.data.length);
      console.log("aaray 1 : ", worktime_array);
      worktime_array.fill(0);
      console.log("aaray 2 : ", worktime_array);
      this.worktimes = worktime_array;

      // 勤務開始時間hh
      let starttimes_hh_array = [];
      let starttimes_mm_array = [];
      let endtimes_hh_array = [];
      let endtimes_mm_array = [];
      let breaktimes_array = [];
      let breaktimes_midnight_array = [];

      var worktime_array2 = response.data.map(function(element, index, array) {
        starttimes_hh_array[index] = element.starttime_hh;
        starttimes_mm_array[index] = element.starttime_mm;
        endtimes_hh_array[index] = element.endtime_hh;
        endtimes_mm_array[index] = element.endtime_mm;
        breaktimes_array[index] = element.breaktime;
        breaktimes_midnight_array[index] = element.breaktime_midnight;
        return 0;
      });

      this.starttimes_hh = starttimes_hh_array;
      this.starttimes_mm = starttimes_mm_array;
      this.endtimes_hh = endtimes_hh_array;
      this.endtimes_mm = endtimes_mm_array;
      this.breaktimes = breaktimes_array;
      this.breaktimes_midnight = breaktimes_midnight_array;
      console.log("worktime_array2", worktime_array2);

      this.workschedules = response.data;
    },

    async store(editedItem) {
      console.log("storeuser");
      /*
      const response = await axios.post(`/api/user/${editedItem.id}/store`, {
        id: editedItem.id,
        name: editedItem.name,
        email: editedItem.email,
        usertype_id: editedItem.usertype_id,
        workingtime_type: editedItem.workingtime_type,
        worktime_day: editedItem.worktime_day,
        maxworktime_month: editedItem.maxworktime_month,
        workingtime_min: editedItem.workingtime_min,
        workingtime_max: editedItem.workingtime_max,
        hiredate: editedItem.hiredate,
        paid_holiday: editedItem.paid_holiday,
        is_admin: editedItem.is_admin,
        is_deleted: editedItem.is_deleted
      });
      */

      if (response.status !== OK) {
        this.$store.commit("error/setCode", response.status);
        return false;
      }
    },

    worktime_day: function(index) {
      let worktime_array_temp = this.worktimes;
      let worktime = getWorktime(
        this.starttimes_hh[index],
        this.starttimes_mm[index],
        this.endtimes_hh[index],
        this.endtimes_mm[index],
        this.breaktimes[index],
        this.breaktimes_midnight[index]
      );
      worktime_array_temp[index] = worktime;
      console.log("splice before : ", worktime, " index :", index);
      this.worktimes = worktime_array_temp;
      //this.worktimes.splice(index, 1, worktime);
      //this.$set(this.worktimes, index, worktime);
      //this.worktimes.$set(index, worktime);
      console.log("splice after : ", worktime, " index :", index);

      return this.worktimes[index];
    },

    pj_worktime_day: function() {
      console.log("pj_worktime_day :");
      return this.endtime_hh + this.endtime_hh;
    }
  },
  watch: {
    $route: {
      async handler() {
        await this.fetchWorkSchedules();
      },
      immediate: true
    }
  }
};
</script>
