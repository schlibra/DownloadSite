<script setup>
import 'mdui/mdui.css'
import 'mdui'
import '@mdui/icons/menu'
import '@mdui/icons/more-vert'
import '@mdui/icons/search'
import 'mdui/components/fab'
import '@mdui/icons/bedtime'
import '@mdui/icons/home'
import '@mdui/icons/arrow-back'
import { confirm } from 'mdui/functions/confirm'
import {onMounted, ref} from 'vue'
import router from "@/router/index.js";
import {alert} from "mdui";
import axios from "axios";

const isLogin = ref(localStorage.getItem("token"));
const avatar = ref("https://cdn.tsinbei.com/gravatar/avatar/");
const data = ref({});
document.title = "用户中心 | 下载站"
onMounted(()=>{
  if (isLogin.value){
    axios.post("/?action=getInfo",{}, {
      headers: {
        "Authorization": "Bearer " + isLogin.value
      }
    }).then(res=>{
      if (res.data.code === 200){
        data.value = res.data.data;
        data.value.sex = {"1": "男", "2": "女", "3": "其他", "4": "保密"}[data.value.sex];
        avatar.value = "https://cdn.tsinbei.com/gravatar/avatar/" + data.value.hash;
        console.log(res.data);
      }else {
        alert({
          headline: "请求失败",
          description: "页面请求失败：" + res.data.msg,
          confirmText: "确定",
          onConfirm: ()=>{
            localStorage.removeItem("token");
            router.push({
              path: "/login"
            })
          }
        })
      }
    })
  }else {
    alert({
      headline: "未登录账号",
      description: "未登录账号无法访问该页面",
      confirmText: "确定",
      onConfirm: ()=>{
        localStorage.removeItem("token");
        router.push({
          path: "/login"
        })
      }
    })
  }
})
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
function back() {
  router.back();
}
</script>

<template>
  <mdui-top-app-bar style="position: fixed;">
    <mdui-button-icon @click="back()">
      <mdui-icon-arrow-back></mdui-icon-arrow-back>
    </mdui-button-icon>
    <mdui-top-app-bar-title>个人中心</mdui-top-app-bar-title>
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
    <div style="text-align: center;">
      <img :src="avatar" alt="用户头像" style="width: 20vw;border-radius: 50%;margin-top: 48px;" />
      <h1>{{ data.nickname }}</h1>
      <h3>用户名：{{ data.username }}</h3>
      <h3>上传文件数量：{{ data.downloadCount }}</h3>
      <h3>性别：{{ data.sex }}</h3>
      <h3>邮箱地址：{{ data.email }}</h3>
      <mdui-divider />
      <mdui-button style="background-color: #f00;color: white;margin-top: 16px;">注销账号</mdui-button>
    </div>
  </mdui-layout-main>
</template>
