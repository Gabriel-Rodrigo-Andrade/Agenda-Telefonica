<template>
  <div id="system-app" class="system-shell">
    <MenuSysteam />
    <main class="system-content">
      <router-view />
      <LoadingOverlay :active="navegando" text="Carregando..." />
    </main>
  </div>
</template>

<script>
import MenuSysteam from '../components/MenuSysteam.vue';
import LoadingOverlay from '../components/LoadingOverlay.vue';

export default {
  name: 'SystemLayout',
  components: {
    MenuSysteam,
    LoadingOverlay
  },
  data() {
    return {
      navegando: false,
      removeBefore: null,
      removeAfter: null
    }
  },
  mounted() {
    this.removeBefore = this.$router.beforeEach((to, from, next) => {
      const isSystemToSystem = to.path.startsWith('/sistema') && from.path.startsWith('/sistema') && to.path !== from.path
      if (isSystemToSystem) {
        this.navegando = true
      }
      next();
    });

    this.removeAfter = this.$router.afterEach(() => {
      if (this.navegando) {
        setTimeout(() => {
          this.navegando = false
        }, 200)
      }
    });
  },
  beforeUnmount() {
    if (this.removeBefore) this.removeBefore();
    if (this.removeAfter) this.removeAfter();
  }
}
</script>

<style scoped>
.system-shell {
  min-height: 100vh;
  display: grid;
  grid-template-columns: auto 1fr;
  background: #f5f7fb;
  transition: grid-template-columns 0.4s ease;
}

.system-content {
  min-height: 100vh;
  padding: 24px;
  width: 100%;
  position: relative;
}

.system-content p {
  font-size: 14px;
  opacity: 0.5;
}

@media (max-width: 991.98px) {
  .system-content {
    padding: 16px;
  }
}

@media (max-width: 575.98px) {
  .system-content {
    padding: 12px;
  }
}
</style>
