<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Chat
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg flex" style="min-height: 400px; max-height: 400px;">

                    <!-- List Users -->
                    <div class="w-3/12 bg-gray-200 bg-opacity-25 border-r border-gray-200 overflow-y-scroll"> <!-- Trabalharemos com as Classes do Tailwind (biblioteca css) -->
                        <ul>
                            <li v-for="user in users" :key="user.id"
                                @click="() => {loadMessages(user.id)}"
                                class="p-6 text-lg text-gray-600 leading-7 font-semibold border-b border-gray-200 hover:bg-gray-200 hover:bg-opacity-50 hover:cursor-pointer"
                                :class="(userActive && userActive.id == user.id) ? 'bg-gray-200 bg-opacity-50' : ''">
                                <p class="flex item-center">
                                   {{ user.name }}
                                    <span v-if="user.notification" class="ml-2 w-2 h-2 bg-blue-500 rounded-full"></span>
                                </p>
                            </li>
                        </ul>
                    </div>

                    <!-- Box Message -->
                    <div class="w-9/12 flex flex-col justify-between">

                        <!-- Message -->
                        <div class="w-full p-6 flex flex-col overflow-y-scroll">
                            <div v-for="message in messages" :key="message.id"
                                 :class="(message.from == $attrs.auth.user.id) ? 'text-right' : ''"
                                class="w-full mb-3 message">
                                    <p
                                        :class="(message.from == $attrs.auth.user.id) ? 'messageFromMe' : 'messageToMe'"
                                        class="inline-block p-2 rounded-md"
                                        style="max-width: 75%;">
                                        {{ message.content }}
                                    </p>
                                    <span class="block mt-1 text-xs text-gray-500">{{ formatDate(message.created_at) }}</span>
                            </div>
                        </div>

                        <!-- form -->
                        <div v-if="userActive" class="w-full bg-gray-200 bg-opacity-25 p-6 border-t border-gray-200">
                            <form v-on:submit.prevent="sendMessage">
                                <div class="flex rounded-md overflow-hidden border border-gray-300">
                                    <input v-model="message" type="text" class="flex-1 px-4 py-2 text-sm focus:outline-none">
                                    <button type="submit" class="bg-indigo-500 hover:bg-indigo-600 text-white px-4 py-2">Enviar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
    // import Vue from 'vue'
    import AppLayout from '@/Layouts/AppLayout'
    import moment from 'moment'
    import store from '../store/store.js'

    export default {
        components: {
            AppLayout,
        },
        data () {
            return {
                users: [],
                messages: [],
                userActive: null,
                message:''
            }
        },
        computed: {
            user () {
                return store.state.user
            }
        },
        methods: {
            scrollToBottom () {
                // Método de deixar sempre o scroll embaixo, após uma nova mensagem
                if ( this.messages.length ) {
                    document.querySelectorAll('.message:last-child')[0].scrollIntoView() // Pegando o último elemento que tem essa classe e dando scroll até ela
                }
            },
            loadMessages: async function(userId) {

                // Pegando os dados de um usuário
                axios.get(`api/users/${userId}`).then(response => {
                    this.userActive = response.data.user
                })

                // Pegando as mensagens do usuário daquele id
                await axios.get(`api/messages/${userId}`).then(response => {
                    this.messages = response.data.messages
                })

                // Limpando a bolinha de mensagem recebida quando clicamos no respectivo chat da pessoa
                const user = this.users.filter((user) => {

                    if (user.id === userId) {
                        return user
                    }
                })

                // Quando encontrar um usuário
                if (user) {
                    // user.notification = true (Deveria ser reativo, mas não vai funcionar, então...)
                    // Vamos setar da forma abaixo para ser reativo
                    //Vue.set(user[0], 'notification', true)
                    user[0].notification = false
                }

                // Descendo o scroll
                this.scrollToBottom()
            },
            sendMessage: async function () {

                // Enviando e cadastrando mensagem
                await axios.post('api/messages/store',{
                    'content': this.message,
                        'to': this.userActive.id
                }).then(response => {

                    // Pegamos o retorno com a nova mensagem e atualizamos as mensagens do chat
                    this.messages.push({
                        'from': this.user.id, // Pegando esse do Store
                        'to': this.userActive.id,
                        'content': this.message,
                        'created_at': new Date().toISOString(),
                        'updated_at': new Date().toISOString()
                    })

                    this.message = '' // Limpando o campo de mensagem
                })

                this.scrollToBottom()

            },
            formatDate: function(date) {
                return moment(date).format("DD/MM/YYYY HH:mm");
            }
        },
        mounted () {

            axios.get('api/users').then(response => {
                this.users = response.data.users
            })

            // Se conectando a um canal (no caso aqui, privado) | canal user.id
            // Usando o ponto ali, evita de ter que colocar o namespace todo
            Echo.private(`user.${this.user.id}`).listen('.SendMessage', async (data) => {

                if (this.userActive && this.userActive.id === data.message.from) {
                    await this.messages.push(data.message)
                    this.scrollToBottom()
                } else {
                    // Colocando a bolinha de nova mensagem
                    const user = this.users.filter((user) => {
                        // Quando o usuário que estivermos percorrendo, for igual ao usuário que enviou a mensagem
                        if (user.id === data.message.from) {
                            return user
                        }
                    })

                    // Quando encontrar um usuário
                    if (user) {
                        // user.notification = true (Deveria ser reativo, mas não vai funcionar, então...)
                        // Vamos setar da forma abaixo para ser reativo
                        //Vue.set(user[0], 'notification', true)
                        user[0].notification = true
                    }
                }

                console.log(data)
                console.log('O evento retornou')
            }) // Esse evento é aquele que nomeamos no método broadcastAs

        }
    }
</script>

<style>



</style>
