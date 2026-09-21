<?php

use yii\helpers\Html;
use yii\helpers\Url;
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>智能Compound供给系统</title>
    <link rel="stylesheet" href="./css/keepGrain.css" />
  </head>
  <body>
    <div class="contanier">
      <div class="header">
        <img src="./image/nxp.png" />
        智能Compound供给系统
      </div>
      <div class="content" id="goodscontent">
        <!-- <div class="goods">
          <div class="shelfNo">| 货架 一</div>

          <div class="gooddoor">
            <div class="progress">
              <div class="vline"></div>
            </div>
            <div class="stockno">a01</div>
          </div>
          可领取
          <div class="gooddoor klq">
            <div class="progress">
              <div class="vline"></div>
            </div>
          </div>
          已过期
          <div class="gooddoor ygq">
            <div class="progress">
              <div class="vline"></div>
            </div>
          </div>
          即将过期
          <div class="gooddoor jjgq">
            <div class="progress">
              <div class="vline"></div>
            </div>
            <div class></div>
          </div>
          正在领取
          <div class="gooddoor zzlq">
            <div class="progress">
              <div class="vline"></div>
            </div>
            <div class="goodinfo">
              <div class="name">张三</div>
              <div class="goodnum">123456789987654321</div>
            </div>
          </div>
          <div class="gooddoor"></div>
        </div> -->
      </div>
      <div class="footer">
        <img src="./image/klq.png" />
        <span>可领取</span>
        <img src="./image/kx.png" />
        <span>空闲</span>
        <img src="./image/ygq.png" />
        <span>已过期</span>
        <img src="./image/jjgq.png" />
        <span>即将过期</span>
        <img src="./image/zzlq.png" />
        <span>正在领取</span>
      </div>
    </div>
  </body>
  <script src="./js/jquery.min.js"></script>
  <script src="./js/keepGrain.js"></script>
</html>
