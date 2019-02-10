<template>
	<div class="composer">
		<textarea v-model="message" @keydown.enter="send" placeholder="Message.."></textarea>
	</div>
</template>

<script>
	export default {
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