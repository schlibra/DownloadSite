<script setup>
import 'mdui/mdui.css'
import 'mdui'
import '@mdui/icons/menu'
import '@mdui/icons/more-vert'
import '@mdui/icons/search'
import 'mdui/components/fab'
import '@mdui/icons/bedtime'
import '@mdui/icons/home'
import '@mdui/icons/backspace'
import { confirm } from 'mdui/functions/confirm'
import {onMounted, ref} from 'vue'
import router from "@/router/index.js";
import {alert} from "mdui";
import axios from "axios";

const isLogin = ref(localStorage.getItem("token"));
const fileCode = ref("");
const device = ref("");
const keys = [
    "1", "2", "3",
    "4", "5", "6",
    "7", "8", "9",
    "G", "0", "D"
]
document.title = "下载站"
onMounted(()=>{
  device.value = /Android|webOS|iPhone|iPod|iPad|BlackBerry/i.test(navigator.userAgent) ? "mobile" : "pc";
  console.log(device.value)
  if (isLogin.value){
    axios.post("/?action=getInfo", {}, {
      headers: {
        Authorization: "Bearer "+isLogin.value
      }
    }).then(res => {
      if (res.data.code !== 200){
        confirm({
          headline: "登录状态失效",
          description: "登录状态失效："+res.data.msg,
          confirmText: "重新登录",
          cancelText: "退出登录",
          onConfirm: () => {
            localStorage.removeItem("token")
            router.push("/login")
          },
          onCancel: () => {
            localStorage.removeItem("token")
            location.reload()
          }
        })
      }
    })
  }
});
function gotoList(){
  router.push({
    path: "/list"
  })
  // window.open("/list", "_self")
}
function login(){
  router.push({
    path: "/login"
  })
}
function register(){
  router.push({
    path: "/register"
  })
}
function user(){
  router.push({
    path: "/user"
  })
}
function logout(){
  confirm({
    headline: "退出登录",
    description: "是否要退出当前账号的登录？",
    confirmText: "确定",
    cancelText: "取消",
    onConfirm: ()=>{
      localStorage.removeItem("token");
      location.reload();
    }
  })
}
function gotoFile(){
  if (fileCode.value>999999||fileCode.value<111111||fileCode.value.length!==6){
    return alert({
      headline: "文件码格式错误",
      description: "文件码必须大于等于111111且小于等于999999",
      confirmText: "确定"
    })
  }
  router.push({
    path: `/s/${fileCode.value}`
  })
}
function keyboardPress(key) {
  if (key==="G"){
    gotoFile();
  }else if (key==="D"){
    if (fileCode.value.length>=1){
      fileCode.value = fileCode.value.substring(0, fileCode.value.length-1);
    }
  }else {
    if (fileCode.value.length<6){
      fileCode.value += `${key}`;
    }
  }
}
</script>

<template>
  <mdui-top-app-bar style="position: fixed;">
    <mdui-button-icon>
      <mdui-icon-home></mdui-icon-home>
    </mdui-button-icon>
    <mdui-top-app-bar-title>下载站</mdui-top-app-bar-title>
    <div style="flex-grow: 1;"></div>
    <mdui-dropdown>
      <mdui-button-icon slot="trigger">
        <mdui-icon-more-vert></mdui-icon-more-vert>
      </mdui-button-icon>
      <mdui-menu>
        <mdui-menu-item v-show="!isLogin" @click="login()">登录</mdui-menu-item>
        <mdui-menu-item v-show="!isLogin" @click="register()">注册</mdui-menu-item>
        <mdui-menu-item v-show="isLogin" @click="user()">个人中心</mdui-menu-item>
        <mdui-menu-item v-show="isLogin" @click="logout()">退出</mdui-menu-item>
      </mdui-menu>
    </mdui-dropdown>
  </mdui-top-app-bar>

  <mdui-layout-main>
    <h1 style="text-align: center;margin-top: 80px;">SCH文件下载站</h1>
    <h3 style="text-align: center;">更方便的分享文件</h3>
    <p style="text-align: center;" @click="gotoList"><mdui-button>文件列表</mdui-button></p>
    <div style="margin-left: 10vw;margin-right: 10vw;margin-top: 4vh;">
      <mdui-text-field type="number" min="111111" max="999999" minlength="6" maxlength="6" counter :inputmode="device==='pc'?'numeric':'none'"  v-model="fileCode" @keydown.enter="gotoFile" clearable label="请输入文件码" autocomplete="off" enterkeyhint="search">
        <mdui-button-icon @click="gotoFile" slot="icon">
          <mdui-icon-search></mdui-icon-search>
        </mdui-button-icon>
      </mdui-text-field>
    </div>
    <div class="keyboard" v-if="device==='mobile'" style="margin-left: 5vw;margin-right: 5vw;margin-top: 24px;">
      <mdui-button @click="keyboardPress(key)" style="width: 28vw;height: 20vw;margin-right: 2px;" v-for="key in keys">
        <mdui-icon-search v-if="key==='G'" />
        <mdui-icon-backspace v-else-if="key==='D'" />
        <span v-else>{{ key }}</span>
      </mdui-button>
    </div>
  </mdui-layout-main>
</template>
