<template>
	<div class="feed" ref="feed">
		<ul v-if="contact">
			<li v-for="(message, index) in messages" @mouseover="hover(index)" @mouseout="hover(-1)" :class="`message${message.to == contact.id ? ' sent' : ' received'}`">
				<div class="text">
					<span v-if="{'selected': isSelected(index)}" class="closeButton float-right clearfix" @click="deleteMsg(message, contact, selectedGroup)">X</span>
					{{ message.text }}
					<hr class="m-1">
					<span class="time">
	              		{{ message.created_at | moment }}
					</span>
				</div>
			</li>
		</ul>

		<ul v-if="selectedGroup">
			<li v-for="(message, index) in groupMessages" @mouseover="hover(index)" @mouseout="hover(-1)" :class="`message${message.user_id == user.id ? ' sent' : ' received'}`">
				<div class="text">
					<span v-if="{'selected': isSelected(index)}" class="closeButton float-right clearfix" @click="deleteMsg(message, contact, selectedGroup)">X</span>
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
			selectedGroup: {
                type: Object
            },
			user: {
                type: Object
            },
			contact: {
				type: Object,
			},
			messages: {
				type: Array,
				required: false
			},
			groupMessages: {
				type: Array,
				required: false
			},
		},
		data() {
			return {
				hoverli : -1
			}
		},
		methods: {
			scrollToBottom() {
				setTimeout(() => {
					this.$refs.feed.scrollTop = this.$refs.feed.scrollHeight - this.$refs.feed.clientHeight;
				}, 50);
			},
			deleteMsg(message, contact, selectedGroup)
			{
				this.$emit('deleteMsg', {
					'message': message, 
					'contact': contact, 
					'selectedGroup': selectedGroup
				});
			},
			hover(selectedIndex)
			{
			    this.hoverli = selectedIndex
			},

			isSelected(index)
			{
			    return this.hoverli === index
			}
		},
		watch: {
			contact(contact) {
				this.scrollToBottom();
			},
			messages(messages) {
				this.scrollToBottom();
			},
			groupMessages(groupMessages) {
				this.scrollToBottom();
			},
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
		height: 447px;
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
					.closeButton {
						cursor: pointer;
					}
				}


			}
		}	
	}
</style>