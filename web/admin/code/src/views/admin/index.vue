<template>
  <div class="app-container">
    <div class="infoRow">
      <div v-for="(item,index) in info1" :key="index">
        <p>{{ item.title }}</p>
        <p>{{ item.num }}</p>
      </div>
    </div>
    <div class="infoRow">
      <div v-for="(item,index) in info2" :key="index">
        <p>{{ item.title }}</p>
        <p>{{ item.num }}</p>
      </div>
    </div>

    <el-tabs v-model="activeName" type="card" style="margin-top:40px;" @tab-click="handleClick">
      <el-tab-pane v-for="(item,index) in menus" :key="index" :label="item.title" :name="index.toString()">
        <el-date-picker
          :picker-options="datePickerOptions"
          :clearable="false"
          v-model="date"
          type="daterange"
          format="yyyy 年 MM 月 dd 日"
          value-format="yyyy-MM-dd"
          unlink-panels
          range-separator="至"
          start-placeholder="开始日期"
          end-placeholder="结束日期"
          style="width:450px;"
          @change="changeDate"/>
        <el-radio-group v-model="dateType" style="float:right;" @change="changeDate">
          <el-radio-button label="按日"/>
          <el-radio-button label="按周"/>
          <el-radio-button label="按月"/>
        </el-radio-group>
        <div :id="'main' + index" style="width:100%;height:400px;"/>
      </el-tab-pane>
    </el-tabs>

    <h4 style="margin-top:100px;">用户地域分布图</h4>
    <el-select v-model="mapType" style="display:block;width:200px;" @change="changeMapData">
      <el-option
        v-for="item in mapOptions"
        :key="item.value"
        :label="item.label"
        :value="item.value"/>
    </el-select>
    <div class="map-box">
      <svg id="map" width="500" height="331"/>
      <el-table :data="mapData.data" :row-style="styleRow" @row-click="clickRow">
        <el-table-column prop="name" label="身份"/>
        <el-table-column prop="count" label="用户数"/>
      </el-table>
      <el-table :data="cityDate" style="border:1px solid #ddd;margin-left:30px;">
        <el-table-column prop="name" label="城市" align="center"/>
        <el-table-column prop="count" label="用户数" align="center"/>
      </el-table>
    </div>
  </div>
</template>

<script>
import echarts from 'echarts'
import * as d3 from 'd3'
import { getBaseInfo, getLineInfo, getMapInfo } from '@/api/report'

export default {
  name: 'Admin',
  data() {
    return {
      info1: [
        { title: '今日登录人数', num: null },
        { title: '今日新增用户数', num: null },
        { title: '今日投票量', num: null },
        { title: '今日实名认证人数', num: null },
        { title: '今日新增节点数', num: null }
      ],
      info2: [
        { title: '用户总数', num: null },
        { title: '7日内重复登录人数', num: null },
        { title: '合计投票量', num: null },
        { title: '实名认证总数', num: null },
        { title: '节点总数', num: null }
      ],
      menus: [
        { title: '登录人数' },
        { title: '新增用户数' },
        { title: '新增投票量' },
        { title: '新增实名认证人数' },
        { title: '新增节点数量' }
      ],
      activeName: '0',
      date: [],
      datePickerOptions: {
        shortcuts: [{
          text: '最近7天',
          onClick(picker) {
            const end = new Date()
            const start = new Date()
            start.setTime(start.getTime() - 3600 * 1000 * 24 * 7)
            picker.$emit('pick', [start, end])
          }
        }, {
          text: '最近15天',
          onClick(picker) {
            const end = new Date()
            const start = new Date()
            start.setTime(start.getTime() - 3600 * 1000 * 24 * 15)
            picker.$emit('pick', [start, end])
          }
        }, {
          text: '最近30天',
          onClick(picker) {
            const end = new Date()
            const start = new Date()
            start.setTime(start.getTime() - 3600 * 1000 * 24 * 30)
            picker.$emit('pick', [start, end])
          }
        }]
      },
      dateType: '按日',
      mapData: [],
      mapOptions: [
        { value: 1, label: '注册用户' },
        { value: 2, label: '实名认证用户' },
        { value: 3, label: '实名非节点用户' },
        { value: 4, label: '节点用户' }
      ],
      mapType: 1,
      styleRow: row => {
        if (row.row.name === this.provinceName) return { 'background': '#ebeef5' }
      },
      cityDate: [],
      provinceName: ''
    }
  },
  created() {
    getBaseInfo().then(res => {
      this.info1[0].num = res.content.userLoginNum
      this.info1[1].num = res.content.userCreateNum
      this.info1[2].num = res.content.voteNum
      this.info1[3].num = res.content.identifyNum
      this.info1[4].num = res.content.nodeNum
      this.info2[0].num = res.content.userAllNum
      this.info2[1].num = res.content.repeatLoginNum
      this.info2[2].num = res.content.voteAllNum
      this.info2[3].num = res.content.identifyAllNum
      this.info2[4].num = res.content.nodeAllNum
    })
    const end = new Date()
    const start = new Date()
    start.setTime(start.getTime() - 3600 * 1000 * 24 * 30)
    this.date[0] = start.toLocaleDateString().replace(/\//g, '-')
    this.date[1] = end.toLocaleDateString().replace(/\//g, '-')
    this.handleClick({ name: 0, label: '登录人数' })
  },
  mounted() {
    this.changeMapData()
  },
  methods: {
    // 切换折线图菜单
    handleClick(val) {
      let group = null
      switch (this.dateType) {
        case '按日':
          group = 1
          break
        case '按周':
          group = 2
          break
        case '按月':
          group = 3
          break
      }
      getLineInfo(parseInt(this.activeName) + 1, group, this.date[0], this.date[1]).then(res => {
        var name = 'main' + val.name
        this.$nextTick(() => {
          var myChart = echarts.init(document.getElementById(name))
          myChart.setOption({
            title: {
              text: val.label,
              x: 'center',
              y: 'bottom'
            },
            xAxis: {
              type: 'category',
              data: res.content.date
            },
            yAxis: {
              type: 'value'
            },
            series: [{
              data: res.content.data,
              type: 'line',
              label: {
                normal: {
                  show: true,
                  position: 'top',
                  color: '#888'
                }
              }
            }],
            color: ['#409EFF']
          })
        })
      })
    },
    // 折线图切换日期||类型
    changeDate(val) {
      this.handleClick({ name: this.activeName, label: this.menus[this.activeName].title })
    },
    changeMapData() {
      getMapInfo(this.mapType).then(res => {
        this.mapData = res.content
        this.initMap()
      })
    },
    initMap() {
      d3.json('../../static/china.geojson').then(data => {
        data.features.forEach((item, index, arr) => {
          for (var i = 0; i < this.mapData.data.length; i++) {
            if (item.properties.name === this.mapData.data[i].name) {
              item.properties.count = this.mapData.data[i].count
              item.properties.proportion = this.mapData.data[i].count / this.mapData.allPeople
            }
          }
        })
        var projection = d3.geoMercator()
          .scale(350)
          .center([140, 25])
        var path = d3.geoPath().projection(projection)
        var svg = d3.select('#map')
        var tooltip = d3.select('.app-container')
          .append('div')
          .attr('class', 'tooltip')
          .style('display', 'none')
        svg.selectAll('*').remove()
        svg.selectAll('path')
          .data(data.features)
          .enter()
          .append('path')
          .attr('stroke', '#fff')
          .attr('stroke-width', 1)
          .attr('fill', function(d, i) {
            const choice = d.properties.proportion
            switch (true) {
              case choice > 0 || choice <= 3:
                return '#9ac9ff'
              case choice > 3 || choice <= 6:
                return '#7ab9ff'
              case choice > 6 || choice <= 15:
                return '#46a1ff'
              case choice > 15:
                return '#1086ff'
              default:
                return '#ebf3fc'
            }
          })
          .attr('d', path)
          .on('mouseover', function(d, i) {
            d3.select(this)
              .attr('fill', 'yellow')
            var txt = 0
            if (d.properties.count) txt = d.properties.count
            tooltip.html(`${d.properties.name}：${txt}`)
              .style('left', d3.event.pageX - 250 + 'px')
              .style('top', d3.event.pageY - 100 + 'px')
              .style('display', 'block')
          })
          .on('mouseout', function(d, i) {
            d3.select(this)
              .attr('fill', function(d, i) {
                const choice = d.properties.proportion
                switch (true) {
                  case choice > 0 || choice <= 3:
                    return '#9ac9ff'
                  case choice > 3 || choice <= 6:
                    return '#7ab9ff'
                  case choice > 6 || choice <= 15:
                    return '#46a1ff'
                  case choice > 15:
                    return '#1086ff'
                  default:
                    return '#ebf3fc'
                }
              })
            tooltip.style('display', 'none')
          })
      })
    },
    clickRow(row) {
      this.cityDate = row.child
      this.provinceName = row.name
    }
  }
}
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
.infoRow {
  display: flex;
  justify-content: space-between;
  > div {
    flex: 1;
    p {
      text-align: center;
    }
    p:nth-child(1) {
      color: #888;
    }
    p:nth-child(2) {
      font-size: 24px;
    }
  }
}

.map-box {
  display: flex;
  justify-content: space-around;
  margin-bottom: 50px;
}
</style>
