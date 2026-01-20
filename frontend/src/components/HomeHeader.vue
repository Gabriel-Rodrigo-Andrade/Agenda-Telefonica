<template>
  <header class="home-header">
    <div class="container-home-header">
      <div class="logo-home-header">
        <RouterLink to="/" class="brand">
          <img src="../assets/phone-book.png" class="icon-book"/>
          <h3 class="brand-title">Agenda Telefônica</h3>
        </RouterLink>
      </div>
      
      <button class="hamburger" @click="menuOpen = !menuOpen" aria-label="Toggle menu">
        <span></span>
        <span></span>
        <span></span>
      </button>

      <div class="menus-home-header" :class="{ 'menu-open': menuOpen }">
        <nav class="nav-menus-home" aria-label="main-navegation">
          <a class="nav-links" href="#top" @click.prevent="handleNav('#top')">Início</a>
          <a class="nav-links" href="#features" @click.prevent="handleNav('#features')">Funcionalidades</a>
          <a class="nav-links" href="#how-to" @click.prevent="handleNav('#how-to')">Como Usar</a>
          <a class="nav-links" href="#cta" @click.prevent="handleNav('#cta')">Acessar</a>
        </nav>
      </div>
    </div>
  </header>
</template>

<script setup>
import { ref } from 'vue';
const menuOpen = ref(false);

const handleNav = (id_da_div) => {
  const target_element = document.querySelector(id_da_div);
  if (target_element) {
    target_element.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }
  menuOpen.value = false;
};
</script>

<style scoped>
.home-header {
  background-color: rgb(255, 255, 255);
  padding: 0.2rem 0;
  position: relative;
  z-index: 50;
  width: 100%;
  min-height: 70px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
  margin-top: 1rem;
}

.container-home-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0 5rem ;
}

.logo-home-header .brand {
  display: flex;
  align-items: center;
  text-decoration: none;
  color: inherit;
}

.icon-book {
  width: 45px;
  height: 45px;
}

.brand-title {
  margin-left: 8px; 
}

.nav-menus-home {
  display: flex;
  align-items: center;
  transition: all 0.3s ease;
}

.nav-links {
  color: black;
  font-size: 1.1rem;
  text-decoration: none;
  font-weight: 500;
  padding: 0.4rem 0.8rem;
  border-radius: 6px;
  display: inline-block;
  margin: 0 0.5rem;
  transition: transform 0.2s ease, color 0.2s ease;
}
.nav-links:hover {
  transform: translateY(-3px) scale(1.05);
  color: #0743A4;
  font-weight: 600;

}

.nav-links.router-link-active {
  color: #0743A4;
}

.nav-links:focus-visible {
  outline: 2px solid #0743A4;
  outline-offset: 3px;
}

.hamburger {
  display: none;
  flex-direction: column;
  gap: 5px;
  background: transparent;
  border: none;
  cursor: pointer;
  padding: 8px;
}

.hamburger span {
  width: 28px;
  height: 3px;
  background-color: #000000;
  border-radius: 2px;
  transition: all 0.3s ease;
}

/* Breakpoints Bootstrap: xs: <576px, sm: >=576px, md: >=768px, lg: >=992px, xl: >=1200px, xxl: >=1400px */
@media (max-width: 991px) {
  .container-home-header {
    padding: 0 2rem;
  }

  .hamburger {
    display: flex;
  }

  .menus-home-header {
    position: fixed;
    top: 70px;
    right: -100%;
    width: 280px;
    height: calc(100vh - 70px);
    background: white;
    box-shadow: -2px 0 10px rgba(0, 0, 0, 0.1);
    transition: right 0.3s ease;
    z-index: 40;
  }

  .menus-home-header.menu-open {
    right: 0;
  }

  .nav-menus-home {
    flex-direction: column;
    align-items: flex-start;
    gap: 0;
    padding: 2rem 1.5rem;
  }

  .nav-links {
    width: 100%;
    padding: 1rem;
    margin: 0;
    border-bottom: 1px solid #f0f0f0;
  }

  .nav-links:hover {
    transform: none;
    background-color: #f8f9fa;
  }
}

@media (max-width: 575px) {
  .container-home-header {
    padding: 0 1rem;
  }

  .brand-title {
    font-size: 1.1rem;
  }

  .icon-book {
    width: 38px;
    height: 38px;
  }
}

</style>