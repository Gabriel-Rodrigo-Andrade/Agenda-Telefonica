<template>
  <section class="home" id="top">
    <div class="resume">
      <div class="resume-title">
        <h1>Sistema de Agenda Telefônica Completo</h1>
      </div>
      <div class="resume-desc">
        <p>Gerencie seus contatos de forma organizada com cadastro de dados pessoais, endereços e telefones em uma plataforma intuitiva e eficiente.</p>
      </div>
      <div class="resume-icon">
        <img src="../assets/group.png" class="icon"/>
      </div>
    </div>

    <div class="feactures scroll-section" id="features">
      <div class="features-title">
        <h2>Funcionalidades do Sistema</h2>
      </div>
      <div class="features-carousel">
        <article class="card snap-item">
          <img src="../assets/add-contact.png" class="icon"/>
          <h3 class="card-title">Cadastro de Contatos</h3>
          <p class="card-desc">Registre dados pessoais completos com validação de CPF e e-mail únicos.</p>
        </article>

        <article class="card snap-item">
          <img src="../assets/location.png" class="icon"/>
          <h3 class="card-title">Gestão de Endereços</h3>
          <p class="card-desc">Cadastre e edite endereços completos vinculados aos contatos.</p>
        </article>

        <article class="card snap-item">
          <img src="../assets/phone.png" class="icon"/>
          <h3 class="card-title">Cadastro de Telefones</h3>
          <p class="card-desc">Registre telefones comerciais, residenciais e celulares com máscara automática.</p>
        </article>

        <article class="card snap-item">
          <img src="../assets/search.png" class="icon"/>
          <h3 class="card-title">Consulta Avançada</h3>
          <p class="card-desc">Pesquise contatos por nome e visualize todos os dados cadastrados.</p>
        </article>
      </div>
    </div>

    <div class="how-to scroll-section" id="how-to">
      <div class="how-title">
        <h2>Como Usar o Sistema</h2>
      </div>

      <div class="how-list">
        <article class="card step-card">
          <div class="step-header">
            <span class="step-badge">1</span>
            <div>
              <h3 class="card-title">Cadastrar Dados Pessoais</h3>
              <p class="card-desc">Comece registrando as informações básicas do contato:</p>
            </div>
          </div>
          <ul class="step-items">
            <li>Nome Completo</li>
            <li>CPF (deve ser único no sistema)</li>
            <li>E-mail (deve ser único no sistema)</li>
            <li>Data de Nascimento</li>
          </ul>
        </article>

        <article class="card step-card">
          <div class="step-header">
            <span class="step-badge">2</span>
            <div>
              <h3 class="card-title">Cadastrar Endereço</h3>
              <p class="card-desc">Após cadastrar o contato, adicione o endereço:</p>
            </div>
          </div>
          <ul class="step-items">
            <li>Selecione o contato cadastrado</li>
            <li>Preencha CEP, Rua, Número, Bairro</li>
            <li>Escolha Cidade e Estado</li>
            <li>Se já existir endereço, os dados serão carregados para edição</li>
          </ul>
        </article>

        <article class="card step-card">
          <div class="step-header">
            <span class="step-badge">3</span>
            <div>
              <h3 class="card-title">Cadastrar Telefones</h3>
              <p class="card-desc">Adicione os números de telefone do contato:</p>
            </div>
          </div>
          <ul class="step-items">
            <li>Selecione o contato</li>
            <li>Telefone Comercial, Residencial e Celular</li>
            <li>Máscara automática: (42) 9 8888-7766</li>
            <li>Dados existentes são carregados para edição</li>
          </ul>
        </article>

        <article class="card step-card">
          <div class="step-header">
            <span class="step-badge">4</span>
            <div>
              <h3 class="card-title">Consultar Contatos</h3>
              <p class="card-desc">Encontre e visualize informações dos contatos:</p>
            </div>
          </div>
          <ul class="step-items">
            <li>Digite o nome do contato na busca</li>
            <li>Visualize todos os dados em lista</li>
            <li>Busca por similaridade (%like%)</li>
            <li>Dados pessoais, endereço e telefones exibidos</li>
          </ul>
        </article>
      </div>
    </div>

    <div class="cta scroll-section" id="cta">
      <h2 class="cta-title">Comece a Organizar Seus Contatos</h2>
      <p class="cta-desc">Utilize todas as funcionalidades do sistema para manter sua agenda telefônica sempre atualizada e organizada.</p>
      <div class="cta-actions">
        <button class="btn btn-primary" @click="cadastrarContato">
          <span class="btn-icon">
            <img src="../assets/add-user.png" alt="Cadastrar contato" class="btn-icon-img" />
          </span>
          Organizar Meus Contatos
        </button>
        <button class="btn btn-ghost" @click="consultarContatos">
          <span class="btn-icon">
            <img src="../assets/search-icon.png" alt="Consultar contatos" class="btn-icon-img" />
          </span>
          Consultar Contatos
        </button>
      </div>
    </div>

    <LoadingOverlay
      :active="navegando"
      text="Navegando..."
      :fullscreen="true"
    />

  </section>
</template>

<script>
import LoadingOverlay from '../components/LoadingOverlay.vue'

export default {
  name: 'HomeLp',
  components: { LoadingOverlay },
  data() {
    return {
      navegando: false
    }
  },
  methods: {
    cadastrarContato() {
      this.navegando = true
      setTimeout(() => {
        this.$router.push('/sistema')
      }, 300)
    },
    consultarContatos() {
      this.navegando = true
      setTimeout(() => {
        this.$router.push('/sistema/contatos')
      }, 300)
    }
  }
}
</script>

<style scoped>
.home {
  padding: 3rem 1.5rem 4rem;
  background: #f5f7fb;
  color: #0f172a;
  display: grid;
  gap: 6rem;
  overflow-x: hidden;
}

.scroll-section {
  scroll-margin-top: 50px;
}

.resume {
  max-width: 1060px;
  margin: 0 auto;
  text-align: center;
  display: grid;
  gap: 1.25rem;
}

.resume-title h1 {
  font-size: 2.2rem;
  margin: 0;
  font-weight: 700;
}

.resume-desc p {
  margin: 0;
  font-size: 1.05rem;
  color: #475569;
  line-height: 1.6;
}

.resume-icon {
  display: flex;
  justify-content: center;
}

.icon {
  width: 60px;
  height: 60px;
  object-fit: contain;
  border-radius: 20px;
}

.feactures {
  max-width: 1400px;
  margin: 0 auto;
  display: grid;
  gap: 1.5rem;
}

.features-title h2 {
  margin: 0;
  text-align: center;
  font-size: 1.7rem;
  font-weight: 700;
}

.features-carousel {
  display: flex;
  gap: 1rem;
  overflow-x: auto;
  padding: 0.15rem 0.25rem 0.35rem;
  scroll-snap-type: x mandatory;
  -webkit-overflow-scrolling: touch;
}

.features-carousel::-webkit-scrollbar {
  height: 6px;
}

.features-carousel::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 999px;
}

.card {
  background: #fff;
  border: 1px solid #e5e7eb;
  border-radius: 14px;
  padding: 1.25rem;
  box-shadow: 0 18px 30px rgba(15, 23, 42, 0.04);
  display: grid;
  gap: 0.6rem;
}

.snap-item {
  scroll-snap-align: start;
  flex: 0 0 260px;
  max-width: 260px;
}

.card-title {
  margin: 0;
  font-size: 1.05rem;
  font-weight: 700;
  color: #0f172a;
}

.card-desc {
  margin: 0;
  margin-top: 0.2rem;
  color: #475569;
  line-height: 1.5;
}

.how-to {
  max-width: 840px;
  width: 100%;
  margin: 0 auto;
  display: grid;
  gap: 1.25rem;
}

.how-title h2 {
  margin: 0;
  text-align: center;
  font-size: 1.7rem;
  font-weight: 700;
}

.how-list {
  display: grid;
  gap: 1rem;
}

.step-card {
  padding: 1.25rem 1.5rem;
}

.step-header {
  display: grid;
  grid-template-columns: auto 1fr;
  gap: 0.75rem;
  align-items: center;
}

.step-badge {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background: #2563eb;
  color: #fff;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 0.95rem;
}

.step-items {
  list-style: none;
  margin: 0.75rem 0 0;
  padding: 0;
  display: grid;
  gap: 0.4rem;
  color: #334155;
  line-height: 1.5;
}

.step-items li {
  position: relative;
  padding-left: 20px;
}

.step-items li::before {
  content: '✔';
  position: absolute;
  left: 0;
  top: 0;
  color: #22c55e;
  font-size: 0.9rem;
  line-height: 1.2;
}

.cta {
  max-width: 840px;
  margin: 0 auto;
  text-align: center;
  display: grid;
  gap: 1rem;
}

.cta-title {
  margin: 0;
  text-align: center;
  font-size: 1.7rem;
  font-weight: 700;
}

.cta-desc {
  margin: 0;
  color: #475569;
  font-size: 1.05rem;
  line-height: 1.6;
}

.cta-actions {
  display: flex;
  gap: 0.75rem;
  justify-content: center;
  flex-wrap: wrap;
  margin-top: 0.5rem;
}

.btn {
  border: none;
  border-radius: 12px;
  padding: 0.85rem 1.4rem;
  font-size: 0.98rem;
  font-weight: 600;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 0.45rem;
  transition: transform 0.15s ease, box-shadow 0.2s ease, background 0.2s ease, color 0.2s ease;
}

.btn:active {
  transform: translateY(1px);
}

.btn-primary {
  background: #2563eb;
  color: #fff;
  box-shadow: 0 12px 24px rgba(37, 99, 235, 0.2);
}

.btn-primary:hover {
  background: #1d4ed8;
}

.btn-ghost {
  background: #fff;
  color: #0f172a;
  border: 1px solid #e2e8f0;
  box-shadow: 0 10px 20px rgba(15, 23, 42, 0.04);
}

.btn-ghost:hover {
  background: #f8fafc;
}

.btn-icon {
  font-size: 1.05rem;
  line-height: 1;
  display: inline-flex;
  align-items: center;
  justify-content: center;
}

.btn-icon-img {
  width: 18px;
  height: 18px;
  object-fit: contain;
  display: block;
}

@media (max-width: 1199.98px) {
  .home {
    padding: 2.75rem 1.25rem 3.25rem;
    gap: 6rem;
  }
}

@media (max-width: 991.98px) {
  .resume-title h1 {
    font-size: 1.9rem;
  }

  .resume-desc p {
    font-size: 1rem;
  }

  .icon {
    width: 50px;
    height: 50px;
  }

  .cta-title {
    font-size: 1.6rem;
  }

  .cta-desc {
    font-size: 1rem;
  }
}

@media (max-width: 767.98px) {
  .home {
    padding: 2.5rem 1rem 3rem;
    gap: 6rem;
  }

  .card {
    padding: 1.1rem;
  }

  .resume-title h1 {
    font-size: 1.7rem;
  }

  .cta-actions {
    flex-direction: column;
  }

  .cta-actions .btn {
    width: 100%;
    justify-content: center;
  }
}

@media (min-width: 768px) {
  .snap-item {
    flex: 0 0 280px;
    max-width: 280px;
  }
}

@media (min-width: 992px) {
  .features-carousel {
    justify-content: center;
  }

  .snap-item {
    flex: 0 0 calc((100% - 3rem) / 4);
    max-width: 260px;
  }
}

@media (max-width: 575.98px) {
  .home {
    padding: 2.25rem 0.85rem 2.5rem;
    gap: 6rem;
  }

  .icon {
    width: 40px;
    height: 40px;
  }

  .features-title h2,
  .how-title h2 {
    font-size: 1.25rem;
  }

  .card-title {
    font-size: 1rem;
  }

  .card-desc {
    font-size: 0.95rem;
  }

  .step-card {
    padding: 1.1rem 1.2rem;
  }

  .step-header {
    grid-template-columns: 1fr;
    gap: 0.5rem;
  }

  .step-badge {
    width: 32px;
    height: 32px;
    font-size: 0.9rem;
  }

  .cta-title {
    font-size: 1.5rem;
  }

  .cta-desc {
    font-size: 0.98rem;
  }
}
</style>
