function getData() {
  var result = {
    flag: false,
    data: [],
  };
  let prourl = "http://onebe.ap.freescale.net:9001/api/shelf/status-info";
  let devurl = "http://www.mcdkj.cn/api/shelf/status-info";
  $.ajax({
    url: prourl,
    type: "post",
    dataType: "json",
    data: {
      floor: 1,
    },
    async: false,
    success: function (data) {
      result.data = data.data.shelf;
      result.flag = false;
    },
    error: function () {
      result.data = [];
      result.flag = false;
    },
  });
  return result;
}
function sethtml(data) {
  // 更新状态
  let statusMap = {
    1: "可领取",
    2: "醒料中",
    3: "空闲",
    4: "异常",
    5: "预警中",
    6: "过期",
    7: "领取中",
    10: "初始",
  };
  for (let i = 0; i < data.length; i++) {
    const item = data[i];
    let { column, row, status, region } = item;
    // 行和列都存在
    if (column && row) {
      let idaStr = getidstr(parseInt(row), parseInt(column));
      let name = getname(parseInt(row), parseInt(column));
      if ($(`#${idaStr}`)) {
        if (status == "3" || region == "B" || region == "N") {
          if ($(`#${idaStr}`).hasClass("normal")) {
            continue;
          } else {
            $(`#${idaStr}`).removeClass("klq jjgq ygq zzlq").addClass("normal");
            let htmlstr = ` <div class="progress">
                              <div class="vline"></div>
                              <div class="stockno">${name}</div>
                            </div>`;
            $(`#${idaStr}`).html(htmlstr);
          }
        } else if (status == "1") {
          if ($(`#${idaStr}`).hasClass("klq")) {
            // 修改可领取
          } else {
            $(`#${idaStr}`).removeClass("normal jjgq ygq zzlq").addClass("klq");
            let htmlstr = ` <div class="progress">
                              <div class="vline"></div>
                              <div class="stockno">${name}</div>
                            </div>`;
            $(`#${idaStr}`).html(htmlstr);
          }
        } else if (status == "5") {
          if ($(`#${idaStr}`).hasClass("jjgq")) {
          } else {
            $(`#${idaStr}`).removeClass("klq normal ygq zzlq").addClass("jjgq");
            let htmlstr = `<div class="progress">
                            <div class="vline"></div>
                            <div class="stockno">${name}</div>
                          </div>`;
            $(`#${idaStr}`).html(htmlstr);
          }
        } else if (status == "6") {
          if ($(`#${idaStr}`).hasClass("ygq")) {
            // 修改已过期
          } else {
            $(`#${idaStr}`).removeClass("klq jjgq normal zzlq").addClass("ygq");
            let htmlstr = `<div class="progress">
                              <div class="vline"></div>
                              <div class="stockno">${name}</div>
                            </div>`;
            $(`#${idaStr}`).html(htmlstr);
          }
        } else if (status == "7") {
          // 修改正在领取
          if ($(`#${idaStr}`).hasClass("zzlq")) {
            // let pdom = $(`#${idaStr}`).children(".progress")[0];
            // let va = $(pdom).attr("data_value");
            // let newva = parseFloat(va) ;
            // if (newva < 100) {
            //   $(pdom).attr("data_value", newva);
            //   $(pdom).css({
            //     width: `${newva}%`,
            //   });
            // }
          } else {
            $(`#${idaStr}`).removeClass("klq jjgq ygq normal").addClass("zzlq");
            let str = `<div class="progress" data_value = "20" style="width:50%">
                        <div class="vline"></div>
                      </div>
                      <div class="goodinfo">
                        <div class="name">${item.apply.chinese_name}</div>
                        <div class="goodnum">${item.apply.part_no}</div>
                      </div>`;
            $(`#${idaStr}`).html(str);
          }
        } else {
          if ($(`#${idaStr}`).hasClass("normal")) {
            continue;
          } else {
            $(`#${idaStr}`).removeClass("klq jjgq ygq zzlq").addClass("normal");
            let htmlstr = ` <div class="progress">
                              <div class="vline"></div>
                              <div class="stockno">${name}</div>
                            </div>`;
            $(`#${idaStr}`).html(htmlstr);
          }
        }
      }
    }
  }
}
function initHtml() {
  let str = "";
  // 列
  for (let i = 0; i < 6; i++) {
    str += `<div class="goods">`;
    // 行
    for (let j = 0; j < 6; j++) {
      let column = i + 1; // 列
      let row = j + 1; // 行
      let idStr = getidstr(row, column); // 列_行 组成的id
      let name = getname(row, column);
      str += `<div class="gooddoor" id="${idStr}">
                  <div class="progress">
                  <div class="vline"></div>
                  </div>
                  <div class="stockno">${name}</div>
              </div>`;
    }
    str += ` </div>`;
  }
  $("#goodscontent").html(str);
}
/**
 * 更具行和列获取名称
 * @param {number} row 行
 * @param {number} column 列
 */
function getname(row, column) {
  let name = "";
  if (column == 6) {
    name = `Y01-0${row}`;
  } else {
    name = `G0${column}-0${row}`;
  }
  return name;
}
/**
 * 根据行和列货期id字符串
 * @param {number} row
 * @param {number} column
 */
function getidstr(row, column) {
  return `${column}_${row}`;
}
$(document).ready(function () {
  initHtml();
  setTimeout(() => {
    var flag = true; // 是否正在请求中
    var data = [];
    var result = getData();
    flag = false;
    data = result.data;
    sethtml(data);
    let t = setInterval(() => {
      if (!flag) {
        flag = true;
        var result = getData();
        flag = false;
        data = result.data;
        sethtml(data);
      } else {
        clearInterval(t);
      }
    }, 5000);
  }, 50);
});
