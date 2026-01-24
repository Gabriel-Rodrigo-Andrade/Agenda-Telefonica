<template>
  <div class="new-phone">
    <div class="name-desc-page">
      <h2>Cadastro De Telefone</h2>
      <p>  | Tela para cadastro, edição e exclusão de telefones</p>
    </div>
    <div class="add-new-phone">
      <div class="section-header">
        <h3>Gerenciar Numeros</h3>
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

        <form @submit.prevent="salvarTelefone">
          <div class="form-row">
            <div class="form-group full-width">
              <label for="contato_id">Contato *</label>
              <select 
                id="contato_id" 
                v-model="form.contato_id" 
                :class="{ 'input-error': erros.contato_id }"
                :disabled="carregandoContatos"
              >
                <option value="">{{ carregandoContatos ? 'Carregando...' : 'Selecione um contato' }}</option>
                <option v-for="contato in contatos" :key="contato.id" :value="contato.id">
                  {{ contato.nome }} {{ contato.sobrenome }}
                </option>
              </select>
              <span v-if="erros.contato_id" class="error-message">{{ erros.contato_id }}</span>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="telefone_celular">Telefone Celular *</label>
              <input 
                type="text" 
                id="telefone_celular" 
                v-model="form.telefone_celular" 
                placeholder="(00) 00000-0000"
                @input="formatarTelefoneCelular"
                maxlength="15"
                :class="{ 'input-error': erros.telefone_celular }"
              />
              <span v-if="erros.telefone_celular" class="error-message">{{ erros.telefone_celular }}</span>
            </div>

            <div class="form-group">
              <label for="telefone_comercial">Telefone Comercial (Opcional)</label>
              <input 
                type="text" 
                id="telefone_comercial" 
                v-model="form.telefone_comercial" 
                placeholder="(00) 0000-0000"
                @input="formatarTelefoneComercial"
                maxlength="15"
                :class="{ 'input-error': erros.telefone_comercial }"
              />
              <span v-if="erros.telefone_comercial" class="error-message">{{ erros.telefone_comercial }}</span>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="telefone_residencial">Telefone Residencial (Opcional)</label>
              <input 
                type="text" 
                id="telefone_residencial" 
                v-model="form.telefone_residencial" 
                placeholder="(00) 0000-0000"
                @input="formatarTelefoneResidencial"
                maxlength="15"
                :class="{ 'input-error': erros.telefone_residencial }"
              />
              <span v-if="erros.telefone_residencial" class="error-message">{{ erros.telefone_residencial }}</span>
            </div>
          </div>

          <div class="form-actions">
            <button type="button" class="btn-secondary" @click="limparFormulario" :disabled="carregando">
              Limpar
            </button>
            <button type="submit" class="btn-primary" :disabled="carregando">
              {{ carregando ? 'Salvando...' : 'Salvar Telefones' }}
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
  name: 'NewPhoneNumber',
  data() {
    return {
      form: {
        contato_id: '',
        telefone_celular: '',
        telefone_comercial: '',
        telefone_residencial: ''
      },
      contatos: [],
      erros: {},
      mensagemErro: '',
      mensagemSucesso: '',
      carregando: false,
      carregandoContatos: false,
      telefoneId: null
    }
  },
  mounted() {
    this.carregarContatos()
  },
  watch: {
    'form.contato_id'(novo) {
      if (novo) {
        this.carregarTelefonesExistentes()
      } else {
        this.resetCamposTelefone(false)
      }
    }
  },
  methods: {
    formatarTelefoneCelular(event) {
      let valor = event.target.value.replace(/\D/g, '')
      if (valor.length <= 11) {
        // 11 dígitos: (00) 00000-0000
        valor = valor.replace(/^(\d{2})(\d{5})(\d{4})/, '($1) $2-$3')
        // 10 dígitos: (00) 0000-0000
        valor = valor.replace(/^(\d{2})(\d{4})(\d{4})/, '($1) $2-$3')
        // Enquanto digita
        valor = valor.replace(/^(\d{2})(\d{0,5})/, '($1) $2')
      }
      this.form.telefone_celular = valor
      if (this.erros.telefone_celular) {
        delete this.erros.telefone_celular
      }
    },

    formatarTelefoneComercial(event) {
      let valor = event.target.value.replace(/\D/g, '')
      if (valor.length <= 11) {
        valor = valor.replace(/^(\d{2})(\d{5})(\d{4})/, '($1) $2-$3')
        valor = valor.replace(/^(\d{2})(\d{4})(\d{4})/, '($1) $2-$3')
        valor = valor.replace(/^(\d{2})(\d{0,5})/, '($1) $2')
      }
      this.form.telefone_comercial = valor
      if (this.erros.telefone_comercial) {
        delete this.erros.telefone_comercial
      }
    },

    formatarTelefoneResidencial(event) {
      let valor = event.target.value.replace(/\D/g, '')
      if (valor.length <= 10) {
        valor = valor.replace(/^(\d{2})(\d{4})(\d{4})/, '($1) $2-$3')
        valor = valor.replace(/^(\d{2})(\d{0,5})/, '($1) $2')
      }
      this.form.telefone_residencial = valor
      if (this.erros.telefone_residencial) {
        delete this.erros.telefone_residencial
      }
    },

    async carregarContatos() {
      try {
        this.carregandoContatos = true
        const response = await api.get('/enderecos/contatos')
        this.contatos = Array.isArray(response.data) ? response.data : []
      } catch (error) {
        console.error('Erro ao carregar contatos:', error)
        this.mensagemErro = 'Erro ao carregar lista de contatos'
      } finally {
        this.carregandoContatos = false
      }
    },

    async carregarTelefonesExistentes() {
      try {
        this.mensagemErro = ''

        const response = await api.get(`/telefones/contato/${this.form.contato_id}`)
        const dados = response.data

        // Se tiver telefone vai preencher os campos aqui
        this.telefoneId = dados.id
        this.form.telefone_celular = this.aplicarMascaraCelular(dados.telefone_celular || '')
        this.form.telefone_comercial = this.aplicarMascaraFixo(dados.telefone_comercial || '')
        this.form.telefone_residencial = this.aplicarMascaraFixo(dados.telefone_residencial || '')

      } catch (error) {
        if (error.response?.status === 404) {
          this.resetCamposTelefone(true)
          return
        }
        console.error('Erro ao carregar telefones:', error)
        this.mensagemErro = error.response?.data?.erro || 'Erro ao carregar telefones do contato'
      }
    },

    aplicarMascaraCelular(numero) {
      if (!numero) return ''
      const limpo = numero.replace(/\D/g, '')
      if (limpo.length === 11) {
        return limpo.replace(/^(\d{2})(\d{5})(\d{4})/, '($1) $2-$3')
      }
      return numero
    },

    aplicarMascaraFixo(numero) {
      if (!numero) return ''
      const limpo = numero.replace(/\D/g, '')
      if (limpo.length === 10) {
        return limpo.replace(/^(\d{2})(\d{4})(\d{4})/, '($1) $2-$3')
      }
      return numero
    },

    resetCamposTelefone(manterContato) {
      if (!manterContato) {
        this.form.contato_id = ''
      }
      this.form.telefone_celular = ''
      this.form.telefone_comercial = ''
      this.form.telefone_residencial = ''
      this.telefoneId = null
      this.erros = {}
    },

    async salvarTelefone() {
      try {
        this.carregando = true
        this.mensagemErro = ''
        this.mensagemSucesso = ''
        this.erros = {}

        const baseUrl = import.meta.env.VITE_API_URL || 'http://localhost:8080/'
        const url = this.telefoneId 
          ? `${baseUrl}/telefones/${this.telefoneId}`
          : `${baseUrl}/telefones`
        
        const method = this.telefoneId ? 'PUT' : 'POST'

        const response = await fetch(url, {
          method,
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({
            contato_id: this.form.contato_id,
            telefone_celular: this.form.telefone_celular.replace(/\D/g, ''),
            telefone_comercial: this.form.telefone_comercial.replace(/\D/g, ''),
            telefone_residencial: this.form.telefone_residencial.replace(/\D/g, '')
          })
        })

        const dados = await response.json()

        if (!response.ok) {
          if (dados.erros) {
            this.erros = dados.erros
            this.mensagemErro = dados.mensagem || 'Erro ao salvar telefones'
          } else {
            this.mensagemErro = dados.erro || dados.mensagem || 'Erro ao salvar telefones'
          }
          return
        }

        this.mensagemSucesso = dados.mensagem || 'Telefones salvos com sucesso!'
        this.telefoneId = dados.telefone?.id || this.telefoneId
        
        setTimeout(() => {
          this.mensagemSucesso = ''
        }, 5000)

      } catch (error) {
        console.error('Erro ao salvar telefones:', error)
        this.mensagemErro = 'Erro ao salvar telefones. Tente novamente.'
      } finally {
        this.carregando = false
      }
    },

    limparFormulario() {
      this.form = {
        contato_id: '',
        telefone_celular: '',
        telefone_comercial: '',
        telefone_residencial: ''
      }
      this.telefoneId = null
      this.erros = {}
      this.mensagemErro = ''
      this.mensagemSucesso = ''
    }
  }
}
</script>

<style scoped>
.new-phone {
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

.add-new-phone {
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
  margin-bottom: 1.5rem;
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
  .new-phone {
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