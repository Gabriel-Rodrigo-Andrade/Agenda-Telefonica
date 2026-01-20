<template>
  <footer class="home-footer">
    <div class="container-home-footer">
      <div class="footer-brand">
        <div class="brand">
          <img src="../assets/phone-book.png" class="icon-book" alt="Agenda Telefônica"/>
          <h3 class="brand-title">Agenda Telefônica</h3>
        </div>
        <p class="footer-desc">Landing Page apresentando o sistema de gerenciamento de contatos</p>
      </div>
    </div>
  </footer>
</template>

<script setup>

</script>

<style scoped>
.home-footer {
  background-color: rgb(255, 255, 255);
  padding: 2rem 0;
  width: 100%;
  height: 100px;
  box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.08);
}

.container-home-footer {
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 0 5rem;
}

.footer-brand {
  text-align: center;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.75rem;
}

.brand {
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
  font-size: 1.25rem;
  font-weight: 600;
  color: #0f172a;
}

.footer-desc {
  margin: 0;
  color: #475569;
  font-size: 1rem;
  line-height: 1.5;
  margin-bottom: 1rem;
}

/* Breakpoints Bootstrap: xs: <576px, sm: >=576px, md: >=768px, lg: >=992px, xl: >=1200px, xxl: >=1400px */
@media (max-width: 991px) {
  .container-home-footer {
    padding: 0 2rem;
  }
}

@media (max-width: 575px) {
  .container-home-footer {
    padding: 0 1rem;
  }

  .brand-title {
    font-size: 1.1rem;
  }

  .icon-book {
    width: 38px;
    height: 38px;
  }

  .footer-desc {
    font-size: 0.95rem;
  }
}
</style>