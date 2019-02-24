<template>
	<div class="feed" ref="feed">
		<ul v-if="contact">
			<li v-for="message in messages" :class="`message${message.to == contact.id ? ' sent' : ' received'}`">
				<div class="text">
					{{ message.text }}
					<hr class="m-1">
					<span class="time">
	              		{{ message.created_at | moment }}
					</span>
				</div>
			</li>
		</ul>
	</div>
</template>

<script>
	import moment from 'moment';

	export default {
		props: {
			contact: {
				type: Object,

			},
			messages: {
				type: Array,
				required: true
			}
		},
		methods: {
			scrollToBottom() {
				setTimeout(() => {
					this.$refs.feed.scrollTop = this.$refs.feed.scrollHeight - this.$refs.feed.clientHeight;
				}, 50);
			}
		},
		watch: {
			contact(contact) {
				this.scrollToBottom();
			},
			messages(messages) {
				this.scrollToBottom();
			}
		},
	    filters: {
	    	moment(date) {
	    		return moment(date).fromNow();
	    	}
	    }
	};
</script>

<style lang="scss" scoped>
	.feed{
		background: #4c4c4c;
		height: 100%;
		max-height: 440px;
		overflow: scroll;
		overflow-x: hidden;
		ul {
			list-style-type: none;
			padding: 5px;

			li {
				&.message {
					margin: 10px 0;
				    width: auto;
				    clear: both;

					.text {
						max-width: 300px;
						border-radius: 5px;
						padding: 12px;
						display: inline-block;
						background: gray;
					}

					&.received {
						float: left;
					}
					&.sent {
						float: right;
						.text {
							background: lightslategray;

							.time {
								float: right;
							}
						}
					}
				}


			}
		}	
	}
</style>