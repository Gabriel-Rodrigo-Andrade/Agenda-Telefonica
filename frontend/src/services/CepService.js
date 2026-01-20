import { api } from './api'

export async function buscarCep(cep) {
  const { data } = await api.get('/cep', { params: { cep } })
  return data.endereco
}