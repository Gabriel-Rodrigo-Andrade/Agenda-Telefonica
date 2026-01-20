<template>
  <div v-if="active" :class="['loading-overlay', { fullscreen }]">
    <div class="spinner"></div>
    <span class="loading-text">{{ text }}</span>
  </div>
</template>

<script>
export default {
  name: 'LoadingOverlay',
  props: {
    active: {
      type: Boolean,
      default: false
    },
    text: {
      type: String,
      default: 'Carregando...'
    },
    fullscreen: {
      type: Boolean,
      default: false
    }
  }
}
</script>

<style scoped>
.loading-overlay {
  position: absolute;
  inset: 0;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 0.75rem;
  background: rgba(15, 23, 42, 0.35);
  color: #fff;
  font-weight: 600;
  z-index: 20;
  backdrop-filter: blur(2px);
}

.loading-overlay.fullscreen {
  position: fixed;
}

.spinner {
  width: 48px;
  height: 48px;
  border: 4px solid rgba(255, 255, 255, 0.35);
  border-top-color: #fff;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}

.loading-text {
  text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

.fade-overlay-enter-active,
.fade-overlay-leave-active {
  transition: opacity 0.2s ease;
}

.fade-overlay-enter-from,
.fade-overlay-leave-to {
  opacity: 0;
}
</style>
