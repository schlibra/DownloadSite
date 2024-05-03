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

const sending = ref(false);
const isLogin = ref(localStorage.getItem("token"));
const username = ref("");
const nickname = ref("");
const password = ref("");
const confirmPwd = ref("");
const email = ref("");
const code = ref("");
const sex = ref("1")
const egg = ref(false);
const eggCount = ref(0);
const admin = ref(false);
const ban = ref(true);
let token = "";
document.title = "用户注册 | 下载站"
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
function back(){
  router.back();
}
function eggAdd(){
  eggCount.value+=1;
  if (eggCount.value===10){
    alert({
      headline: "彩蛋",
      description: "你成功触发了这个页面的彩蛋，接下来将会出现更多的选项",
      confirmText: "确定",
      onConfirm: ()=>{
        egg.value=true;
      }
    })
  }
}
function sendMail(){
  sending.value = true;
  axios.post("/?action=sendMail", {mail: email.value}).then(res=>{
    sending.value = false;
    if (res.data.code===200){
      token = res.data.token;
      alert({
        headline: "发送成功",
        description: "邮件发送成功，请及时查看您的邮箱",
        confirmText: "确定"
      })
    }else {
      alert({
        headline: "发送失败",
        description: "邮件发送失败："+res.data.msg,
        confirmText: "确定"
      })
    }
  });
}
function doRegister(){
  axios.post("/?action=register", {
    username: username.value,
    nickname: nickname.value,
    password: password.value,
    confirm: confirmPwd.value,
    email: email.value,
    code: code.value,
    sex: sex.value,
    admin: admin.value,
    ban: ban.value
  }, {
    headers: {
      "Authorization": "Bearer " + token
    }
  }).then(res=>{
    if (res.data.code===200){
      localStorage.setItem("token", res.data.token)
      alert({
        headline: "注册成功",
        description: "账号注册成功",
        confirmText: "确定",
        onConfirm: ()=>{
          router.push({
            path: "/"
          })
        }
      })
    }else {
      alert({
        headline: "注册失败",
        description: "账号注册失败："+res.data.msg,
        confirmText: "确定"
      })
    }
  })
}
</script>

<template>
  <mdui-top-app-bar style="position: fixed;">
    <mdui-button-icon>
      <mdui-icon-arrow-back @click="back()"></mdui-icon-arrow-back>
    </mdui-button-icon>
    <mdui-top-app-bar-title @click="eggAdd()">用户注册</mdui-top-app-bar-title>
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
      <h1 @click="eggAdd">用户注册</h1>
      <mdui-text-field label="用户名" clearable v-model="username" />
      <mdui-text-field label="密码" type="password" toggle-password clearable v-model="password" />
      <mdui-text-field label="确认密码" type="password" toggle-password clearable v-model="confirmPwd" />
      <mdui-text-field label="昵称" clearable v-model="nickname" />
      <div>
        <mdui-text-field v-model="email" style="width: 65vw;float: left;" inputmode="email" label="邮箱" type="email"></mdui-text-field>
        <mdui-button @click="sendMail()" style="width: 13vw;margin-top: 8px;float: left;" :disabled="sending">
          <span v-show="!sending">发送验证码</span>
          <mdui-circular-progress v-show="sending"></mdui-circular-progress>
        </mdui-button>
      </div>
      <mdui-text-field label="邮箱验证码" inputmode="numeric" v-model="code"></mdui-text-field>
      <mdui-radio-group :value="sex" style="text-align: left;width: 100%;">
        <span>性别：</span>
        <mdui-radio style="position: relative;top: 12px" value="1">男</mdui-radio>
        <mdui-radio style="position: relative;top: 12px" value="2">女</mdui-radio>
        <mdui-radio style="position: relative;top: 12px" value="3">其他</mdui-radio>
        <mdui-radio style="position: relative;top: 12px" value="4">保密</mdui-radio>
      </mdui-radio-group>
      <mdui-divider style="margin-top: 8px;" v-show="egg"/>
      <div style="width: 100%;text-align: left;" v-show="egg">
        <span>设为管理员：</span>
        <mdui-switch :checked="admin" disabled style="position: relative;top: 6px;"></mdui-switch>
      </div>
      <mdui-divider style="margin-top: 8px;" v-show="egg"/>
      <div style="width: 100%;text-align: left;" v-show="egg">
        <span>封禁用户：</span>
        <mdui-switch style="position: relative;top: 6px;" :checked="ban" disabled></mdui-switch>
      </div>
      <mdui-button full-width style="margin-top: 32px;" @click="doRegister()">注册</mdui-button>
    </div>
  </mdui-layout-main>
</template>
