<template>
  <div class="contacts">
    <div class="name-desc-page">
      <h2>Consultar Contatos</h2>
      <p> | Pesquise e gerencie contatos</p>
    </div>

    <div class="find-contact">
      <div class="section-header">
        <h3>Todos os Contatos</h3>
      </div>

      <div class="search-container">
        <input
          v-model="pesquisa"
          @input="buscarContatos"
          type="text"
          placeholder="Pesquisar contato por nome..."
          class="search-input"
        />
      </div>

      <div v-if="loading" class="loading-message">
        <p>Carregando contatos...</p>
      </div>

      <div v-else-if="error" class="error-message">
        <p>{{ error }}</p>
      </div>

      <div v-else-if="contatos.length === 0" class="empty-message">
        <p>Nenhum contato encontrado</p>
      </div>

      <div v-else class="contacts-list">
        <div v-for="contato in contatos" :key="contato.id" class="contact-card">
          <div class="contact-header">
            <img :src="contato.avatar" :alt="contato.nome" class="avatar" />
            <div class="contact-basic-info">
              <p class="contact-name">{{ contato.nome }}</p>
              <p class="contact-email">{{ contato.email }}</p>
            </div>
            <p class="contact-date">{{ contato.data }}</p>
            <div class="contact-actions">
              <button
                class="icon-button"
                type="button"
                @click="editarContato(contato)"
                title="Editar contato"
              >
                <span class="description">Editar</span>
                <img :src="editIcon" alt="Editar" />
              </button>
              <button
                class="icon-button danger"
                type="button"
                :disabled="deletandoId === contato.id"
                @click="excluirContato(contato)"
                title="Excluir contato"
              >
                <span class="description">Excluir</span>
                <img :src="trashIcon" alt="Excluir" />
              </button>
            </div>
          </div>
          
          <div class="contact-details">
            <div class="detail-item">
              <span class="detail-label">Telefone Comercial:</span>
              <span class="detail-value">{{ contato.telefone_comercial || 'Não registrado' }}</span>
            </div>
            <div class="detail-item">
              <span class="detail-label">Telefone Residencial:</span>
              <span class="detail-value">{{ contato.telefone_residencial || 'Não registrado' }}</span>
            </div>
            <div class="detail-item">
              <span class="detail-label">Telefone Celular:</span>
              <span class="detail-value">{{ contato.telefone_celular || 'Não registrado' }}</span>
            </div>
            <div class="detail-item">
              <span class="detail-label">Endereço:</span>
              <span class="detail-value">{{ contato.endereco !== 'Sem endereço' ? contato.endereco : 'Não registrado' }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal de edicao -->
    <div v-if="modalEditarAberto" class="modal-overlay" @click="fecharModalEditar">
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h2>Editar Contato</h2>
          <button type="button" class="btn-close" @click="fecharModalEditar">
            ×
          </button>
        </div>

        <div class="modal-body">
          <div v-if="mensagemErroModal" class="alert alert-error">
            {{ mensagemErroModal }}
            <button @click="mensagemErroModal = ''" class="btn-close">×</button>
          </div>

          <div v-if="mensagemSucessoModal" class="alert alert-success">
            {{ mensagemSucessoModal }}
            <button @click="mensagemSucessoModal = ''" class="btn-close">×</button>
          </div>

          <div class="modal-tabs">
            <button
              v-for="aba in abas"
              :key="aba"
              :class="['tab-button', { active: abaAtiva === aba }]"
              @click="abaAtiva = aba"
            >
              {{ aba }}
            </button>
          </div>

          <form @submit.prevent="salvarEdicao" class="form">
            <div v-if="abaAtiva === 'Informações'" class="tab-content">
              <div class="form-row">
                <div class="form-group">
                  <label for="nome">Nome *</label>
                  <input
                    id="nome"
                    v-model="contatoEdit.nome"
                    type="text"
                    required
                    placeholder="Digite o nome"
                    :class="{ 'input-error': errosModal.nome }"
                  />
                  <span v-if="errosModal.nome" class="error-message">{{ errosModal.nome }}</span>
                </div>

                <div class="form-group">
                  <label for="sobrenome">Sobrenome</label>
                  <input
                    id="sobrenome"
                    v-model="contatoEdit.sobrenome"
                    type="text"
                    placeholder="Digite o sobrenome"
                  />
                </div>
              </div>

              <div class="form-row">
                <div class="form-group">
                  <label for="cpf">CPF</label>
                  <input
                    id="cpf"
                    v-model="contatoEdit.cpf"
                    type="text"
                    disabled
                  />
                </div>

                <div class="form-group">
                  <label for="data_nascimento">Data de Nascimento</label>
                  <input
                    id="data_nascimento"
                    v-model="contatoEdit.data_nascimento"
                    type="date"
                    :class="{ 'input-error': errosModal.data_nascimento }"
                  />
                  <span v-if="errosModal.data_nascimento" class="error-message">{{ errosModal.data_nascimento }}</span>
                </div>
              </div>

              <div class="form-row">
                <div class="form-group full-width">
                  <label for="email">Email *</label>
                  <input
                    id="email"
                    v-model="contatoEdit.email"
                    type="email"
                    required
                    placeholder="exemplo@email.com"
                    :class="{ 'input-error': errosModal.email }"
                  />
                  <span v-if="errosModal.email" class="error-message">{{ errosModal.email }}</span>
                </div>
              </div>
            </div>

            <div v-if="abaAtiva === 'Endereços'" class="tab-content">
              <div v-if="loadingEnderecos" class="loading-state">
                Carregando endereços...
              </div>
              
              <div v-else-if="enderecos.length === 0" class="empty-state">
                Nenhum endereço cadastrado
              </div>

              <div v-else class="tab-form-wrapper">
                <div class="form-row">
                  <div class="form-group full-width">
                    <label for="endereco_select">Selecione um Endereço *</label>
                    <select 
                      id="endereco_select" 
                      v-model="enderecoSelecionadoId"
                      @change="selecionarEndereco"
                      required
                    >
                      <option value="">Escolha um endereço...</option>
                      <option v-for="endereco in enderecos" :key="endereco.id" :value="endereco.id">
                        {{ endereco.tipo | capitalize }} - {{ endereco.logradouro }}, {{ endereco.numero }}
                      </option>
                    </select>
                  </div>
                </div>

                <div v-if="enderecoSelecionadoId" class="tab-form-wrapper">
                  <div class="form-row">
                    <div class="form-group">
                      <label for="endereco_tipo">Tipo *</label>
                      <select id="endereco_tipo" v-model="enderecoEditando.tipo" required>
                        <option value="residencial">Residencial</option>
                        <option value="comercial">Comercial</option>
                        <option value="outro">Outro</option>
                      </select>
                    </div>
                    <div class="form-group">
                      <label for="endereco_cep">CEP *</label>
                      <input
                        id="endereco_cep"
                        v-model="enderecoEditando.cep"
                        type="text"
                        placeholder="00000-000"
                        maxlength="9"
                        @input="formatarCEP"
                        required
                      />
                    </div>
                  </div>

                  <div class="form-row">
                    <div class="form-group full-width">
                      <label for="endereco_logradouro">Logradouro *</label>
                      <input
                        id="endereco_logradouro"
                        v-model="enderecoEditando.logradouro"
                        type="text"
                        placeholder="Rua, Avenida, etc"
                        required
                      />
                    </div>
                  </div>

                  <div class="form-row">
                    <div class="form-group">
                      <label for="endereco_numero">Número *</label>
                      <input
                        id="endereco_numero"
                        v-model="enderecoEditando.numero"
                        type="text"
                        placeholder="0"
                        required
                      />
                    </div>
                    <div class="form-group">
                      <label for="endereco_complemento">Complemento</label>
                      <input
                        id="endereco_complemento"
                        v-model="enderecoEditando.complemento"
                        type="text"
                        placeholder="Apto, sala, etc"
                      />
                    </div>
                  </div>

                  <div class="form-row">
                    <div class="form-group">
                      <label for="endereco_bairro">Bairro *</label>
                      <input
                        id="endereco_bairro"
                        v-model="enderecoEditando.bairro"
                        type="text"
                        required
                      />
                    </div>
                    <div class="form-group">
                      <label for="endereco_cidade">Cidade *</label>
                      <input
                        id="endereco_cidade"
                        v-model="enderecoEditando.cidade"
                        type="text"
                        required
                      />
                    </div>
                  </div>

                  <div class="form-row">
                    <div class="form-group">
                      <label for="endereco_estado">Estado (UF) *</label>
                      <input
                        id="endereco_estado"
                        v-model="enderecoEditando.estado"
                        type="text"
                        placeholder="SP"
                        maxlength="2"
                        required
                      />
                    </div>
                  </div>

                  <div class="form-buttons">
                    <button type="button" @click="excluirEndereco(enderecoSelecionadoId)" class="btn-secondary">
                      Excluir Endereço
                    </button>
                    <button type="button" @click="salvarEdicaoEndereco" class="btn-primary">
                      Salvar Endereço
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <div v-if="abaAtiva === 'Telefones'" class="tab-content">
              <div v-if="loadingTelefones" class="loading-state">
                Carregando telefones...
              </div>
              <div v-else class="tab-form-wrapper">
                <div class="form-row">
                  <div class="form-group">
                    <label for="telefone_celular">Telefone Celular *</label>
                    <input
                      id="telefone_celular"
                      v-model="telefones.celular"
                      type="text"
                      placeholder="(00) 00000-0000"
                      maxlength="15"
                      @input="formatarTelefone('celular')"
                      :class="{ 'input-error': errosModal.telefone_celular }"
                    />
                    <span v-if="errosModal.telefone_celular" class="error-message">{{ errosModal.telefone_celular }}</span>
                  </div>

                  <div class="form-group">
                    <label for="telefone_comercial">Telefone Comercial</label>
                    <input
                      id="telefone_comercial"
                      v-model="telefones.comercial"
                      type="text"
                      placeholder="(00) 0000-0000 ou (00) 00000-0000"
                      maxlength="15"
                      @input="formatarTelefone('comercial')"
                      :class="{ 'input-error': errosModal.telefone_comercial }"
                    />
                    <span v-if="errosModal.telefone_comercial" class="error-message">{{ errosModal.telefone_comercial }}</span>
                  </div>
                </div>

                <div class="form-row">
                  <div class="form-group">
                    <label for="telefone_residencial">Telefone Residencial</label>
                    <input
                      id="telefone_residencial"
                      v-model="telefones.residencial"
                      type="text"
                      placeholder="(00) 0000-0000"
                      maxlength="14"
                      @input="formatarTelefone('residencial')"
                      :class="{ 'input-error': errosModal.telefone_residencial }"
                    />
                    <span v-if="errosModal.telefone_residencial" class="error-message">{{ errosModal.telefone_residencial }}</span>
                  </div>
                </div>

                <button type="button" @click="salvarTelefones" class="btn-primary">
                  Salvar Telefones
                </button>
              </div>
            </div>

            <div class="form-actions">
              <button type="button" class="btn-secondary" @click="fecharModalEditar">
                Cancelar
              </button>
              <button type="submit" class="btn-primary">
                Salvar Alterações
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { formatDistanceToNow, parseISO } from 'date-fns'
import { ptBR } from 'date-fns/locale'
import { api } from '../services/api'
import editIcon from '../assets/icons/edit-icon.png'
import trashIcon from '../assets/icons/trash-icon.png'

export default {
  name: 'Contatos',
  data() {
    return {
      contatos: [],
      pesquisa: '',
      loading: true,
      error: null,
      buscaTimeout: null,
      deletandoId: null,
      editIcon,
      trashIcon,
      modalEditarAberto: false,
      abaAtiva: 'Informações',
      abas: ['Informações', 'Endereços', 'Telefones'],
      contatoEdit: {
        id: null,
        nome: '',
        sobrenome: '',
        cpf: '',
        data_nascimento: '',
        email: ''
      },
      enderecos: [],
      enderecoSelecionadoId: '',
      enderecoEmEdicao: null,
      enderecoEditando: {
        id: null,
        tipo: '',
        logradouro: '',
        numero: '',
        complemento: '',
        bairro: '',
        cidade: '',
        estado: '',
        cep: ''
      },
      telefones: {
        celular: '',
        comercial: '',
        residencial: ''
      },
      loadingEnderecos: false,
      loadingTelefones: false,
      mensagemErroModal: '',
      mensagemSucessoModal: '',
      errosModal: {}
    }
  },
  mounted() {
    this.carregarContatos()
  },
  filters: {
    capitalize(value) {
      if (!value) return ''
      value = value.toString()
      return value.charAt(0).toUpperCase() + value.slice(1)
    }
  },
  methods: {
    buscarContatos() {
      // Debounce
      clearTimeout(this.buscaTimeout)
      this.buscaTimeout = setTimeout(() => {
        this.carregarContatos()
      }, 300)
    },
    
    async carregarContatos() {
      try {
        this.loading = true
        this.error = null

        const params = {}
        if (this.pesquisa.trim()) {
          params.busca = this.pesquisa.trim()
        }

        const response = await api.get('/contatos', { params })
        this.contatos = this.formatarContatos(response.data)
      } catch (error) {
        console.error('Erro ao carregar contatos:', error)
        this.error = 'Erro ao carregar contatos. Tente novamente.'
      } finally {
        this.loading = false
      }
    },

    formatarContatos(contatos) {
      return contatos.map(contato => ({
        id: contato.id,
        nome: contato.nome_completo || contato.nome, // exibicao
        nome_separado: contato.nome, //edicao
        sobrenome: contato.sobrenome || '',
        cpf: contato.cpf || '',
        data_nascimento: contato.data_nascimento || '',
        email: contato.email,
        telefone_comercial: contato.telefone_comercial || '',
        telefone_residencial: contato.telefone_residencial || '',
        telefone_celular: contato.telefone_celular || '',
        endereco: contato.endereco || 'Sem endereço',
        data: this.formatarData(contato.criado_em),
        avatar: `https://i.pravatar.cc/150?u=${contato.email}`
      }))
    },

    formatarData(dataString) {
      return formatDistanceToNow(parseISO(dataString), {
        addSuffix: true,
        locale: ptBR
      })
    },

    editarContato(contato) {
      this.contatoEdit = {
        id: contato.id,
        nome: contato.nome_separado || contato.nome,
        sobrenome: contato.sobrenome || '',
        cpf: contato.cpf || '',
        data_nascimento: contato.data_nascimento || '',
        email: contato.email
      }
      this.abaAtiva = 'Informações'
      this.errosModal = {}
      this.mensagemErroModal = ''
      this.mensagemSucessoModal = ''
      this.enderecos = []
      this.telefones = { celular: '', comercial: '', residencial: '' }
      this.modalEditarAberto = true
      
      this.carregarEnderecos(contato.id)
      this.carregarTelefones(contato.id)
    },

    fecharModalEditar() {
      this.modalEditarAberto = false
      this.contatoEdit = {
        id: null,
        nome: '',
        sobrenome: '',
        cpf: '',
        data_nascimento: '',
        email: ''
      }
      this.errosModal = {}
      this.mensagemErroModal = ''
      this.mensagemSucessoModal = ''
      this.enderecos = []
      this.enderecoSelecionadoId = ''
      this.enderecoEditando = {
        id: null,
        tipo: '',
        logradouro: '',
        numero: '',
        complemento: '',
        bairro: '',
        cidade: '',
        estado: '',
        cep: ''
      }
      this.telefones = { celular: '', comercial: '', residencial: '' }
    },

    async carregarEnderecos(contatoId) {
      try {
        this.loadingEnderecos = true
        const response = await api.get(`/contatos/${contatoId}/enderecos`)
        this.enderecos = response.data
      } catch (error) {
        console.error('Erro ao carregar endereços:', error)
      } finally {
        this.loadingEnderecos = false
      }
    },

    async carregarTelefones(contatoId) {
      try {
        this.loadingTelefones = true
        const response = await api.get(`/telefones/contato/${contatoId}`)
        const data = response.data
        this.telefones = {
          celular: data.telefone_celular || '',
          comercial: data.telefone_comercial || '',
          residencial: data.telefone_residencial || ''
        }
      } catch (error) {
        console.error('Erro ao carregar telefones:', error)
        this.telefones = { celular: '', comercial: '', residencial: '' }
      } finally {
        this.loadingTelefones = false
      }
    },

    async salvarTelefones() {
      try {
        this.errosModal = {}
        this.mensagemErroModal = ''

        const response = await api.get(`/telefones/contato/${this.contatoEdit.id}`)

        let method = 'POST'
        let url = '/telefones'

        const data = response.data
        if (data.id) {
          method = 'PUT'
          url = `/telefones/${data.id}`
        }

        const payload = {
          contato_id: this.contatoEdit.id,
          telefone_celular: this.telefones.celular,
          telefone_comercial: this.telefones.comercial,
          telefone_residencial: this.telefones.residencial
        }

        const responseSave = method === 'POST'
          ? await api.post(url, payload)
          : await api.put(url, payload)

        const dados = responseSave.data

        this.mensagemSucessoModal = dados?.mensagem || 'Telefones atualizados com sucesso!'
        await this.carregarContatos()

        setTimeout(() => {
          this.mensagemSucessoModal = ''
        }, 3000)
      } catch (error) {
        console.error('Erro ao salvar telefones:', error)

        const dados = error.response?.data

        if (error.response?.status === 422 && dados?.erros) {
          this.errosModal = dados.erros
          this.mensagemErroModal = dados.mensagem || 'Por favor, corrija os erros no formulário'
          return
        }

        this.mensagemErroModal = dados?.mensagem || 'Erro ao conectar com o servidor. Tente novamente.'
      }
    },

    async excluirEndereco(enderecoId) {
      if (!confirm('Ao deletar este endereço você não poderá restaurá-lo novamente, deseja apagar mesmo assim ?')) return

      try {
        await api.delete(`/enderecos/${enderecoId}`)
        this.mensagemSucessoModal = 'Endereço excluído com sucesso!'
        await this.carregarEnderecos(this.contatoEdit.id)
        await this.carregarContatos()
        this.enderecoSelecionadoId = ''
        this.enderecoEditando = {
          id: null,
          tipo: '',
          logradouro: '',
          numero: '',
          complemento: '',
          bairro: '',
          cidade: '',
          estado: '',
          cep: ''
        }

        setTimeout(() => {
          this.mensagemSucessoModal = ''
        }, 3000)
      } catch (error) {
        this.mensagemErroModal = error.response?.data?.erro || 'Erro ao excluir endereço'
        console.error('Erro ao excluir endereço:', error)
        this.mensagemErroModal = 'Erro ao conectar com o servidor'
      }
    },

    selecionarEndereco() {
      if (!this.enderecoSelecionadoId) {
        this.enderecoEditando = {
          id: null,
          tipo: '',
          logradouro: '',
          numero: '',
          complemento: '',
          bairro: '',
          cidade: '',
          estado: '',
          cep: ''
        }
        return
      }

      const endereco = this.enderecos.find(e => e.id === this.enderecoSelecionadoId)
      if (endereco) {
        this.enderecoEditando = {
          id: endereco.id,
          tipo: endereco.tipo,
          logradouro: endereco.logradouro,
          numero: endereco.numero,
          complemento: endereco.complemento || '',
          bairro: endereco.bairro,
          cidade: endereco.cidade,
          estado: endereco.estado,
          cep: endereco.cep
        }
      }
    },

    abrirEdicaoEndereco(endereco) {
      this.enderecoEmEdicao = endereco.id
      this.enderecoEditando = {
        id: endereco.id,
        tipo: endereco.tipo,
        logradouro: endereco.logradouro,
        numero: endereco.numero,
        complemento: endereco.complemento || '',
        bairro: endereco.bairro,
        cidade: endereco.cidade,
        estado: endereco.estado,
        cep: endereco.cep
      }
    },

    cancelarEdicaoEndereco() {
      this.enderecoEmEdicao = null
      this.enderecoEditando = {
        id: null,
        tipo: '',
        logradouro: '',
        numero: '',
        complemento: '',
        bairro: '',
        cidade: '',
        estado: '',
        cep: ''
      }
    },

    async salvarEdicaoEndereco() {
      try {
        this.errosModal = {}
        this.mensagemErroModal = ''

        // Validacao basica
        if (!this.enderecoEditando.logradouro || !this.enderecoEditando.numero || 
            !this.enderecoEditando.bairro || !this.enderecoEditando.cidade || 
            !this.enderecoEditando.estado || !this.enderecoEditando.cep) {
          this.mensagemErroModal = 'Preencha todos os campos obrigatórios'
          return
        }

        const response = await api.put(`/enderecos/${this.enderecoEditando.id}`, {
          contato_id: this.contatoEdit.id,
          tipo: this.enderecoEditando.tipo,
          logradouro: this.enderecoEditando.logradouro,
          numero: this.enderecoEditando.numero,
          complemento: this.enderecoEditando.complemento,
          bairro: this.enderecoEditando.bairro,
          cidade: this.enderecoEditando.cidade,
          estado: this.enderecoEditando.estado,
          cep: this.enderecoEditando.cep
        })

        const dados = response.data
        this.mensagemSucessoModal = dados.mensagem || 'Endereço atualizado com sucesso!'
        await this.carregarEnderecos(this.contatoEdit.id)
        await this.carregarContatos()
        this.enderecoSelecionadoId = ''
        this.enderecoEditando = {
          id: null,
          tipo: '',
          logradouro: '',
          numero: '',
          complemento: '',
          bairro: '',
          cidade: '',
          estado: '',
          cep: ''
        }

        setTimeout(() => {
          this.mensagemSucessoModal = ''
        }, 3000)
      } catch (error) {
        console.error('Erro ao salvar endereço:', error)
        this.mensagemErroModal = 'Erro ao conectar com o servidor. Tente novamente.'
      }
    },

    formatarCEP() {
      let valor = this.enderecoEditando.cep
      valor = valor.replace(/\D/g, '')
      
      if (valor.length > 0) {
        valor = valor.substring(0, 8)
        if (valor.length > 5) {
          valor = `${valor.substring(0, 5)}-${valor.substring(5)}`
        }
      }
      
      this.enderecoEditando.cep = valor
    },

    formatarTelefone(tipo) {
      let valor = this.telefones[tipo]
      
      // remove caracteres
      valor = valor.replace(/\D/g, '')
      
      // mascara
      if (tipo === 'residencial') {
        // Residencial: (XX) XXXX-XXXX (10 dígitos)
        if (valor.length > 0) {
          valor = valor.substring(0, 10)
          if (valor.length > 2) {
            valor = `(${valor.substring(0, 2)}) ${valor.substring(2)}`
          }
          if (valor.length > 9) {
            valor = `${valor.substring(0, 9)}-${valor.substring(9)}`
          }
        }
      } else {
        // Celular e comercial: (XX) XXXXX-XXXX ou (XX) XXXX-XXXX (10-11 dígitos)
        if (valor.length > 0) {
          valor = valor.substring(0, 11)
          if (valor.length > 2) {
            valor = `(${valor.substring(0, 2)}) ${valor.substring(2)}`
          }
          if (valor.length >= 10 && valor.length <= 15) {
            const parteNumeros = valor.replace(/\D/g, '')
            if (parteNumeros.length === 11) {
              valor = `(${parteNumeros.substring(0, 2)}) ${parteNumeros.substring(2, 7)}-${parteNumeros.substring(7)}`
            } else if (parteNumeros.length === 10) {
              valor = `(${parteNumeros.substring(0, 2)}) ${parteNumeros.substring(2, 6)}-${parteNumeros.substring(6)}`
            }
          }
        }
      }
      
      this.telefones[tipo] = valor
      // Limpar erro ao digitar
      if (this.errosModal[`telefone_${tipo}`]) {
        delete this.errosModal[`telefone_${tipo}`]
      }
    },

    async salvarEdicao() {
      try {
        this.errosModal = {}
        this.mensagemErroModal = ''

        // Valida data de nascimento
        if (this.contatoEdit.data_nascimento) {
          const dataNascimento = new Date(this.contatoEdit.data_nascimento)
          const hoje = new Date()
          hoje.setHours(0, 0, 0, 0)
          
          if (dataNascimento >= hoje) {
            this.errosModal.data_nascimento = 'Data de nascimento não pode ser hoje ou no futuro'
            this.mensagemErroModal = 'Por favor, corrija os erros no formulário'
            return
          }
        }

        const response = await api.put(`/contatos/${this.contatoEdit.id}`, {
          nome: this.contatoEdit.nome,
          sobrenome: this.contatoEdit.sobrenome,
          data_nascimento: this.contatoEdit.data_nascimento,
          email: this.contatoEdit.email
        })

        const dados = response.data
        this.mensagemSucessoModal = dados.mensagem || 'Contato atualizado com sucesso!'
        await this.carregarContatos()

        setTimeout(() => {
          this.fecharModalEditar()
        }, 1500)
      } catch (error) {
        if (error.response?.data?.erros && typeof error.response.data.erros === 'object') {
          this.errosModal = error.response.data.erros
          this.mensagemErroModal = error.response.data.mensagem || 'Por favor, corrija os erros no formulário'
        } else {
          console.error('Erro ao salvar contato:', error)
          this.mensagemErroModal = error.response?.data?.mensagem || 'Erro ao conectar com o servidor. Tente novamente.'
        }
      }
    },

    async excluirContato(contato) {
      if (this.deletandoId) return

      const confirmar = window.confirm('Ao deletar o contato você não poderá restaurá-lo novamente, deseja apagar mesmo assim ?')
      if (!confirmar) return

      this.deletandoId = contato.id
      this.error = null

      try {
        await api.delete(`/contatos/${contato.id}`)
        this.contatos = this.contatos.filter(c => c.id !== contato.id)
      } catch (error) {
        console.error('Erro ao deletar contato:', error)
        this.error = error.response?.data?.erro || 'Erro ao deletar contato. Tente novamente.'
      } finally {
        this.deletandoId = null
      }
    }
  }
}
</script>

<style scoped>
.contacts {
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

.find-contact {
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

.search-container {
  margin-bottom: 1.5rem;
}

.search-input {
  width: 100%;
  padding: 0.75rem 1rem;
  font-size: 0.95rem;
  border: 1px solid #e5e7eb;
  border-radius: 6px;
  background: #fff;
  transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.search-input:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.contacts-list {
  display: grid;
  gap: 1rem;
}

.contact-card {
  background: #fff;
  border-radius: 8px;
  border: 1px solid #e5e7eb;
  padding: 1.25rem;
  transition: box-shadow 0.2s ease;
}

.contact-card:hover {
  box-shadow: 0 4px 12px rgba(15, 23, 42, 0.1);
}

.contact-header {
  display: grid;
  grid-template-columns: 50px 1fr auto auto;
  align-items: center;
  gap: 1rem;
  padding-bottom: 1rem;
  margin-bottom: 1rem;
  border-bottom: 1px solid #f3f4f6;
}

.contact-actions {
  display: flex;
  align-items: center;
  gap: 0.35rem;
  justify-self: end;
}

.icon-button {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 36px;
  height: 36px;
  border: 1px solid #e5e7eb;
  border-radius: 6px;
  background: #fff;
  color: #374151;
  cursor: pointer;
  transition: background-color 0.2s ease, border-color 0.2s ease, color 0.2s ease, transform 0.2s ease;
}

.icon-button img {
  width: 18px;
  height: 18px;
}

.icon-button:hover {
  background: #f3f4f6;
  border-color: #d1d5db;
  transform: translateY(-1px);
}

.icon-button.danger {
  color: #dc2626;
}

.icon-button.danger:hover {
  background: #fef2f2;
  border-color: #fecaca;
}

.icon-button:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none;
  background: #f9fafb;
}

.description {
  position: absolute;
  width: 1px;
  height: 1px;
  padding: 0;
  margin: -1px;
  overflow: hidden;
  clip: rect(0, 0, 0, 0);
  white-space: nowrap;
  border: 0;
}

.avatar {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  object-fit: cover;
}

.contact-basic-info {
  display: grid;
  gap: 0.25rem;
  min-width: 0;
}

.contact-name {
  margin: 0;
  font-weight: 600;
  font-size: 1rem;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.contact-email {
  margin: 0;
  font-size: 0.875rem;
  color: #6b7280;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.contact-date {
  margin: 0;
  font-size: 0.875rem;
  color: #9ca3af;
  white-space: nowrap;
}

.contact-details {
  display: grid;
  gap: 0.75rem;
}

.detail-item {
  display: grid;
  grid-template-columns: 160px 1fr;
  gap: 0.5rem;
  align-items: start;
}

.detail-label {
  font-size: 0.875rem;
  font-weight: 600;
  color: #374151;
}

.detail-value {
  font-size: 0.875rem;
  color: #6b7280;
  word-break: break-word;
}

.loading-message,
.error-message,
.empty-message {
  padding: 2rem;
  text-align: center;
  color: #666;
}

.error-message {
  color: #dc2626;
}

@media (max-width: 1024px) {
  .contact-header {
    grid-template-columns: 50px 1fr;
  }

  .contact-date {
    grid-column: 2;
    margin-top: 0.5rem;
  }

  .contact-actions {
    grid-column: 2;
  }

  .detail-item {
    grid-template-columns: 140px 1fr;
  }
}

@media (max-width: 768px) {
  .contacts {
    padding: 0.5rem 1rem;
  }

  .contact-card {
    padding: 1rem;
  }

  .contact-header {
    grid-template-columns: 40px 1fr;
    gap: 0.5rem 0.75rem;
    align-items: start;
  }

  .avatar {
    width: 40px;
    height: 40px;
  }

  .contact-date {
    grid-column: 2;
    margin-top: 0.35rem;
    font-size: 0.8125rem;
  }

  .contact-actions {
    grid-column: 2;
    justify-self: end;
    margin-top: 0.35rem;
    gap: 0.25rem;
  }

  .icon-button {
    width: 32px;
    height: 32px;
  }

  .icon-button img {    width: 16px;
    height: 16px;
  }

  .contact-name {
    font-size: 0.9375rem;
  }

  .contact-email {
    font-size: 0.8125rem;
  }

  .detail-item {
    grid-template-columns: 1fr;
    gap: 0.25rem;
  }

  .detail-label {
    font-size: 0.8125rem;
  }

  .detail-value {
    font-size: 0.8125rem;
    padding-left: 0;
  }
}

@media (max-width: 480px) {
  .contact-card {
    padding: 0.9rem;
  }

  .contact-header {
    gap: 0.4rem 0.6rem;
  }

  .contact-date {
    font-size: 0.75rem;
  }

  .contact-actions {
    gap: 0.2rem;
  }

  .icon-button {
    width: 30px;
    height: 30px;
  }

  .icon-button img {
    width: 14px;
    height: 14px;
  }
}

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal-content {
  background: #fff;
  border-radius: 8px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
  max-width: 600px;
  width: 90%;
  max-height: 85vh;
  display: flex;
  flex-direction: column;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem;
  border-bottom: 1px solid #e5e7eb;
}

.modal-header h2 {
  margin: 0;
  font-size: 1.25rem;
  font-weight: 600;
}

.modal-body {
  flex: 1;
  overflow-y: auto;
  padding: 1.5rem;
}

.modal-tabs {
  display: flex;
  gap: 0;
  border-bottom: 1px solid #e5e7eb;
  margin-bottom: 1.5rem;
  margin-left: -1.5rem;
  margin-right: -1.5rem;
  padding: 0 1.5rem;
}

.tab-button {
  flex: 1;
  padding: 1rem;
  background: none;
  border: none;
  cursor: pointer;
  font-size: 0.875rem;
  font-weight: 500;
  color: #6b7280;
  border-bottom: 2px solid transparent;
  transition: color 0.2s ease, border-color 0.2s ease;
}

.tab-button:hover {
  color: #374151;
}

.tab-button.active {
  color: #3B82F6;
  border-bottom-color: #3B82F6;
}

.tab-content {
  display: contents;
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
  transition: opacity 0.2s ease;
}

.btn-close:hover {
  opacity: 0.7;
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

.form-row.full-width {
  grid-template-columns: 1fr;
}

.form-group {
  display: flex;
  flex-direction: column;
}

.form-group.full-width {
  grid-column: 1 / -1;
}

.form-group label {
  font-size: 0.875rem;
  font-weight: 600;
  margin-bottom: 0.5rem;
  color: #374151;
}

.form-group input,
.form-group select {
  padding: 0.75rem;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  font-size: 0.95rem;
  font-family: inherit;
  transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.form-group input:focus,
.form-group select:focus {
  outline: none;
  border-color: #3B82F6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.form-group input:disabled {
  background-color: #f9fafb;
  color: #9ca3af;
  cursor: not-allowed;
}

.form-group input.input-error,
.form-group select.input-error {
  border-color: #ef4444;
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
  padding-top: 1.5rem;
  border-top: 1px solid #e5e7eb;
}

.form-buttons {
  display: flex;
  gap: 1rem;
}

.form-buttons .btn-primary,
.form-buttons .btn-secondary {
  flex: 1;
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

.loading-state,
.empty-state {
  padding: 2rem;
  text-align: center;
  color: #9ca3af;
  font-size: 0.95rem;
}

.tab-form-wrapper {
  display: contents;
}

@media (max-width: 768px) {
  .modal-content {
    width: 95%;
  }

  .form-row {
    grid-template-columns: 1fr;
  }

.form-section {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
  margin-top: 1.5rem;
  padding-top: 1.5rem;
  border-top: 1px solid #e5e7eb;
}

@media (max-width: 768px) {
  .modal-content {
    width: 95%;
  }

  .form-row {
    grid-template-columns: 1fr;
  }

  .form-actions {
    flex-direction: column;
  }

  .modal-tabs {
    margin-left: -1.5rem;
    margin-right: -1.5rem;
  }

  .tab-button {
    font-size: 0.8125rem;
    padding: 0.875rem 0.5rem;
  }

  .form-actions .btn-primary,
  .form-actions .btn-secondary {
    width: 100%;
  }
}
}
</style>
