//获取当前时间
function getNowFormatDate() {
  var date = new Date();
  var year = date.getFullYear();
  var month = date.getMonth() + 1;
  var strDate = date.getDate();
  var Hour = date.getHours(); // 获取当前小时数(0-23)
  var Minute = date.getMinutes(); // 获取当前分钟数(0-59)
  var Second = date.getSeconds(); // 获取当前秒数(0-59)
  var show_day = new Array(
    "星期日",
    "星期一",
    "星期二",
    "星期三",
    "星期四",
    "星期五",
    "星期六"
  );
  var day = date.getDay();
  if (Hour < 10) {
    Hour = "0" + Hour;
  }
  if (Minute < 10) {
    Minute = "0" + Minute;
  }
  if (Second < 10) {
    Second = "0" + Second;
  }
  if (month >= 1 && month <= 9) {
    month = "0" + month;
  }
  if (strDate >= 0 && strDate <= 9) {
    strDate = "0" + strDate;
  }
  var currentdate = `<div>${year}年${month}月${strDate}日</div>
                      <div>${show_day[day]}</div>
                    `;
  var HMS = Hour + ":" + Minute + ":" + Second;
  var temp_time = year + "-" + month + "-" + strDate + " " + HMS;
  $(".nowTime li:nth-child(1)").html(HMS);
  $(".nowTime li:nth-child(2)").html(currentdate);
  // setTimeout(getNowFormatDate, 1000); //每隔1秒重新调用一次该函数
}

function getData() {
  var result = {
    data: {},
  };
  let prourl = "http://onebe.ap.freescale.net:9001/api/compound-api/chart";
  let devurl = "http://www.mcdkj.cn/api/compound-api/chart";
  $.ajax({
    url: prourl,
    type: "post",
    dataType: "json",
    data: {},
    async: false,
    success: function (data) {
      if (data.status == true) {
        result.data = data.data;
      } else {
        result.data = {};
      }
    },
    error: function () {
      result.data = {};
    },
  });
  return result;
}

// 当前货架物科情况
function init_myChart1(warning) {
  let data = [];
  let keys = Object.keys(warning);
  for (let i = 0; i < keys.length; i++) {
    const key = keys[i];
    data.push({ value: warning[key], name: key });
  }
  option = {
    tooltip: {
      trigger: "item",
      formatter: "{b} <br/> {c} ({d}%)",
    },
    color: [
      "#8fc31f",
      "#f35833",
      "#00ccff",
      "#ffcc00",
      "#f5e965",
      "#a74faf",
      "#ff9668",
    ],

    series: [
      {
        name: "12小时内过期",
        type: "pie",
        radius: "30%",
        center: ["50%", "50%"],
        data: data,
        itemStyle: {
          normal: {
            label: {
              show: true,
              color: "#ffffff",
              formatter: "{b} : {c}",
              fontSize: 10, //文字的字体大小
            },
          },
          labelLine: { show: true },
        },
      },
    ],
  };
  // 使用刚指定的配置项和数据显示图表。
  let myChart1 = echarts.init(document.getElementById("main1"));
  
  
  myChart1.setOption(option);
}

// 库存情况 by device
function init_myChart2(part_no) {
  let part_no_name = [];
  let part_no_available = [];
  let part_no_thaw = [];
  let keys = Object.keys(part_no);
  for (let i = 0; i < keys.length; i++) {
    const key = keys[i];
    part_no_name.push(key);
    part_no_available.push(part_no[key].available);
    part_no_thaw.push(part_no[key].thaw);
  }
  option = {
    color: ["#00ad4e", "#b0e0e6"],
    tooltip: {
      trigger: "axis",
      axisPointer: {
        type: "shadow",
      },
    },
    grid: {
      top: "10%",
      left: "13%",
      right: "10",
      bottom: "15%",
      containLabel: false,
    },
    legend: {
      data: ["可领取", "解冻中"],
      textStyle: {
        color: "#ffffff",
      },
    },
    xAxis: [
      {
        type: "category",
        axisTick: { show: false },
        data: part_no_name,
        axisLabel: {
          color: "#ffffff",
        },
      },
    ],
    yAxis: [
      {
        type: "value",
        axisLabel: {
          color: "#ffffff",
        },
      },
    ],
    series: [
      {
        name: "可领取",
        type: "bar",
        barGap: 0,
        data: part_no_available,
      },
      {
        name: "解冻中",
        type: "bar",
        data: part_no_thaw,
      },
    ],
  };

  // 使用刚指定的配置项和数据显示图表。
  let myChart2 = echarts.init(document.getElementById("main2"));
  myChart2.setOption(option);
}

// 库存情况 by 状态
function init_myChart3(stock_status) {
  let stock_status_name = [],
    stock_status_data = [];
  let keys = Object.keys(stock_status);
  for (let i = 0; i < keys.length; i++) {
    const key = keys[i];
    stock_status_name.push(key);
    stock_status_data.push(stock_status[key]);
  }
  option = {
    tooltip: {
      trigger: "axis",
    },
    grid: {
      top: "10%",
      left: "13%",
      right: "10",
      bottom: "15%",
      containLabel: false,
    },
    xAxis: [
      {
        type: "category",
        data: stock_status_name,
        axisLabel: {
          color: "#ffffff",
        },
      },
    ],

    yAxis: [
      {
        type: "value",
        axisLabel: {
          color: "#ffffff",
        },
      },
    ],

    series: [
      {
        type: "bar",
        data: stock_status_data,
        itemStyle: {
          normal: {
            color: function (params) {
              const colorllist = ["#00ad4e", "#b0e0e6"];
              return colorllist[params.dataIndex];
            },
          },
        },
      },
    ],
  };

  // 使用刚指定的配置项和数据显示图表。
  let myChart3 = echarts.init(document.getElementById("main3"));
  myChart3.setOption(option);
}

function init_myChart4(broken) {
  let broken_date = [],
    broken_data = [];
  let keys = Object.keys(broken);
  for (let i = 0; i < keys.length; i++) {
    const key = keys[i];
    broken_date.push(key);
    broken_data.push({ count: broken[key] });
  }
  var data = {
    uploadData: broken_data,
  };
  var uploadCnt = [];

  if (data.uploadData != null) {
    for (var i = 0; i < data.uploadData.length; i++) {
      uploadCnt.push(data.uploadData[i].count);
    }
  }
  option = {
    tooltip: {
      trigger: "axis",
      formatter: function (params, ticket, callback) {
        var res = "";
        for (var i = 0, l = params.length; i < l; i++) {
          res += "" + params[i].seriesName + " : " + params[i].value + "<br>";
        }
        return res;
      },
      transitionDuration: 0,
      borderRadius: 8,
      borderWidth: 2,
      padding: [5, 10],
      axisPointer: {
        type: "line",
        lineStyle: {
          type: "dashed",
          color: "#ffff00",
        },
      },
    },
    grid: {
      top: "10%",
      left: "13%",
      right: "10",
      bottom: "15%",
      containLabel: false,
    },
    xAxis: [
      {
        type: "category",
        boundaryGap: false,
        axisLabel: {
          interval: 0,
          fontSize: 14,
          show: true,
          color: "#ffffff",
        },
        axisLine: {
          show: false,
          lineStyle: {
            color: "#ffffff",
          },
        },
        axisTick: {
          show: false,
        },
        data: broken_date,
        offset: 10,
      },
    ],
    yAxis: [
      {
        type: "value",
        axisLine: {
          show: false,
          lineStyle: {
            color: "#a0a8b9",
          },
        },
        axisLabel: {
          margin: 10,
          textStyle: {
            fontSize: 14,
            show: true,
            color: "#ffffff",
          },
        },
        splitLine: {
          lineStyle: {
            color: "#2b3646",
          },
        },
        axisTick: {
          show: false,
        },
      },
    ],
    series: [
      {
        name: "下架情况",
        type: "line",
        // smooth: true,
        showSymbol: true,
        lineStyle: {
          normal: {
            width: 2,
          },
        },
        areaStyle: {
          normal: {
            color: new echarts.graphic.LinearGradient(
              0,
              0,
              0,
              1,
              [
                {
                  offset: 0,
                  color: "rgba(137, 189, 27, 0.3)",
                },
                {
                  offset: 0.8,
                  color: "rgba(137, 189, 27, 0)",
                },
              ],
              false
            ),
            shadowColor: "rgba(0, 0, 0, 0.1)",
            shadowBlur: 10,
          },
        },
        itemStyle: {
          normal: {
            color: "#1cc840",
          },
        },
        data: uploadCnt,
      },
    ],
  };
  let myChart4 = echarts.init(document.getElementById("main4"));
  myChart4.setOption(option);
}
function init_myChart5(scattered) {
  let scattered_name = [],
    scattered_data = [];
  let keys = Object.keys(scattered);
  for (let i = 0; i < keys.length; i++) {
    const key = keys[i];
    scattered_name.push(key);
    scattered_data.push({ count: scattered[key] });
  }
  var data = {
    uploadData: scattered_data,
  };
  var uploadCnt = [];
  if (data.uploadData != null) {
    for (var i = 0; i < data.uploadData.length; i++) {
      uploadCnt.push(data.uploadData[i].count);
    }
  }

  option = {
    tooltip: {
      trigger: "axis",
      formatter: function (params, ticket, callback) {
        var res = "";
        for (var i = 0, l = params.length; i < l; i++) {
          res += "" + params[i].seriesName + " : " + params[i].value + "<br>";
        }
        return res;
      },
      transitionDuration: 0,
      borderRadius: 8,
      borderWidth: 2,
      padding: [5, 10],
      axisPointer: {
        type: "line",
        lineStyle: {
          type: "dashed",
          color: "#ffff00",
        },
      },
    },
    grid: {
      top: "10%",
      left: "13%",
      right: "10",
      bottom: "15%",
      containLabel: false,
    },
    xAxis: [
      {
        type: "category",
        boundaryGap: false,
        axisLabel: {
          interval: 0,
          fontSize: 14,
          show: true,
          color: "#ffffff",
        },
        axisLine: {
          show: false,
          lineStyle: {
            color: "#a0a8b9",
          },
        },
        axisTick: {
          show: false,
        },
        data: scattered_name,
        offset: 10,
      },
    ],
    yAxis: [
      {
        type: "value",
        axisLine: {
          show: false,
          lineStyle: {
            color: "#a0a8b9",
          },
        },
        axisLabel: {
          margin: 10,
          textStyle: {
            fontSize: 14,
            show: true,
            color: "#ffffff",
          },
        },
        splitLine: {
          lineStyle: {
            color: "#2b3646",
          },
        },
        axisTick: {
          show: false,
        },
      },
    ],
    series: [
      {
        name: "零散报废情况",
        type: "line",
        // smooth: true,
        showSymbol: true,
        lineStyle: {
          normal: {
            width: 2,
          },
        },
        areaStyle: {
          normal: {
            color: new echarts.graphic.LinearGradient(
              0,
              0,
              0,
              1,
              [
                {
                  offset: 0,
                  color: "rgba(137, 189, 27, 0.3)",
                },
                {
                  offset: 0.8,
                  color: "rgba(137, 189, 27, 0)",
                },
              ],
              false
            ),
            shadowColor: "rgba(0, 0, 0, 0.1)",
            shadowBlur: 10,
          },
        },
        itemStyle: {
          normal: {
            color: "#1cc840",
          },
        },
        data: uploadCnt,
      },
    ],
  };

  let myChart5 = echarts.init(document.getElementById("main5"));
  myChart5.setOption(option);
}
function init_myChart6(get_up) {
  let get_up_data = []; // 上架数量
  let get_up_date = []; // 上架日期
  let keys = Object.keys(get_up);
  for (let i = 0; i < keys.length; i++) {
    const key = keys[i];
    get_up_date.push(key);
    get_up_data.push({ count: get_up[key] });
  }
  var data = {
    uploadData: get_up_data,
  };
  var uploadCnt = [];
  if (data.uploadData != null) {
    for (var i = 0; i < data.uploadData.length; i++) {
      uploadCnt.push(data.uploadData[i].count);
    }
  }
  option = {
    tooltip: {
      trigger: "axis",
      formatter: function (params, ticket, callback) {
        var res = "";
        for (var i = 0, l = params.length; i < l; i++) {
          res += "" + params[i].seriesName + " : " + params[i].value + "<br>";
        }
        return res;
      },
      transitionDuration: 0,
      borderRadius: 8,
      borderWidth: 2,
      padding: [5, 10],
      axisPointer: {
        type: "line",
        lineStyle: {
          type: "dashed",
          color: "#ffff00",
        },
      },
    },
    grid: {
      top: "10%",
      left: "13%",
      right: "10",
      bottom: "15%",
      containLabel: false,
    },
    xAxis: [
      {
        type: "category",
        boundaryGap: false,
        axisLabel: {
          interval: 0,
          fontSize: 14,
          show: true,
          color: "#ffffff",
        },
        axisLine: {
          show: false,
          lineStyle: {
            color: "#a0a8b9",
          },
        },
        axisTick: {
          show: false,
        },
        data: get_up_date,
        offset: 10,
      },
    ],
    yAxis: [
      {
        type: "value",
        axisLine: {
          show: false,
          lineStyle: {
            color: "#a0a8b9",
          },
        },
        axisLabel: {
          margin: 10,
          textStyle: {
            fontSize: 14,
            show: true,
            color: "#ffffff",
          },
        },
        splitLine: {
          lineStyle: {
            color: "#2b3646",
          },
        },
        axisTick: {
          show: false,
        },
      },
    ],
    series: [
      {
        name: "上架情况",
        type: "line",
        // smooth: true,
        showSymbol: true,
        lineStyle: {
          normal: {
            width: 2,
          },
        },
        areaStyle: {
          normal: {
            color: new echarts.graphic.LinearGradient(
              0,
              0,
              0,
              1,
              [
                {
                  offset: 0,
                  color: "rgba(137, 189, 27, 0.3)",
                },
                {
                  offset: 0.8,
                  color: "rgba(137, 189, 27, 0)",
                },
              ],
              false
            ),
            shadowColor: "rgba(0, 0, 0, 0.1)",
            shadowBlur: 10,
          },
        },
        itemStyle: {
          normal: {
            color: "#1cc840",
          },
        },
        data: uploadCnt,
      },
    ],
  };
  let myChart6 = echarts.init(document.getElementById("main6"));
  myChart6.setOption(option);
}

function init() {
  let result = getData();
  let data = result.data;
  const { broken, get_up, part_no, scattered, stock_status, warning } = data;
  // 基于准备好的dom，初始化echarts实例
  init_myChart1(warning);
  init_myChart2(part_no);
  init_myChart3(stock_status);
  init_myChart4(broken);
  init_myChart5(scattered);
  init_myChart6(get_up);

}
function setsize(){
  let main1_parent_w = $('#main1').parent().width()
  let main1_parent_h = $('#main1').parent().height()
  $('#main1').css({
    width:main1_parent_w,
    height:main1_parent_h
  })
  $('#main2').css({
    width:main1_parent_w,
    height:main1_parent_h
  })
  $('#main3').css({
    width:main1_parent_w,
    height:main1_parent_h
  })
  $('#main4').css({
    width:main1_parent_w,
    height:main1_parent_h
  })
  $('#main5').css({
    width:main1_parent_w,
    height:main1_parent_h
  })
  $('#main6').css({
    width:main1_parent_w,
    height:main1_parent_h
  })
}
$(document).ready(function () {
  let t = null;
  let nowClientWidth = document.documentElement.clientWidth;
  let nowClientHeight = document.documentElement.clientHeight;
  setsize()
  let myChart1 = echarts.init(document.getElementById("main1"));
  let myChart2 = echarts.init(document.getElementById("main2"));
  let myChart3 = echarts.init(document.getElementById("main3"));
  let myChart4 = echarts.init(document.getElementById("main4"));
  let myChart5 = echarts.init(document.getElementById("main5"));
  let myChart6 = echarts.init(document.getElementById("main6"));
  function nowSizeWidth(val, initWidth = 1920) {
    return val * (nowClientWidth / initWidth);
  }
  function nowSizeHigth(val, initHight = 1080) {
    return val * (nowClientHeight / initHight);
  }
  // 获取当前时间
  getNowFormatDate();
  setTimeout(() => {
    init();
    t && clearInterval(t);
    t = setInterval(() => {
      init();
    }, 60 * 1000);
  }, 20);

  
  // 页面改变大小
  window.addEventListener("resize", function () {
    setsize()
    myChart1.resize();
    myChart2.resize();
    myChart3.resize();
    myChart4.resize();
    myChart5.resize();
    myChart6.resize();
  });
});
