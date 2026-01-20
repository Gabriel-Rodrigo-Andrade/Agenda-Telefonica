<template>
  <div id="app">
    <HomeHeader />
    <main class="main-content">
      <router-view />
    </main>
    <HomeFooter />
  </div>
</template>

<script>
import HomeFooter from '../components/HomeFooter.vue';
import HomeHeader from '../components/HomeHeader.vue';

export default {
  name: 'DefaultLayout',
  components: {
    HomeHeader,
    HomeFooter
  }
}
</script>

<style scoped>
.main-content {
  flex: 1 0 auto;
}
</style>
