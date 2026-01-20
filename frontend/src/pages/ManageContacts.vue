<template>
  <div class="new-contact">
    <div class="name-desc-page">
		  <h2>Cadastro De Contato</h2>
		  <p>  | Tela para cadastro dos dados pessoais do contato</p>
    </div>
    <div class="add-new-contact">
      <div class="section-header">
        <h3>Gerenciar Contatos</h3>
      </div>
      <div class="form">
        <div v-if="mensagemErro" class="alert alert-error">
          {{ mensagemErro }}
          <button @click="mensagemErro = ''" class="btn-close">×</button>
        </div>

        <div v-if="mensagemSucesso" class="alert alert-success">
          {{ mensagemSucesso }}
          <button @click="mensagemSucesso = ''" class="btn-close">×</button>
        </div>

        <form @submit.prevent="salvarContato">
          <div class="form-row">
            <div class="form-group">
              <label for="nome">Nome *</label>
              <input 
                type="text" 
                id="nome" 
                v-model="form.nome" 
                placeholder="Digite o nome"
                :class="{ 'input-error': erros.nome }"
              />
              <span v-if="erros.nome" class="error-message">{{ erros.nome }}</span>
            </div>

            <div class="form-group">
              <label for="sobrenome">Sobrenome *</label>
              <input 
                type="text" 
                id="sobrenome" 
                v-model="form.sobrenome" 
                placeholder="Digite o sobrenome"
                :class="{ 'input-error': erros.sobrenome }"
              />
              <span v-if="erros.sobrenome" class="error-message">{{ erros.sobrenome }}</span>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="cpf">CPF *</label>
              <input 
                type="text" 
                id="cpf" 
                v-model="form.cpf" 
                placeholder="000.000.000-00"
                @input="formatarCPF"
                maxlength="14"
                :class="{ 'input-error': erros.cpf }"
              />
              <span v-if="erros.cpf" class="error-message">{{ erros.cpf }}</span>
            </div>

            <div class="form-group">
              <label for="data_nascimento">Data de Nascimento *</label>
              <input 
                type="date" 
                id="data_nascimento" 
                v-model="form.data_nascimento" 
                :class="{ 'input-error': erros.data_nascimento }"
              />
              <span v-if="erros.data_nascimento" class="error-message">{{ erros.data_nascimento }}</span>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="email">E-mail *</label>
              <input 
                type="text" 
                id="email" 
                v-model="form.email" 
                placeholder="exemplo@email.com"
                :class="{ 'input-error': erros.email }"
              />
              <span v-if="erros.email" class="error-message">{{ erros.email }}</span>
            </div>

            <div class="form-group">
              <label for="tipo_email">Tipo de E-mail *</label>
              <select 
                id="tipo_email" 
                v-model="form.tipo_email" 
                :class="{ 'input-error': erros.tipo_email }"
              >
                <option value="">Selecione</option>
                <option value="pessoal">Pessoal</option>
                <option value="trabalho">Trabalho</option>
              </select>
              <span v-if="erros.tipo_email" class="error-message">{{ erros.tipo_email }}</span>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group full-width">
              <label for="foto_url">URL da Foto (Opcional)</label>
              <input 
                type="url" 
                id="foto_url" 
                v-model="form.foto_url" 
                placeholder="https://exemplo.com/foto.jpg"
              />
            </div>
          </div>

          <div class="form-actions">
            <button type="button" class="btn-secondary" @click="limparFormulario" :disabled="carregando">
              Limpar
            </button>
            <button type="submit" class="btn-primary" :disabled="carregando">
              {{ carregando ? 'Salvando...' : 'Salvar Contato' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import { api } from '../services/api'

export default {
  name: 'ManageContacts',
  data() {
    return {
      form: {
        nome: '',
        sobrenome: '',
        cpf: '',
        email: '',
        tipo_email: '',
        data_nascimento: '',
        foto_url: ''
      },
      erros: {},
      mensagemErro: '',
      mensagemSucesso: '',
      carregando: false
    }
  },
  methods: {
    formatarCPF(event) {
      let valor = event.target.value.replace(/\D/g, '')
      if (valor.length <= 11) {
        valor = valor.replace(/(\d{3})(\d)/, '$1.$2')
        valor = valor.replace(/(\d{3})(\d)/, '$1.$2')
        valor = valor.replace(/(\d{3})(\d{1,2})$/, '$1-$2')
      }
      this.form.cpf = valor
      // Limpar erro ao usuario comecar a digitar
      if (this.erros.cpf) {
        delete this.erros.cpf
      }
    },
    
    async salvarContato() {
      try {
        this.carregando = true
        this.mensagemErro = ''
        this.mensagemSucesso = ''
        this.erros = {}

        const payload = {
          ...this.form,
          data_nascimento: this.form.data_nascimento
        }
        
        const response = await api.post('/contatos', payload)
        const dados = response.data
        
        this.mensagemSucesso = dados.mensagem || 'Contato salvo com sucesso!'
        this.limparFormulario()
        
        setTimeout(() => {
          this.mensagemSucesso = ''
        }, 3000)
        
      } catch (error) {
        if (error.response?.data?.erros && typeof error.response.data.erros === 'object') {
          this.erros = error.response.data.erros
          this.mensagemErro = error.response.data.mensagem || 'Por favor, corrija os erros no formulário'
        } else {
          console.error('Erro ao salvar:', error)
          this.mensagemErro = error.response?.data?.mensagem || 'Erro ao conectar com o servidor. Tente novamente.'
        }
      } finally {
        this.carregando = false
      }
    },
    
    limparFormulario() {
      this.form = {
        nome: '',
        sobrenome: '',
        cpf: '',
        email: '',
        tipo_email: '',
        data_nascimento: '',
        foto_url: ''
      }
      this.erros = {}
    }
  }
}
</script>

<style scoped>
.new-contact{
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

.add-new-contact {
  padding: 1.5rem;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  background: #fafafa;
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

.alert {
  padding: 1rem;
  border-radius: 6px;
  margin-bottom: 0.5rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-weight: 500;
}

.alert-error {
  background-color: #fee2e2;
  color: #991b1b;
  border: 1px solid #fecaca;
}

.alert-success {
  background-color: #dcfce7;
  color: #166534;
  border: 1px solid #bbf7d0;
}

.btn-close {
  background: none;
  border: none;
  font-size: 1.5rem;
  cursor: pointer;
  color: inherit;
  padding: 0;
  width: 2rem;
  height: 2rem;
  display: flex;
  align-items: center;
  justify-content: center;
}

.btn-close:hover {
  opacity: 0.7;
}

@media (max-width: 575.98px) {
  .new-contact {
    padding: 0.5rem;
  }
  
  .name-desc-page h2 {
    font-size: 1.25rem;
  }
  
  .name-desc-page p {
    font-size: 12px;
  }
}

.form {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.form-group label {
  font-size: 0.9rem;
  font-weight: 600;
  color: #374151;
}

.form-group.full-width {
  grid-column: span 2;
}

.form-group input,
.form-group select {
  padding: 0.75rem;
  border: 2px solid #d1d5db;
  border-radius: 6px;
  font-size: 0.95rem;
  transition: all 0.2s ease;
  background: white;
  margin-bottom: 0.5rem;
}

.form-group input:focus,
.form-group select:focus {
  outline: none;
  border-color: #3B82F6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.form-group input::placeholder {
  color: #9ca3af;
}

.form-group input.input-error,
.form-group select.input-error {
  border-color: #ef4444;
  background-color: #fef2f2;
}

.form-group input.input-error:focus,
.form-group select.input-error:focus {
  border-color: #ef4444;
  box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
}

.error-message {
  color: #dc2626;
  font-size: 0.85rem;
  font-weight: 500;
  margin-top: -0.35rem;
  margin-bottom: 0.5rem;
  display: block;
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  margin-top: 1rem;
  padding-top: 1.5rem;
  border-top: 1px solid #e5e7eb;
}

.btn-primary,
.btn-secondary {
  padding: 0.75rem 1.5rem;
  border-radius: 6px;
  font-weight: 600;
  font-size: 0.95rem;
  cursor: pointer;
  transition: all 0.2s ease;
  border: none;
}

.btn-primary {
  background: #3B82F6;
  color: white;
}

.btn-primary:hover:not(:disabled) {
  background: #2563EB;
  transform: translateY(-1px);
  box-shadow: 0 4px 6px rgba(59, 130, 246, 0.2);
}

.btn-primary:disabled {
  background: #9ca3af;
  cursor: not-allowed;
  opacity: 0.7;
}

.btn-secondary {
  background: white;
  color: #6b7280;
  border: 1px solid #d1d5db;
}

.btn-secondary:hover:not(:disabled) {
  background: #f9fafb;
  border-color: #9ca3af;
}

.btn-secondary:disabled {
  cursor: not-allowed;
  opacity: 0.6;
}

@media (max-width: 768px) {
  .form-row {
    grid-template-columns: 1fr;
  }
  
  .form-group.full-width {
    grid-column: span 1;
  }
  
  .form-actions {
    flex-direction: column;
  }
  
  .btn-primary,
  .btn-secondary {
    width: 100%;
  }

  .alert {
    flex-direction: column;
    gap: 1rem;
    align-items: flex-start;
  }
}
</style>