<template>

	<div class="contacts-list">
		<ul>
			<li v-for="(contact, index) in contacts" :key="contact.id" @click="selectContact(index, contact)" :class="{ 'selected': index==selected }">
				<div class="avatar">
					<img :src="contact.profile_image" :alt="contact.name">
				</div>
				<div class="contact">
					<p class="name">{{ contact.name }}</p>
					<p class="email">{{ contact.email }}</p>
				</div>
			</li>
		</ul>
	</div>
	
</template>

<script>
	export default {
		props: {
			contacts: {
				type: Array,
				default: []
			}
		},
		data() {
			return {
				selected: 0
			};
		},
		methods: {
			selectContact(index, contact) {
				this.selected = index;

				this.$emit('selected', contact);
			}
		}
	};
</script>

<style lang="scss" scoped>
    
    .contacts-list {
        flex: 2;
        max-height: 650px;
        overflow: scroll;
        overflow-x: hidden;
        border-left: 1px solid lightgray;
        background: #b9b9b9;

        ul {
        	list-style-type: none;
        	padding-left: 0;

        	li {
        		display: flex;
        		padding: 2px;
        		border-bottom: 1px solid lightgray;
        		height: 80px;
        		position: relative;
        		cursor: pointer;

				&.selected {
					background: #e4e4e4;
				}
		
        		.avatar {
        		    flex: 1;
        		    display: flex;
        		    align-items: center;

        		    img {
        		        width: 40px;
        		        border-radius: 50%;
        		        margin: 0 auto;
        		    }
        		}

        		.contact {
        		    display: flex;
        		    flex-direction: column;
        		    justify-content: center;
        		    flex: 4;
        		    font-size: 12px;
        		    overflow: hidden;

        		    p {
        		    	margin: 0;

        		    	&.name {
        		    		font-weight: bold;
        		    	}
        		    }
        		}
        	}
        }
    }

</style>