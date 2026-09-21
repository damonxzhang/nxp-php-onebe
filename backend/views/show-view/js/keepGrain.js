function getData() {
  var result = {
    flag: false,
    data: [],
  };
  $.ajax({
    url: "http://www.mcdkj.cn/api/shelf/status-info",
    type: "post",
    data: {
      floor: 2,
    },
    async: false,
    success: function (data) {
      result.data = data;
      result.flag = false;
    },
    complete: function () {
      result.data = [];
      result.flag = false;
    },
  });
  console.log("result:", result);
  return result;
}
function sethtml(data) {
  var htmlstr = $("#goodscontent").html();
  console.log("htmlstr:", htmlstr);
  if (!htmlstr) {
    // 插入
  } else {
    // 更新
  }
}
$(document).ready(function () {
  //   var flag = false; // 是否正在请求中
  //   var data = [];
  //   var result = getData();
  //   flag = result.flag;
  //   data = result.data;
  //   sethtml(data);
  //   setInterval(() => {
  //     if (!flag) {
  //       flag = true;
  //       var result = getData();
  //       flag = result.flag;
  //       data = result.data;
  //       sethtml(data);
  //     }
  //   }, 1000);
  let str = "";
  for (let i = 0; i < 6; i++) {
    str += `<div class="goods">`;
    for (let j = 0; j < 6; j++) {
      if (i == 5) {
        str += `
        <div class="gooddoor">
            <div class="progress">
            <div class="vline"></div>
            </div>
            <div class="stockno">Y0${i + 1}-0${j + 1}</div>
        </div>
      `;
      } else {
        str += `
        <div class="gooddoor">
            <div class="progress">
            <div class="vline"></div>
            </div>
            <div class="stockno">G0${i + 1}-0${j + 1}</div>
        </div>
      `;
      }
    }
    str += ` </div>`;
  }
  $("#goodscontent").html(str);
});
