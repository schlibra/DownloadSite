<script setup>
import 'mdui/mdui.css'
import 'mdui'
import '@mdui/icons/menu'
import '@mdui/icons/more-vert'
import '@mdui/icons/search'
import 'mdui/components/fab'
import '@mdui/icons/arrow-right'
import '@mdui/icons/bedtime'
import {onMounted, reactive, ref} from 'vue'
import router from "@/router/index.js";
import '@mdui/icons/file-download--outlined'
import '@mdui/icons/error'
import '@mdui/icons/link'
import '@mdui/icons/arrow-back'
import '@mdui/icons/lock'
import axios from "axios";
import {alert, setColorScheme, confirm} from "mdui";

const isLogin = ref(localStorage.getItem("token"));
const isAdmin = ref(false)
const exist = ref(true);
const hasPassword = ref(false);
const link = ref("");
const password = ref("");
const data = ref({});
document.title = "文件详情 | 下载站"
onMounted(()=>{
  if (isLogin.value){
    axios.post("/?action=getInfo", {}, {
      headers: {
        Authorization: "Bearer "+isLogin.value
      }
    }).then(res => {
      if (res.data.code === 200) {
        isAdmin.value = res.data.data.admin === "1";
      }else {
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
  const path = location.href.replace(location.protocol+"//"+location.host,"");
  let s;
  if (path.startsWith("/file")) {
    // setColorScheme("#39BBC5");
    s = new URLSearchParams(location.search).get("s")
  }else if (path.startsWith("/s/")){
    s = path.replace("/s/", "");
  }else {
    exist.value = false;
    return;
  }
  axios.post("/?action=fileInfo", {id: s}).then(res => {
    if (res.data.code === 200) {
      if (res.data.data.hasPassword){
        hasPassword.value = true;
      }
      link.value = res.data.data.link;
      data.value = res.data.data;
    } else {
      exist.value = false;
    }
  })
})
function gotoFile(id){
  router.push({
    path: "/file",
    query: {
      id
    }
  })
}
function gotoHome() {
  router.push({
    path: "/"
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
function getFile() {
  if (password.value.length){
    axios.post("/?action=getFile", {id: link.value, password: password.value}).then(res=>{
      if (res.data.code===200){
        data.value = res.data.data;
        hasPassword.value = false;
      }else {
        alert({
          headline: "提取失败",
          description: "文件提取失败："+res.data.msg,
          confirmText: "确定"
        })
      }
    });
  }else {
    alert({
      headline: "请输入提取码",
      description: "提取码不能为空",
      confirmText: "确定"
    })
  }
}
function gotoDownload(){
  console.log(link.value);
  // return;
  location.href = "/download/" + link.value + (password.value.length ? "?password=" + password.value : "");
}
function copyText(text) {
  if (navigator.clipboard) {
    navigator.clipboard.writeText(text)
  }
}
function goto_tsinbei() {
  if (data.value.tsinbei_pwd!==""){
    copyText(data.value.tsinbei_pwd);
  }
  location.href = data.value.tsinbei;
}
function goto_123() {
  if (data.value.pan123_pwd!==""){
    copyText(data.value.pan123_pwd);
  }
  location.href = data.value.pan123;
}
function goto_lanzou() {
  if (data.value.lanzou_pwd!==""){
    copyText(data.value.lanzou_pwd);
  }
  location.href = data.value.lanzou;
}
function admin() {
  router.push({
    path: "/admin"
  })
}
</script>

<template>
  <mdui-top-app-bar style="position: fixed;">
    <mdui-button-icon>
      <mdui-icon-arrow-back @click="back()"></mdui-icon-arrow-back>
    </mdui-button-icon>
    <mdui-top-app-bar-title>文件详情</mdui-top-app-bar-title>
    <div style="flex-grow: 1;"></div>
    <mdui-dropdown>
      <mdui-button-icon slot="trigger">
        <mdui-icon-more-vert></mdui-icon-more-vert>
      </mdui-button-icon>
      <mdui-menu>
        <mdui-menu-item v-show="!isLogin" @click="login()">登录</mdui-menu-item>
        <mdui-menu-item v-show="!isLogin" @click="register()">注册</mdui-menu-item>
        <mdui-menu-item v-if="isAdmin" @click="admin()">系统设置</mdui-menu-item>
        <mdui-menu-item v-show="isLogin" @click="user()">个人中心</mdui-menu-item>
        <mdui-menu-item v-show="isLogin" @click="logout()">退出</mdui-menu-item>
      </mdui-menu>
    </mdui-dropdown>
  </mdui-top-app-bar>

  <mdui-layout-main>
    <div style="text-align: center;margin-left: 10vw;margin-right: 10vw;margin-top: 80px;" v-if="exist&&!hasPassword">
      <mdui-icon-file-download--outlined style="font-size: 160px"/>
      <h1>{{ data.filename }}</h1>
      <h2>文件大小：{{ data.filesize }}</h2>
      <h2>文件上传时间：{{ data.createTime }}</h2>
      <h3>文件码：{{ link }}</h3>
      <h3>文件上传者：{{ data.nickname }}({{ data.username }})</h3>
      <h3>文件下载量：{{ data.downloadCount }}</h3>
      <h3 v-if="data.description.length>0">文件描述：{{ data.description }}</h3>
      <mdui-button full-width style="margin-bottom: 24px;" @click="gotoDownload()">本站下载</mdui-button>
      <mdui-button @click="goto_tsinbei()" v-if="data.tsinbei!==''" full-width style="margin-bottom: 24px;">清北网盘下载<span v-if="data.tsinbei_pwd!==''">(提取码：{{ data.tsinbei_pwd }})</span><mdui-icon-link /></mdui-button>
      <mdui-button @click="goto_123()" v-if="data.pan123!==''" full-width style="margin-bottom: 24px;">123网盘下载<span v-if="data.pan123_pwd!==''">(提取码：{{ data.pan123_pwd }})</span><mdui-icon-link /></mdui-button>
      <mdui-button @click="goto_lanzou()" v-if="data.lanzou!==''" full-width style="margin-bottom: 24px;">蓝奏云下载<span v-if="data.lanzou_pwd!==''">(提取码：{{ data.lanzou_pwd }})</span><mdui-icon-link /></mdui-button>
    </div>
    <div style="text-align: center;margin-left: 10vw;margin-right: 10vw;margin-top: 80px;" v-if="!exist">
      <mdui-icon-error style="font-size: 160px;color: red"/>
      <h1>文件不存在</h1>
      <mdui-button full-width @click="gotoHome()">返回首页</mdui-button>
    </div>
    <div style="text-align: center;margin-left: 10vw;margin-right: 10vw;margin-top: 80px;" v-if="hasPassword">
      <mdui-icon-lock style="font-size: 160px;color: red"/>
      <h1>请输入提取码</h1>
      <mdui-text-field v-model="password" label="提取码" />
      <mdui-button full-width @click="getFile()" style="margin-top: 8px;">提取文件</mdui-button>
      <mdui-button full-width @click="back()" style="margin-top: 8px;">返回</mdui-button>
    </div>
  </mdui-layout-main>
</template>
