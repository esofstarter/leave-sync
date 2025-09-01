<script lang="ts" setup>
  import './Header.css';
</script>
<template>
  <nav>
    <div class="wrapper">
      <div class="logo"><h1>LeaveSync</h1></div>

      <input type="radio" name="slider" id="menu-btn">
      <input type="radio" name="slider" id="close-btn">
      <label for="menu-btn" class="btn menu-btn"><i class="fas fa-bars"></i></label>
    </div>
  </nav>
</template>
