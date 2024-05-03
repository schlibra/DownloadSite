<script setup>
import 'mdui/mdui.css'
import 'mdui'
import '@mdui/icons/menu'
import '@mdui/icons/more-vert'
import '@mdui/icons/search'
import 'mdui/components/fab'
import '@mdui/icons/bedtime'
import '@mdui/icons/arrow-back'
import { confirm } from 'mdui/functions/confirm'
import { ref } from 'vue'
import router from "@/router/index.js";
import axios from 'axios'
import {alert} from "mdui";

const isLogin = ref(localStorage.getItem("token"));
const username = ref("");
const password = ref("");
document.title = "用户登录 | 下载站"
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
function doLogin(){
  axios.post("/?action=login", {
    username: username.value,
    password: password.value
  }).then(res=>{
    if (res.data.code===200){
      localStorage.setItem("token", res.data.token);
      alert({
        headline: "登录成功",
        description: res.data.msg,
        confirmText: "确定",
        onConfirm: ()=>{
          router.push({
            path: "/"
          })
        }
      })
    }else {
      alert({
        headline: "登录失败",
        description: res.data.msg,
        confirmText: "确定"
      })
    }
    console.log(res);
  })
}
function back(){
  router.back();
}
</script>

<template>
  <mdui-top-app-bar style="position: fixed;">
    <mdui-button-icon>
      <mdui-icon-arrow-back @click="back()"></mdui-icon-arrow-back>
    </mdui-button-icon>
    <mdui-top-app-bar-title>用户登录</mdui-top-app-bar-title>
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
    <div style="text-align: center;margin-left: 10vw;margin-right: 10vw;">
      <h1>用户登录</h1>
      <mdui-text-field label="用户名" clearable v-model="username" />
      <mdui-text-field label="密码" type="password" toggle-password clearable v-model="password" />
      <mdui-button full-width style="margin-top: 32px;" @click="doLogin()">登录</mdui-button>
    </div>
  </mdui-layout-main>
</template>
