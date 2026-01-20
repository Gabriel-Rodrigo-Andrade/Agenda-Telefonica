import { createRouter, createWebHistory } from 'vue-router'
import DefaultLayout from '../layouts/DefaultLayout.vue'
import SystemLayout from '../layouts/SystemLayout.vue'
import HomeLp from '../pages/HomeLp.vue'
import Dashboard from '../pages/Dashboard.vue'
import Contacts from '../pages/Contacts.vue'
import ManageAdresses from '../pages/ManageAdresses.vue'
import NewPhone from '../pages/NewPhoneNumber.vue'
import ManageContacts from '../pages/ManageContacts.vue'

const routes = [
  {
    path: '/',
    component: DefaultLayout,
    children: [
      {
        path: '',
        name: 'Home',
        component: HomeLp
      }
    ]
  },
  {
    path: '/sistema',
    component: SystemLayout,
    children: [
      {
        path: '',
        name: 'SistemaDashboard',
        component: Dashboard
      },
      {
        path: 'contatos',
        name: 'Contatos',
        component: Contacts
      },
      {
        path: 'novocontato',
        name: 'NovoContato',
        component: ManageContacts
      },
      {
        path: 'novoendereco',
        name: 'Enderecos',
        component: ManageAdresses
      },
      {
        path: 'novotelefone',
        name: 'Telefones',
        component: NewPhone
      }
    ]
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router
