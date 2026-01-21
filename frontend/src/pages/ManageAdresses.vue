<template>
  <div class="new-adress">
    <div class="name-desc-page">
		  <h2>Cadastro De Endereço</h2>
      <p>  | Tela para cadastro, edição e exclusão de endereços</p>
    </div>
    <div class="add-new-adress">
      <div class="section-header">
        <h3>Gerenciar Endereços</h3>
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

        <form @submit.prevent="salvarEndereco">
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
              <label for="cep">CEP *</label>
              <div class="input-with-button">
                <input 
                  type="text" 
                  id="cep" 
                  v-model="form.cep" 
                  placeholder="00000-000"
                  @input="formatarCEP"
                  maxlength="9"
                  :class="{ 'input-error': erros.cep }"
                />
                <button 
                  type="button" 
                  class="btn-buscar-cep"
                  @click="buscarCEP"
                  :disabled="carregandoCep || form.cep.length < 9"
                >
                  {{ carregandoCep ? 'Buscando...' : 'Buscar' }}
                </button>
              </div>
              <span v-if="erros.cep" class="error-message">{{ erros.cep }}</span>
            </div>

            <div class="form-group">
              <label for="tipo">Tipo de Endereço *</label>
              <select 
                id="tipo" 
                v-model="form.tipo" 
                :class="{ 'input-error': erros.tipo }"
              >
                <option value="">Selecione</option>
                <option value="residencial">Residencial</option>
                <option value="comercial">Comercial</option>
                <option value="outro">Outro</option>
              </select>
              <span v-if="erros.tipo" class="error-message">{{ erros.tipo }}</span>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="logradouro">Endereço/Logradouro *</label>
              <input 
                type="text" 
                id="logradouro" 
                v-model="form.logradouro" 
                placeholder="Rua, Avenida, etc."
                :class="{ 'input-error': erros.logradouro }"
              />
              <span v-if="erros.logradouro" class="error-message">{{ erros.logradouro }}</span>
            </div>

            <div class="form-group">
              <label for="numero">Número *</label>
              <input 
                type="text" 
                id="numero" 
                v-model="form.numero" 
                placeholder="123"
                :class="{ 'input-error': erros.numero }"
              />
              <span v-if="erros.numero" class="error-message">{{ erros.numero }}</span>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="complemento">Complemento (Opcional)</label>
              <input 
                type="text" 
                id="complemento" 
                v-model="form.complemento" 
                placeholder="Apto, Bloco, Casa, etc."
              />
            </div>

            <div class="form-group">
              <label for="bairro">Bairro *</label>
              <input 
                type="text" 
                id="bairro" 
                v-model="form.bairro" 
                placeholder="Nome do bairro"
                :class="{ 'input-error': erros.bairro }"
              />
              <span v-if="erros.bairro" class="error-message">{{ erros.bairro }}</span>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="cidade">Cidade *</label>
              <input 
                type="text" 
                id="cidade" 
                v-model="form.cidade" 
                required 
                placeholder="Nome da cidade"
                :class="{ 'input-error': erros.cidade }"
              /><span v-if="erros.cidade" class="error-message">{{ erros.cidade }}</span>
            </div>

            <div class="form-group">
              <label for="estado">Estado *</label>
              <select 
                id="estado" 
                v-model="form.estado" 
                required
                :class="{ 'input-error': erros.estado }"
              >
                <option value="">Selecione um estado</option>
                <option value="AC">Acre</option>
                <option value="AL">Alagoas</option>
                <option value="AP">Amapá</option>
                <option value="AM">Amazonas</option>
                <option value="BA">Bahia</option>
                <option value="CE">Ceará</option>
                <option value="DF">Distrito Federal</option>
                <option value="ES">Espírito Santo</option>
                <option value="GO">Goiás</option>
                <option value="MA">Maranhão</option>
                <option value="MT">Mato Grosso</option>
                <option value="MS">Mato Grosso do Sul</option>
                <option value="MG">Minas Gerais</option>
                <option value="PA">Pará</option>
                <option value="PB">Paraíba</option>
                <option value="PR">Paraná</option>
                <option value="PE">Pernambuco</option>
                <option value="PI">Piauí</option>
                <option value="RJ">Rio de Janeiro</option>
                <option value="RN">Rio Grande do Norte</option>
                <option value="RS">Rio Grande do Sul</option>
                <option value="RO">Rondônia</option>
                <option value="RR">Roraima</option>
                <option value="SC">Santa Catarina</option>
                <option value="SP">São Paulo</option>
                <option value="SE">Sergipe</option>
                <option value="TO">Tocantins</option>
              </select>
              <span v-if="erros.estado" class="error-message">{{ erros.estado }}</span>
            </div>
          </div>

          <div class="form-actions">
            <button type="button" class="btn-secondary" @click="limparFormulario" :disabled="carregando">
              Limpar
            </button>
            <button type="submit" class="btn-primary" :disabled="carregando">
              {{ carregando ? 'Salvando...' : 'Salvar Endereço' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import { api } from '../services/api'
import { buscarCep } from '../services/CepService'

export default {
  name: 'ManageAdresses',
  data() {
    return {
      form: {
        contato_id: '',
        cep: '',
        logradouro: '',
        numero: '',
        complemento: '',
        bairro: '',
        cidade: '',
        estado: '',
        tipo: ''
      },
      contatos: [],
      erros: {},
      mensagemErro: '',
      mensagemSucesso: '',
      carregando: false,
      carregandoContatos: false,
      carregandoCep: false,
      carregandoEndereco: false,
      enderecoId: null
    }
  },
  mounted() {
    this.carregarContatos()
  },
  watch: {
    'form.contato_id'(novo) {
      if (novo) {
        this.carregarEnderecoExistente()
      } else {
        this.resetCamposEndereco(false)
      }
    }
  },
  methods: {
    formatarCEP(event) {
      let valor = event.target.value.replace(/\D/g, '')
      if (valor.length <= 8) {
        valor = valor.replace(/(\d{5})(\d)/, '$1-$2')
      }
      this.form.cep = valor
      if (this.erros.cep) {
        delete this.erros.cep
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

    async carregarEnderecoExistente() {
      try {
        this.carregandoEndereco = true
        this.mensagemErro = ''

        const response = await api.get(`/enderecos/contato/${this.form.contato_id}`)
        const dados = response.data

        this.enderecoId = dados.id
        this.form = {
          ...this.form,
          cep: dados.cep || '',
          logradouro: dados.logradouro || '',
          numero: dados.numero || '',
          complemento: dados.complemento || '',
          bairro: dados.bairro || '',
          cidade: dados.cidade || '',
          estado: dados.estado || '',
          tipo: dados.tipo || ''
        }
        // mascara do cep
        if (this.form.cep) {
          this.form.cep = this.form.cep.replace(/(\d{5})(\d{3})/, '$1-$2')
        }

      } catch (error) {
        if (error.response?.status === 404) {
          // Se n tem endereco para este contato vai limpa campos mas continua o contato selecionado
          this.resetCamposEndereco(true)
          return
        }
        console.error('Erro ao carregar endereço do contato:', error)
        this.mensagemErro = error.response?.data?.erro || 'Erro ao carregar endereço do contato'
      } finally {
        this.carregandoEndereco = false
      }
    },

    async buscarCEP() {
      if (this.form.cep.length < 9) {
        this.mensagemErro = 'CEP inválido'
        return
      }

      try {
        this.carregandoCep = true
        this.mensagemErro = ''
        
        const data = await buscarCep(this.form.cep)

        if (data.erro) {
          this.mensagemErro = 'CEP não encontrado'
          return
        }

        // aq q vai preencher os campos com os dados do viacep se passar das validacao
        this.form.logradouro = data.logradouro || ''
        this.form.bairro = data.bairro || ''
        this.form.cidade = data.cidade || ''
        this.form.estado = data.estado || ''

      } catch (error) {
        console.error('Erro ao buscar CEP:', error)
        this.mensagemErro = 'Erro ao buscar CEP. Verifique o número digitado.'
      } finally {
        this.carregandoCep = false
      }
    },
    
    async salvarEndereco() {
      try {
        this.carregando = true
        this.mensagemErro = ''
        this.mensagemSucesso = ''
        this.erros = {}

        const payload = {
          ...this.form,
          cep: this.form.cep.replace(/\D/g, '')
        }

        const isEdicao = !!this.enderecoId
        const baseUrl = import.meta.env.VITE_API_URL || 'http://localhost:8080/api'
        const url = isEdicao
          // se isEdicao for true escolhe ? = (put, se isEdicao for false escolhe : = post
          ? `${baseUrl}/enderecos/${this.enderecoId}`
          : `${baseUrl}/enderecos`
        const metodo = isEdicao ? 'PUT' : 'POST'
        
        const response = await fetch(url, {
          method: metodo,
          headers: {
            'Content-Type': 'application/json'
          },
          //converte json para string, antes tava lendo como objeto
          body: JSON.stringify(payload)
        })
        
        const dados = await response.json()

        if (!response.ok) {
          if (dados.erros && typeof dados.erros === 'object') {
            this.erros = dados.erros
            this.mensagemErro = dados.mensagem || 'Por favor, corrija os erros no formulário'
          } else {
            this.mensagemErro = dados.mensagem || 'Erro ao salvar endereço. Tente novamente.'
          }
          return
        }
        
                this.enderecoId = dados?.endereco?.id || this.enderecoId
                this.mensagemSucesso = dados.mensagem || (isEdicao ? 'Endereço atualizado com sucesso!' : 'Endereço salvo com sucesso!')

                // os dados permanecem no form para mais edicao
                this.form = {
                  ...this.form,
                  ...payload,
                  cep: payload.cep.replace(/(\d{5})(\d{3})/, '$1-$2')
                }
        
        setTimeout(() => {
          this.mensagemSucesso = ''
        }, 3000)
        
      } catch (error) {
        console.error('Erro ao salvar:', error)
        this.mensagemErro = 'Erro ao conectar com o servidor. Tente novamente.'
      } finally {
        this.carregando = false
      }
    },
    
    limparFormulario() {
      this.resetCamposEndereco(false)
      this.erros = {}
    },

    resetCamposEndereco(preservarContato = false) {
      const contatoSelecionado = preservarContato ? this.form.contato_id : ''
      this.form = {
        contato_id: contatoSelecionado,
        cep: '',
        logradouro: '',
        numero: '',
        complemento: '',
        bairro: '',
        cidade: '',
        estado: '',
        tipo: ''
      }
      this.enderecoId = null
    }
  }
}
</script>

<style scoped>
.new-adress {
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

.add-new-adress {
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
  .new-adress {
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

.input-with-button {
  display: flex;
  gap: 0.5rem;
}

.input-with-button input {
  flex: 1;
  margin-bottom: 0;
}

.btn-buscar-cep {
  padding: 0.75rem 1.25rem;
  background: #3B82F6;
  color: white;
  border: none;
  border-radius: 6px;
  font-weight: 600;
  font-size: 0.9rem;
  cursor: pointer;
  transition: all 0.2s ease;
  white-space: nowrap;
}

.btn-buscar-cep:hover:not(:disabled) {
  background: #2563EB;
}

.btn-buscar-cep:disabled {
  background: #9ca3af;
  cursor: not-allowed;
  opacity: 0.7;
}

.checkbox-input {
  width: auto;
  margin-right: 0.5rem;
  cursor: pointer;
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

  .input-with-button {
    flex-direction: column;
  }

  .btn-buscar-cep {
    width: 100%;
  }
}
</style>