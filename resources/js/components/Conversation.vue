<template>

	<div class="conversation">

		<h1>{{ contact ? contact.name : (selectedGroup ? selectedGroup.name : 'Select a Contact') }}
			<span v-if="contact && isTyping" style="float:right; color: #639a5d;">typing.....</span>
		</h1>
		<MessagesFeed  @deleteMsg="deleteMsg" :contact="contact" :selectedGroup="selectedGroup" :messages="messages" :groupMessages="groupMessages" :user="authUser"/>
		<MessageComposer @send="sendMessage" :selectedContact="contact" :selectedGroup="selectedGroup" :user="authUser" />
	</div>
	
</template>

<script>
	import MessagesFeed from './MessagesFeed';
	import MessageComposer from './MessageComposer';

	export default {
		props: {
			user: {
                type: Object,
            },
			contact: {
				type: Object
			},
			selectedGroup: {
				type: Object
			},
			messages: {
				type: Array,
				default: []
			},
			groupMessages: {
				type: Array,
				default: []
			}
		},
		data() {
			return {
				isTyping: false,
				authUser: this.user
			}
		},
        created() {
            Echo.private(`typing.${this.user.id}`)
                .listenForWhisper('typing', (e) => {

                	if(this.contact && (e.user.id == this.contact.id)) {
                		this.isTyping = e.typing;

                		setTimeout(() => {
                		    this.isTyping = false;
                		}, 1500);
                	}
                    
                });

        },
		methods: {
			sendMessage(text) {
				if (!this.contact && !this.selectedGroup ) {
					return;
				}

				if(this.contact != null)
				{
					axios.post('/conversation/send', {
						contact_id: this.contact.id,
						text: text
					}).then((response) => {
						this.$emit('new', response.data);
					});
				}
				else {
					axios.post('/conversation/group/send', {
						group_id: this.selectedGroup.id,
						text: text
					}).then((response) => {
						this.$emit('new', response.data);
					});
				}
			},
			deleteMsg(data) {
				if(data.contact != null)
				{
					axios.post(`/delete/msg/${data.message.id}`, {
						message: data.message,
						contact: data.contact
					}).then((response) => {
						this.$emit('delete', data);
					});
				}
				else
				{
					axios.post(`/delete/group/msg/${data.message.id}`, {
						message: data.message,
						group: data.selectedGroup
					}).then((response) => {
						this.$emit('delete', data);
					});
				}
			}
		},
		components: {MessagesFeed, MessageComposer}
	};
</script>

<style lang="scss" scoped>
    
    .conversation {
        flex: 5;
        display: flex;
        flex-direction: column;
        justify-content: space-between;

        h1 {
        	font-size: 20px;
        	padding: 10px;
        	margin: 0;
        	border-bottom: 1px dashed lightgray;
        	background: #e4e4e4;
        }
    }

</style>