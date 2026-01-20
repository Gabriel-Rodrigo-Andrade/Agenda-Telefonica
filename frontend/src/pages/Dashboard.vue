<template>
	<section class="dashboard">
    <div class="name-desc-page">
		  <h2>Dashboard</h2>
		  <p>  | Bem-vindo ao sistema de agenda telefônica</p>
    </div>

    <div class="fast-access">
      <h3>Acesso Rápido</h3>

      <div class="cards">
			  <router-link class="card" to="/sistema/novocontato">
          <img src="../assets/add-contact.png" class="icon-cards" />
			  	<h4>Novo Contato</h4>
			  	<p>Cadastrar dados pessoais.</p>
			  </router-link>
			  <router-link class="card" to="/sistema/novoendereco">
          <img src="../assets/location.png" class="icon-cards" />
			  	<h4>Adicionar Endereço</h4>
			  	<p>Cadastrar novo endereço.</p>
			  </router-link>
			  <router-link class="card" to="/sistema/novotelefone">
          <img src="../assets/phone.png" class="icon-cards" />
			  	<h4>Adicionar Telefone</h4>
			  	<p>Cadastrar e gerenciar telefones.</p>
			  </router-link>
        <router-link class="card" to="/sistema/contatos">
          <img src="../assets/search-dashboard.png" class="icon-cards" />
			  	<h4>Consultar Contatos</h4>
			  	<p>Ver todos os contatos.</p>
			  </router-link>
		  </div>
    </div>

    <div class="recent-section">
      <div class="recent-contacts">
        <div class="section-header">
          <h3>Contatos Recentes</h3>
          <router-link to="/sistema/contatos" class="view-all">Ver todos</router-link>
        </div>
        <div class="contacts-list">
          <div v-for="contato in contatosRecentes" :key="contato.id" class="contact-item">
            <img :src="contato.avatar" :alt="contato.nome" class="avatar" />
            <div class="contact-info">
              <p class="contact-name">{{ contato.nome }}</p>
              <p class="contact-email">{{ contato.email }}</p>
            </div>
            <p class="contact-phone">{{ contato.telefone }}</p>
            <p class="contact-date">{{ contato.data }}</p>
          </div>
        </div>
      </div>

      <div class="recent-activities">
        <div class="section-header">
          <h3>Atividades Recentes</h3>
        </div>
        <div class="activities-list">
          <div v-for="atividade in atividadesRecentes" :key="atividade.id" class="activity-item">
            <div class="activity-icon">
              <img :src="atividade.icon" alt="" class="activity-icon-img" />
            </div>
            <div class="activity-info">
              <p class="activity-title">{{ atividade.titulo }}</p>
              <p class="activity-description">{{ atividade.descricao }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
	</section>
</template>

<script>
import { formatDistanceToNow, parseISO } from 'date-fns'
import { ptBR } from 'date-fns/locale'
import { api } from '../services/api'

export default {
	name: 'Dashboard',
  data() {
    return {
      contatosRecentes: [],
      atividadesRecentes: [],
      loading: true,
      error: null
    }
  },
  mounted() {
    this.carregarDados()
  },
  methods: {
    async carregarDados() {
      try {
        this.loading = true
        this.error = null
        
        // carrega contatos recentes
        const contatosResponse = await api.get('/contatos/recentes')
        this.contatosRecentes = this.formatarContatos(contatosResponse.data)
        
        // carrega atividades recentes
        const atividadesResponse = await api.get('/atividades/recentes')
        this.atividadesRecentes = this.formatarAtividades(atividadesResponse.data)
        
      } catch (error) {
        console.error('Erro ao carregar dados:', error)
        this.error = error.message
      } finally {
        this.loading = false
      }
    },
    
    formatarContatos(contatos) {
      return contatos.map(contato => ({
        id: contato.id,
        nome: contato.nome,
        email: contato.email,
        telefone: contato.telefone || 'Sem telefone',
        data: this.formatarData(contato.criado_em),
        avatar: `https://i.pravatar.cc/150?u=${contato.email}`
      }))
    },
    
    formatarAtividades(atividades) {
      const iconUrl = (name) => new URL(`../assets/${name}`, import.meta.url).href
      const tiposAtividade = {
        'novo_contato': { icon: iconUrl('add-contact.png'), cor: '#3B82F6', titulo: 'Novo contato adicionado' },
        'novo_endereco': { icon: iconUrl('location.png'), cor: '#10B981', titulo: 'Endereço atualizado' },
        'novo_telefone': { icon: iconUrl('phone.png'), cor: '#A855F7', titulo: 'Telefone adicionado' },
        'contato_editado': { icon: iconUrl('edit.png'), cor: '#F97316', titulo: 'Contato editado' },
        'contato_removido': { icon: iconUrl('tash.png'), cor: '#EF4444', titulo: 'Contato removido' }
      }
      
      return atividades.map((atividade, index) => {
        const tipo = tiposAtividade[atividade.tipo] || tiposAtividade['novo_contato']
        // Extrair apenas a parte após o primeiro ":" para evitar repetição
        const descricaoLimpa = atividade.descricao.includes(':') 
          ? atividade.descricao.split(':').slice(1).join(':').trim()
          : atividade.descricao
        return {
          id: atividade.id || index,
          ...tipo,
          descricao: `${descricaoLimpa} - ${this.formatarData(atividade.criado_em)}`
        }
      })
    },
    
    
    formatarData(dataString) {
      return formatDistanceToNow(parseISO(dataString), {
        addSuffix: true,
        locale: ptBR
      })
    }
  }
}
</script>

<style scoped>
.dashboard{
  padding: 0.5rem 1.5rem;
}

.name-desc-page {
	padding-bottom: 14px;
	margin-bottom: 16px;
	border-bottom: 1px solid #e5e7eb;
  p {
    font-size: 14px;
    opacity: 0.5;
  }
}

.cards { 
  display: grid; 
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); 
  gap: 0.8rem; 
  margin: 1rem 0;
}

.card { 
  display: block; 
  padding: 1rem; 
  border: 1px solid #e5e7eb; 
  border-radius: 8px; 
  text-decoration: none; 
  color: inherit; 
  background: #fafafa; 
  transition: transform .15s ease, box-shadow .15s ease;
  align-items: center;
  text-align: center;
  h4 {
    padding: 0.5rem;
  }
  p {
    font-size: 14px;
    opacity: 0.5;
  }
}

.card:hover { 
  transform: translateY(-6px);
}

.card:nth-child(1):hover {
  box-shadow: 0 4px 12px #78C94B;
}

.card:nth-child(2):hover {
  box-shadow: 0 4px 12px #334CD6;
}

.card:nth-child(3):hover {
  box-shadow: 0 4px 12px #8631DE;
}

.card:nth-child(4):hover {
  box-shadow: 0 4px 12px #AD401C;
}

.icon-cards {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  object-fit: cover;
  margin: 0 auto 12px;
  display: block;
}

.recent-section {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 2rem;
  margin-top: 3rem;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
  padding-bottom: 1rem;
  margin-bottom: 1rem;
  border-bottom: 1px solid #e5e7eb;
}

.recent-contacts,
.recent-activities {
  padding: 1.5rem;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  background: #fafafa;
}

.view-all {
  color: #3B82F6;
  text-decoration: none;
  font-size: 0.9rem;
  font-weight: 500;
}

.view-all:hover {
  text-decoration: underline;
}

.contacts-list {
  display: grid;
  gap: 1rem;
}

.contact-item {
  display: grid;
  grid-template-columns: 50px 1fr auto auto;
  align-items: center;
  gap: 1rem;
  padding: 1rem;
}

.avatar {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  object-fit: cover;
}

.contact-info {
  display: grid;
  gap: 0.25rem;
  min-width: 0;
}

.contact-name {
  margin: 0;
  font-weight: 600;
  font-size: 0.95rem;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.contact-email {
  margin: 0;
  font-size: 0.85rem;
  color: #6b7280;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.contact-phone {
  margin: 0;
  font-size: 0.9rem;
  color: #374151;
  font-weight: 500;
}

.contact-date {
  margin: 0;
  font-size: 0.8rem;
  color: #9ca3af;
  text-align: right;
}

.activities-list {
  display: grid;
  gap: 1rem;
}

.activity-item {
  display: grid;
  grid-template-columns: 50px 1fr;
  align-items: start;
  gap: 1rem;
  padding: 1rem;
}

.activity-icon {
  width: 50px;
  height: 50px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.activity-icon-img {
  width: 80%;
  height: 80%;
  object-fit: contain;
  opacity: 50%;
}

.activity-info {
  display: grid;
  gap: 0.25rem;
}

.activity-title {
  margin: 0;
  font-weight: 600;
  font-size: 0.95rem;
}

.activity-description {
  margin: 0;
  font-size: 0.85rem;
  color: #6b7280;
}

@media (max-width: 991.98px) {
  .recent-section {
    grid-template-columns: 1fr;
  }
  
  .cards {
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  }
}

@media (max-width: 767.98px) {
  .dashboard {
    padding: 0.5rem 0.75rem;
  }
  
  .cards {
    grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
    gap: 0.6rem;
  }
  
  .contact-item {
    grid-template-columns: 40px 1fr;
    gap: 0.75rem;
  }
  
  .contact-phone,
  .contact-date {
    display: none;
  }
  
  .avatar {
    width: 40px;
    height: 40px;
  }
  
  .recent-contacts,
  .recent-activities {
    padding: 1rem;
  }
}

@media (max-width: 575.98px) {
  .dashboard {
    padding: 0.5rem;
  }
  
  .name-desc-page h2 {
    font-size: 1.25rem;
  }
  
  .name-desc-page p {
    font-size: 12px;
  }
  
  .cards {
    grid-template-columns: 1fr;
  }
  
  .card {
    padding: 0.75rem;
  }
  
  .icon-cards {
    width: 40px;
    height: 40px;
  }
  
  .recent-section {
    gap: 1rem;
    margin-top: 2rem;
  }
  
  .section-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.5rem;
  }
  
  .activity-item {
    grid-template-columns: 40px 1fr;
    gap: 0.75rem;
  }
  
  .activity-icon {
    width: 40px;
    height: 40px;
  }
}
</style>