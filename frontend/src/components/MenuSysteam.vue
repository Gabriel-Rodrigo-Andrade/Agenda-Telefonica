<template>
  <aside class="side-menu" :class="{ minimized: isMinimized }">
    <RouterLink to="/" class="brand">
      <img src="../assets/phone-book.png" class="brand-icon" />
      <div>
        <h3 class="brand-sub">Agenda Telefônica</h3>
        <h2 class="brand-title">| Sistema de contatos</h2>
      </div>
    </RouterLink>

    <nav class="menu-links">
      <RouterLink
        v-for="item in items"
        :key="item.to"
        :to="item.to"
        class="menu-link"
        :class="{ active: isActive(item.to) }"
      >
        <img :src="item.icon" class="menu-icon" alt="" />
        <span class="label">{{ item.label }}</span>
      </RouterLink>
    </nav>
    <div class="arrow-container">
      <button class="toggle-arrow" @click="toggleMenu" :title="isMinimized ? 'Expandir' : 'Minimizar'">
        <span class="arrow" :class="{ minimized: isMinimized }">←</span>
      </button>
    </div>
  </aside>
</template>

<script>
import dashboardIcon from '../assets/icons/dashboard-menu.png'
import contactsIcon from '../assets/icons/contacts-menu.png'
import adressIcon from '../assets/icons/adress-menu.png'
import phoneIcon from '../assets/icons/phone-menu.png'
import addContactIcon from '../assets/icons/add-contact-menu.png'

export default {
  name: 'MenuSysteam',
  data() {
    return {
      isMinimized: false
    }
  },
  computed: {
    items() {
      return [
        { label: 'Dashboard', to: '/sistema', icon: dashboardIcon },
        { label: 'Contatos', to: '/sistema/contatos', icon: contactsIcon },
        { label: 'Novo Contato', to: '/sistema/novocontato', icon: addContactIcon },
        { label: 'Gerenciar Endereços', to: '/sistema/novoendereco', icon: adressIcon },
        { label: 'Gerenciar Telefones', to: '/sistema/novotelefone', icon: phoneIcon }
      ]
    }
  },
  methods: {
    isActive(path) {
      const current = this.$route.path
      if (path === '/sistema') {
        return current === '/sistema'
      }
      return current === path || current.startsWith(`${path}/`)
    },
    toggleMenu() {
      this.isMinimized = !this.isMinimized
      localStorage.setItem('menuMinimized', this.isMinimized)
    }
  },
  mounted() {
    //persistencia local com localstorage pra salvar o menu aberto ou fechado
    //antes ao mudar de pagina abria novamente o menu
    const saved = localStorage.getItem('menuMinimized')
    if (saved !== null) {
      this.isMinimized = saved === 'true'
    }
  }
}
</script>

<style scoped>
.side-menu {
  position: sticky;
  top: 0;
  align-self: start;
  align-content: start;
  min-height: 100vh;
  background: #0f172a;
  color: #e2e8f0;
  padding: 24px 20px;
  display: grid;
  gap: 32px;
  grid-template-rows: auto 1fr auto;
  width: 260px;
  transition: width 0.5s ease, padding 0.5s ease, gap 0.5s ease;
  overflow-x: hidden;
}

.brand {
  display: inline-flex;
  align-items: top;
  gap: 12px;
  text-decoration: none;
  color: inherit;
  padding-top: 1.5rem;
  padding-bottom: 14px;
  margin-bottom: 16px;
  border-bottom: 1px solid #1f2937;
}

.brand-icon {
  width: 42px;
  height: 42px;
}

.brand-sub {
  margin: 0;
  font-size: 1rem;
  color: #e2e8f0;
}

.brand-title {
  margin: 0;
  font-size: 0.8rem;
  color: #94a3b8;
}

.brand-title, .brand-sub, .label{
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.menu-links {
  display: grid;
  gap: 8px;
  min-width: 0;
  grid-auto-rows: max-content;
}

.menu-link {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 1rem;
  padding: 10px 12px;
  border-radius: 10px;
  color: #cbd5e1;
  text-decoration: none;
  transition: background 0.2s ease, color 0.2s ease;
}

/* unica forma de converter cor de png = https://codepen.io/sosuke/pen/Pjoqqphttps://codepen.io/sosuke/pen/Pjoqqp */
.menu-icon {
  width: 20px;
  height: 20px;
  object-fit: contain;
  opacity: 0.7;
  transition: opacity 0.2s ease;
  filter: invert(69%) sepia(21%) saturate(303%) hue-rotate(175deg) brightness(91%) contrast(85%);
}

.menu-link:hover .menu-icon {
  opacity: 0.9;
  filter: invert(93%) sepia(4%) saturate(949%) hue-rotate(182deg) brightness(92%) contrast(90%);
}

.menu-link.active .menu-icon {
  opacity: 1;
  filter: invert(32%) sepia(94%) saturate(1059%) hue-rotate(185deg) brightness(86%) contrast(93%);
}

.menu-link:hover {
  background: rgba(255, 255, 255, 0.05);
  color: #e2e8f0;
}

.menu-link.active {
  background: rgba(255, 255, 255, 0.08);
  color: #f8fafc;
}

.label {
  font-size: 0.95rem;
  font-weight: 600;
}

.toggle-arrow {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 40px;
  height: 40px;
  margin-top: auto;
  background: rgba(255, 255, 255, 0.08);
  border: 1px solid rgba(255, 255, 255, 0.15);
  border-radius: 8px;
  color: #cbd5e1;
  cursor: pointer;
  transition: all 0.3s ease;
  padding: 0;
  font-size: 20px;
}

.toggle-arrow:hover {
  background: rgba(255, 255, 255, 0.15);
  color: #e2e8f0;
}

.arrow {
  display: inline-block;
  transition: transform 0.35s ease;
}

.arrow.minimized {
  transform: scaleX(-1);
}

.arrow-container {
  display: flex;
  justify-content: center;
}

.side-menu.minimized {
  width: 100px;
  padding: 24px 20px;
  gap: 32px;
  grid-template-rows: auto 1fr auto;
}

.side-menu.minimized .brand {
  padding-top: 1.5rem;
  padding-bottom: 14px;
  margin-bottom: 16px;
  border-bottom: none;
  justify-content: center;
}

.side-menu.minimized .brand-icon {
  width: 36px;
  height: 36px;
}

.side-menu.minimized .brand > div {
  display: none;
}

.side-menu.minimized .label {
  display: none;
}

.side-menu.minimized .menu-link {
  justify-content: center;
  margin-bottom: 1rem;
  padding: 10px 12px;
}

.side-menu.minimized .toggle-arrow {
  width: 36px;
  height: 36px;
  font-size: 18px;
}

.side-menu.side-menu.minimized .label,
.side-menu.minimized .brand > div {
  opacity: 0;
  transform: translateX(-10px);
  pointer-events: none;
  transition: tranform 0.15s ease, opacity 0.15 ease;
}

.label, .brand > div {
  transition: opacity 0.2s ease 0.15s, transform 0.2s ease 0.15s;
  opacity: 1;
  transform: translateX(0);
}

.label {
  transition-delay: 0.5s;
}
</style>