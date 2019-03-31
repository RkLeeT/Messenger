<template>
    <div class="chat-app">
        <Conversation :contact="selectedContact" :selectedGroup="selectedGroup" :messages="messages" :groupMessages="groupMessages" @new="saveNewMessage" @delete="deleteMessage" :user="user"/>
        <ContactsList :contacts="contacts" :groups="groups" @selected="startConversationWith" @groupSelected="startConversationWithGroup" :user="user" />    
    </div>
</template>

<script>
    import Conversation from './Conversation'
    import ContactsList from './ContactsList'

    export default {
        props: {
            user: {
                type: Object,
                required: true
            }
        },
        data() {
            return {
                selectedContact: null,
                selectedGroup: null,
                messages: [],
                groupMessages: [],
                contacts: [],
                groups: [],
            };
        },
        mounted() {
            Echo.private(`messages.${this.user.id}`)
                .listen('NewMessage', (e) => {
                    this.handleIncoming(e.message);
                });

            axios.get('/contacts')
                .then((response) => {
                    console.log(response.data);
                    this.contacts = response.data;
                });
            axios.get('/getGroups')
                .then((response) => {
                    console.log(response.data);
                    this.groups = response.data;
                });    
        },
        methods: {
            startConversationWith(contact) {
                this.selectedGroup = null;
                this.groupMessages = null;
                if (contact == null) {
                    return;
                }
                this.updateUnreadCount(contact, true);
                axios.get(`/conversation/${contact.id}`)
                    .then((response) => {
                        this.messages = response.data;
                        this.selectedContact = contact;
                    })
            },
            startConversationWithGroup(group) {
                this.selectedContact = null;
                this.messages = null;
                if (group == null) {
                    return;
                }
                // this.updateUnreadCount(group, true);
                axios.get(`/conversation/group/${group.id}`)
                    .then((response) => {
                        this.groupMessages = response.data;
                        this.selectedGroup = group;
                    })
            },
            saveNewMessage(message) {
                if (this.contact != null) {
                    this.messages.push(message);
                }
                else {
                    this.groupMessages.push(message);
                }
            },
            deleteMessage(message, contact, selectedGroup) {
                if (contact != null) {
                    this.messages.splice(this.messages.indexOf(message.id), 1);
                }
                else {
                    this.groupMessages.splice(this.groupMessages.indexOf(message.id), 1);
                }
            },
            handleIncoming(message) {
                if (this.selectedContact && message.from == this.selectedContact.id) {
                    // this.saveNewMessage(message);
                    this.messages.push(message);
                    return;
                }

                this.updateUnreadCount(message.from_contact, false);
            },
            updateUnreadCount(contact, reset) {
                this.contacts = this.contacts.map((single) => {
                    if (single.id != contact.id) {
                        return single;
                    }

                    if (reset) {
                        single.unread = 0;
                    }
                    else {
                        single.unread = 1;
                    }
                    return single;
                })
            }
        },
        watch: {
            updateContacts(contacts) {
                axios.get('/contacts')
                .then((response) => {
                    console.log(response.data);
                    this.contacts = response.data;
                });
            }
        },
        components: {Conversation, ContactsList}
    };
</script>

<style lang="scss" scoped>
    
    .chat-app {
        display: flex;
    }

</style>