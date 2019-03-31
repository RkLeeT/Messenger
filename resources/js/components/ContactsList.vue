<template>

	<div class="contacts-list">
        <input class="searchContact" type="text" placeholder="Search..">
        <ul class="nav nav-tabs" id="myTab">
            <li class="nav-item">
                <a class="nav-link active" id="all-tab" data-toggle="tab" href="#all">All</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="groups-tab" data-toggle="tab" href="#groupsTab">Groups</a>
            </li>
        </ul>

        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="all">
                <ul class="innerList">
                    <li v-for="contact in sortedContacts" :key="contact.id" @click="selectContact(contact)" :class="{ 'selected': contact == selected }">
                        <div class="avatar">
                            <img :src="contact.profile_image" :alt="contact.name" :class="{'online': onlineUsers.find(onlineUser=>onlineUser.id === contact.id) ? 1:0}">
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


            <div class="tab-pane fade" id="groupsTab">
                <form @submit.prevent="submit">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" name="name" id="name" v-model="formInfo.name" value="PATCH"/>
                    </div>
                    <button type="submit" class="btn btn-primary">Create Group</button>
                </form>
                
                <ul class="innerList">
                    <li v-for="group in sortedGroups" :key="group.id" @click="selectGroup(group)">
                        <div class="avatar">
                            <img :src="group.profile_image" :alt="group.name">
                        </div>
                        <div class="contact">
                            <p class="name">{{ group.name }}</p>
                        </div>
                    </li>
                </ul>

            </div>
        </div>
     


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
			},
            groups: {
                type: Array,
                default: []
            },
		},
		data() {
			return {
				selected: this.contacts.length ? this.contacts[0] : null,
                groupSelected: this.groups.length ? this.groups[0] : null,
                userTyping: null,
                onlineUsers: [],
                formInfo: {},
			};
		},
        created() {

            Echo.join(`privateStatus`)
                .here((users) => {
                    this.onlineUsers = users;
                    console.log("Online: ",users);
                })
                .joining((user) => {
                    this.onlineUsers.push(user);
                    console.log("Joining: ",user.name);
                })
                .leaving((user) => {
                    this.onlineUsers.splice(this.onlineUsers.indexOf(user), 1);
                    console.log("Leaving: ",user.name);
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
                // this.groupSelected = null;
                // this.$emit('groupSelected', null);
				this.selected = contact;
				this.$emit('selected', contact);
			},
            selectGroup(group) {
                // this.selected = null;
                // this.$emit('selected', null);
                this.groupSelected = group;
                this.$emit('groupSelected', group);
            },
            submit() {
                axios.post('/createGroup', this.formInfo).then(response => {
                    alert('Message sent!');
                    this.formInfo = "";
                })
                .catch(error => {
                    if (error.response.status === 422) {
                        this.errors = error.response.data.errors || {};
                    }
                });
            },
		},
        computed: {
            sortedContacts() {
                return _.sortBy(this.contacts, [(contact) => {
                    // if (contact == this.selected) {
                    //     return Infinity;
                    // }

                    return contact.latest;
                    // return contact.created_at;
                }]).reverse();
            },
            sortedGroups() {
                return _.sortBy(this.groups, [(group) => {
                    return group.created_at;
                }]).reverse();
            }
        },
        watch: {
            updateContacts(contacts) {
                this.sortedContacts();
            }
        },
        
	};
</script>

<style lang="scss" scoped>
    
    .contacts-list {
        flex: 2;
        height: 592px;
        overflow: scroll;
        overflow-x: hidden;
        border-left: 1px solid lightgray;
        background: #b9b9b9;

        .searchContact {
            width: 100%;
            height: 33px;
            background: #9ea7af;
            color: white;
            padding: 20px;
            font-size: 20px;
            border: 2px solid #cce3f6;

            &::placeholder {
                color: white;
            }
        }

        ul.nav {
            li {
                text-align: center;
                width: 50%;
                color: #6c757d;
                font-size: 17px;
                box-sizing: border-box;
                font-weight: bold;
            }
        }

        ul.innerList {
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
                    background: #cce3f6;
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
                        border: 3px solid #909090;

                        &.online {
                            border: 3px solid #cce3f6;
                        }
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