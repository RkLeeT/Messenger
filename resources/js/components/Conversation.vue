<template>

	<div class="conversation">
		<h1>{{ contact ? contact.name : 'Select a Contact' }}
			<span v-if="isTyping" style="float:right; color: #639a5d;">typing.....</span>
		</h1>
		<MessagesFeed :contact="contact" :messages="messages" />
		<MessageComposer @send="sendMessage" :selectedContact="contact" :user="authUser" />
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
			messages: {
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
				if (!this.contact) {
					return;
				}

				axios.post('/conversation/send', {
					contact_id: this.contact.id,
					text: text
				}).then((response) => {
					this.$emit('new', response.data);
				});
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