<template>
	<div class="composer">
		<textarea v-model="message" @keyup="typingEvent" @keyup.enter="send" placeholder="Message.."></textarea>
	</div>
</template>

<script>
	export default {
		props: {
			user: {
                type: Object
            },
            selectedContact: {
				type: Object
			}
        },
		data() {
			return {
				message: ''
			};
		},
		methods: {
			send(e) {
				e.preventDefault();

				if (this.message == '') {return;}

				this.$emit('send', this.message);
				this.message = '';
			},
			typingEvent() {

			    Echo.private(`typing.${this.selectedContact.id}`)
			    	.whisper('typing', {
		    	        user: this.user,
        				typing: true
		    	    });

			}
		}
	};
</script>

<style lang="scss" scoped>
	.composer {
		background: #b9b9b9;
		
		textarea {
			width: 95%;
			margin: 20px;
			padding: 6px;
			resize: none;
			border-radius: 3px;
			border: 1px solid lightgray;
			background: #e4e4e4;
		}
	}
</style>