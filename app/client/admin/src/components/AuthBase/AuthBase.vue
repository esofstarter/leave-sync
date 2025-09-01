<script setup lang="ts">
  import "./AuthBase.scss";
</script>

<template>
  <div class="auth-base">
    <div class="auth-base__wrapper">
      <div class="auth-base__container">
        <div class="auth-base__logo">
          <a href="#">
            <img src="@/../assets/images/sm_logo.png" />
          </a>
        </div>
        <router-view />
      </div>
    </div>
  </div>
</template>
