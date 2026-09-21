function getData() {
  var result = {
    data: {},
  };
  let prourl =
    "http://onebe.ap.freescale.net:9001/api/compound-api/compound-show-area";
  let devurl = "http://www.mcdkj.cn/api/compound-api/compound-show-area";
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
/**
 *
 * @param {*} id
 * @param {*} data
 * @param {*} lsq 是否是领取区 领取区的下标从1开始
 */
function setData(id, data) {
  for (let i = 0; i < $(`#${id} li`).length; i++) {
    let dindex = i + 1;
    let dtext = data[dindex];
    const item = $(`#${id} li`)[i];
    let cdiv = $(item).children("div")[0];
    if (cdiv && dtext) {
      // 更新内容
      let t = $(cdiv).text();
      if (t != dtext) {
        $(cdiv).text(dtext);
      }
    } else if (cdiv && !dtext) {
      // 上一次存在，这次不存在
      $(cdiv).remove();
    } else if (!cdiv && dtext) {
      let html = `<div class="titlename">${dtext}</div>`;
      $(item).append(html);
    }
  }
}
function setDataReverse(id, data) {
  let len = $(`#${id} li`).length;
  for (let i = 0; i < $(`#${id} li`).length; i++) {
    let dindex = i + 1;
    let dtext = data[dindex];
    const item = $(`#${id} li`)[len - i - 1];
    let cdiv = $(item).children("div")[0];
    if (cdiv && dtext) {
      // 更新内容
      let t = $(cdiv).text();
      if (t != dtext) {
        $(cdiv).text(dtext);
      }
    } else if (cdiv && !dtext) {
      // 上一次存在，这次不存在
      $(cdiv).remove();
    } else if (!cdiv && dtext) {
      let html = `<div class="titlename">${dtext}</div>`;
      $(item).append(html);
    }
  }
}
function setHtml(data) {
  //   解冻区
  let bdata = data.B;
  if (bdata) {
    setData("thawdata", bdata);
  }

  //   空闲区
  let ndata = data.N;
  if (ndata) {
    setData("freeaData", ndata);
  }
  //   领取区
  let gdata = data.G;
  if (gdata) {
    // setData("lqqdata", gdata);
    setDataReverse("lqqdata", gdata);
  }
  //   临时去
  let ydata = data.Y;
  if (ydata) {
    setData("lsqdata", ydata);
  }
}
$(document).ready(function () {
  var flag = true; // 是否正在请求中
  let data = getData();
  flag = false;
  setHtml(data.data);
  let t = setInterval(() => {
    if (!flag) {
      flag = true;
      let result = getData();
      flag = false;
      data = result.data;
      setHtml(data);
    } else {
      clearInterval(t);
    }
  }, 2000);
});
