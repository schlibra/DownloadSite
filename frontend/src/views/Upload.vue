<script setup>
import 'mdui/mdui.css'
import 'mdui'
import '@mdui/icons/menu'
import '@mdui/icons/more-vert'
import '@mdui/icons/search'
import 'mdui/components/fab'
import '@mdui/icons/bedtime'
import '@mdui/icons/arrow-back'
import '@mdui/icons/backspace'
import { confirm } from 'mdui/functions/confirm'
import {onMounted, ref} from 'vue'
import router from "@/router/index.js";
import {alert} from "mdui";
import axios from "axios";

const isLogin = ref(localStorage.getItem("token"));
const fileCode = ref("");
const device = ref("");
const uploadProgress = ref(0);
const uploading = ref(false);
const filename = ref("");
const description = ref("");
const filesize = ref("");
const filesizeText = ref("")
const tsinbei = ref("");
const tsinbei_pwd = ref("");
const lanzou = ref("");
const lanzou_pwd = ref("");
const pan123 = ref("");
const pan123_pwd = ref("");
const password = ref("");
const blobList = ref([]);
const isSuccess = ref(false);

document.title = "上传文件 | 下载站"
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
  device.value = /Android|webOS|iPhone|iPod|iPad|BlackBerry/i.test(navigator.userAgent) ? "mobile" : "pc";
  console.log(device.value)
  if (!isLogin.value){
    alert({
      headline: "未登录用户",
      description: "未登录用户，无法上传文件",
      confirmText: "确定",
      closeOnEsc: true,
      onConfirm: () => {
        router.push({
          path: "/login"
        })
      }
    })
  }
});
function back(){
  router.back();
}
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
function upload() {
  uploading.value = true
  let file = document.getElementById("file").files[0];
  filesize.value = file.size;
  filesizeText.value = file.size;
  let prefix_list = ["B", "KB", "MB", "GB", "TB"];
  let prefix_count = 0;
  while (filesizeText.value>1024){
    filesizeText.value/=1024;
    prefix_count+=1;
  }
  filesizeText.value = (Math.round(filesizeText.value*100)/100) + prefix_list[prefix_count];
  let list = [];
  console.log(file)
  const pieceSize = 1024 * 1024 * 2;
  const pieceCount = Math.ceil(file.size / pieceSize);
  sendBlob();
  function sendBlob(i=0) {
    let piece = file.slice(pieceSize * i, pieceSize * (i+1));
    let formData = new FormData();
    formData.append("file", piece);
    console.log(piece)
    axios.post("/?action=upload", formData).then(res => {
      list[i] = res.data.hash;
      console.log(res);
      uploadProgress.value = list.length / pieceCount;
      if (list.length === pieceCount) {
        uploading.value = false
        filename.value = file.name;
        blobList.value = list;
        alert({
          headline: "上传完成",
          confirmText: "确定"
        })
      }else {
        sendBlob(i+1);
      }
    }).then(err => {
      if (err) {
        alert({
          headline: "上传失败",
          description: err,
          confirmText: "确定"
        })
        console.log(err)
      }
    })
  }
  for (let i = 0; i < pieceCount; ++i) {

  }
  // formData.append("file", file)
}
function submitFile() {
  let data = {
    filename: filename.value,
    filesize: filesize.value,
    tsinbei: tsinbei.value,
    pan123: pan123.value,
    lanzou: lanzou.value,
    password: password.value,
    blob: blobList.value,
    description: description.value,
    tsinbei_pwd: tsinbei_pwd.value,
    pan123_pwd: pan123_pwd.value,
    lanzou_pwd: lanzou_pwd.value
  };
  axios.post("/?action=submitFile", data, {
    headers: {
      Authorization: "Bearer " + isLogin.value
    }
  }).then(res => {
    if (res.data.code===200){
      alert({
        headline: "上传成功",
        description: "文件提交成功！",
        confirmText: "确定",
        onConfirm: ()=>{
          router.back();
        }
      })
    }
    console.log(res)
  })
}
</script>

<template>
  <mdui-top-app-bar style="position: fixed;">
    <mdui-button-icon @click="back()">
      <mdui-icon-arrow-back></mdui-icon-arrow-back>
    </mdui-button-icon>
    <mdui-top-app-bar-title>上传文件</mdui-top-app-bar-title>
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

  <mdui-layout-main style="position: fixed; top: 80px">
    <div style="margin-left: 8vw;margin-right: 8vw;">
      <h1>上传文件</h1>
      <h2>上传的文件必须遵守相应法律法规，否则将删除用户的所有文件并封禁该账号</h2>
      <input style="margin-bottom: 8px;" type="file" id="file" name="file" @change="upload()"/>
      <mdui-linear-progress :value="uploadProgress" v-show="uploading"></mdui-linear-progress>
      <mdui-text-field label="文件名" v-model="filename"/>
      <mdui-text-field label="文件描述" v-model="description" />
      <h2>文件大小：{{ filesizeText }}</h2>
      <mdui-text-field label="清北网盘链接" v-model="tsinbei"/>
      <mdui-text-field label="清北网盘提取码" v-model="tsinbei_pwd" v-show="tsinbei.length>0"/>
      <mdui-text-field label="蓝奏云链接" v-model="lanzou" />
      <mdui-text-field label="蓝奏云提取码" v-model="lanzou_pwd" v-show="lanzou.length>0" />
      <mdui-text-field label="123云盘链接" v-model="pan123" />
      <mdui-text-field label="123云盘提取码" v-model="pan123_pwd" v-show="pan123.length>0" />
      <mdui-text-field label="文件提取码" v-model="password" />
      <mdui-divider style="margin-top: 8px; margin-bottom: 8px;"/>
      <mdui-button full-width @click="submitFile()">上传文件</mdui-button>
    </div>
  </mdui-layout-main>
</template>
