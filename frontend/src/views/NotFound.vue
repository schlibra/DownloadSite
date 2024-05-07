<script setup>
import 'mdui/mdui.css'
import 'mdui'
import '@mdui/icons/menu'
import '@mdui/icons/more-vert'
import '@mdui/icons/search'
import 'mdui/components/fab'
import '@mdui/icons/arrow-right'
import '@mdui/icons/bedtime'
import {onMounted, ref} from 'vue'
import router from "@/router/index.js";
import '@mdui/icons/arrow-back'
import '@mdui/icons/lock'
import '@mdui/icons/cancel--outlined'
import axios from "axios";
import {alert} from "mdui";
import {confirm} from "mdui/functions/confirm.js";

const isLogin = ref(localStorage.getItem("token"));
const list = ref([]);
document.title = "页面不见了 | 下载站"
onMounted(()=>{
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
  axios.post("/?action=fileList").then(res=>{
    if (res.data.code===200){
      list.value = res.data.data;
    }else {
      alert({
        headline: "文件列表获取失败",
        description: "文件列表获取失败：" + res.data.msg,
        confirmText: "确定"
      })
    }
  })
});
function gotoFile(s){
  router.push({
    path: `/s/${s}`,
  })
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
function back(){
  router.back();
}
function gotoHome() {
  router.push({
    path: "/"
  })
}
</script>

<template>
  <mdui-top-app-bar style="position: fixed;">
    <mdui-button-icon>
      <mdui-icon-arrow-back @click="back()"></mdui-icon-arrow-back>
    </mdui-button-icon>
    <mdui-top-app-bar-title>找不到页面</mdui-top-app-bar-title>
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

  <mdui-layout-main style="position: fixed; top: 80px;width: 100vw;">
    <div style="text-align: center;margin-left: 10vw;margin-right: 10vw;">
      <mdui-icon-cancel--outlined style="font-size: 240px;color: red"/>
      <h1>找不到该页面</h1>
      <mdui-button full-width @click="gotoHome()">返回首页</mdui-button>
    </div>
  </mdui-layout-main>
</template>
