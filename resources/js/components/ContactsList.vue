<template>

	<div class="contacts-list">

        <ul>
            <li v-for="contact in sortedContacts" :key="contact.id" @click="selectContact(contact)" :class="{ 'selected': contact == selected }">
                <div class="avatar">
                    <img :src="contact.profile_image" :alt="contact.name">
                </div>
                <div class="contact">
                    <p class="name">{{ contact.name }}</p>
                    <p class="email">{{ contact.email }}</p>
                    <span v-if="userTyping == contact.id" class="typing">typing...</span>
                </div>
                <span class="unread" v-if="contact.unread">{{ contact.unread }}</span>
            </li>
        </ul>


	</div>
	
</template>

<script>
	export default {
		props: {
            user: {
                type: Object
            },
			contacts: {
				type: Array,
				default: []
			}
		},
		data() {
			return {
				selected: this.contacts.length ? this.contacts[0] : null,
                userTyping: null
			};
		},
        created() {

            Echo.join(`privateStatus`)
                .here((users) => {
                    console.log(users.name);
                })
                .joining((user) => {
                    console.log(user.name);
                })
                .leaving((user) => {
                    console.log(user.name);
                });

            Echo.private(`typing.${this.user.id}`)
                .listenForWhisper('typing', (e) => {

                    this.userTyping = e.user.id;

                    setTimeout(() => {
                        this.userTyping = null;
                    }, 1500);
                });
        },
		methods: {
			selectContact(contact) {
				this.selected = contact;

				this.$emit('selected', contact);
			}
		},
        computed: {
            sortedContacts() {
                return _.sortBy(this.contacts, [(contact) => {
                    // if (contact == this.selected) {
                    //     return Infinity;
                    // }

                    return contact.unread;
                    // return contact.created_at;
                }]).reverse();
            }
        }
	};
</script>

<style lang="scss" scoped>
    
    .contacts-list {
        flex: 2;
        max-height: 590px;
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
		
                span.unread {
                    background: #85e085;
                    color: white;
                    min-width: 20px;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    position: absolute;
                    right: 5px;
                    top: 25px;
                    font-weight: 700;
                    font-size: 13px;
                    line-height: 20px;
                    padding: 0 6px;
                    border-radius: 5px;
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

                    .typing {
                        position: absolute;
                        top: 7px;
                        right: 10px;
                        color: #cbffc5;
                    }
        		}
        	}
        }
    }

</style>