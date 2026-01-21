<template>
  <div>
    <h2>Adicionar Endereço</h2>

    <input v-model="cep" placeholder="CEP" />
    <button @click="buscar">Buscar</button>

    <div v-if="endereco.logradouro">
      <input v-model="endereco.logradouro" placeholder="Logradouro" />
      <input v-model="endereco.numero" placeholder="Número" />
      <input v-model="endereco.complemento" placeholder="Complemento" />
      <input v-model="endereco.bairro" placeholder="Bairro" />
      <input v-model="endereco.cidade" placeholder="Cidade" />
      <input v-model="endereco.estado" placeholder="Estado" />

      <label>
        Principal:
        <input type="checkbox" v-model="endereco.principal" />
      </label>

      <button @click="salvar">Salvar Endereço</button>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";
import { buscarCep } from "../services/CepService";
import { api } from "../services/api";

const cep = ref("");
const endereco = ref({
  logradouro: "",
  numero: "",
  complemento: "",
  bairro: "",
  cidade: "",
  estado: "",
  tipo: "residencial",
  principal: false,
  contato_id: 1 // Exemplo — vc vai enviar o contato correto
});

async function buscar() {
  const data = await buscarCep(cep.value);

  endereco.value.logradouro = data.logradouro;
  endereco.value.bairro = data.bairro;
  endereco.value.cidade = data.cidade;
  endereco.value.estado = data.estado;
  endereco.value.cep = data.cep;
}

async function salvar() {
  const res = await api.post("/SalvarEndereco.php", endereco.value);
  alert("Endereço salvo com sucesso!");
}
</script>