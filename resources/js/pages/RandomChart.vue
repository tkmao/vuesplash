<template>
  <div class="small">
    <h1>vue-chartjs</h1>
    <div class="half">
      <doughnut-chart :chart-data="doughnutcollection"></doughnut-chart>
    </div>
    <div class="half">
      <line-chart :chart-data="datacollection"></line-chart>
    </div>
    <div>
      <input type="text" v-model="lines">
      <button @click="fillData()">Repaint</button>
      <button @click="randomize()">Randomize</button>
    </div>
  </div>
</template>

<script>
import LineChart from "../LineChart.js";
import DoughnutChart from "../DoughnutChart.js";
export default {
  components: {
    LineChart,
    DoughnutChart
  },
  data() {
    return {
      datacollection: {
        labels: [0, 100],
        datasets: [
          {
            label: "data-1",
            backgroundColor: "rgba(255,100,100,0.1)",
            data: [3, 50]
          },
          {
            label: "data-2",
            backgroundColor: "rgba(255,255,100,0.1)",
            data: [60, 20]
          }
        ]
      },
      doughnutcollection: {
        labels: ["data-1", "data-2"],
        datasets: [
          {
            data: [10, 45],
            backgroundColor: ["rgba(255,10,20,0.4)", "rgba(255,255,20,0.4)"]
          }
        ]
      },
      lines: 3
    };
  },
  mounted() {
    this.fillData();
  },
  methods: {
    fillData() {
      var datasets = [];
      var dDatasets = [];
      var labels = [];
      var val = 0;
      var dLabels = [];
      var dColors = [];
      var min = 0;
      var max = 0;
      var loopCnt = this.lines;
      for (var i = 0; i < loopCnt; i++) {
        labels = [this.getRandomInt(), this.getRandomInt()];
        if (max < labels[0]) {
          max = labels[0];
        }
        if (max < labels[1]) {
          max = labels[1];
        }
        datasets.push({
          label: "data-" + (i + 1),
          backgroundColor: "rgba(255,100,100,0.1)",
          data: labels
        });
        dLabels.push("data-" + (i + 1));
        dDatasets.push(this.getRandomInt());
        var code = i * 20;
        dColors.push("rgba(255," + code + "," + code + ",0.4)");
      }
      this.datacollection = {
        labels: [min, max],
        datasets: datasets
      };
      this.doughnutcollection = {
        labels: dLabels,
        datasets: [
          {
            data: dDatasets,
            backgroundColor: dColors
          }
        ]
      };
    },
    randomize() {
      this.lines = Math.floor(Math.random() * 10) + 1;
      this.fillData();
    },
    getRandomInt() {
      return Math.floor(Math.random() * (50 - 5 + 1)) + 5;
    }
  }
};
</script>

<style>
.small {
  margin: 100px auto;
}
.half {
  width: 45%;
  display: inline-block;
}
</style>