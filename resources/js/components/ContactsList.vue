<template>

    <div class="list" style="position: relative;">
        <ProfileInfo v-if="selectInfo" :contact="contactInfo" :group="groupInfo" @cancel="closeInfo" /> 
        <div v-else class="contacts-list">
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
                        <li v-for="group in sortedGroups" :key="group.id">
                            <div class="li-div" @click="selectGroup(group)">
                                <div class="avatar">
                                    <img :src="group.profile_image" :alt="group.name">
                                </div>
                                <div class="contact">
                                    <p class="name">{{ group.name }}</p>
                                </div>
                            </div>

                            <div class="dropdown" style="align-self: center;">
                                <button class="btn btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown">
                                  </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="#" @click="displayInfo(group, 1)">Group Info</a>
                                    <a class="dropdown-item" href="#" @click="deleteGroup(group)">Delete Group</a>
                                    <a class="dropdown-item" href="#" @click="deleteGroup(group)">Exit Group</a>
                                </div>
                            </div>
                            

                        </li>
                    </ul>

                </div>
            </div>
        </div>
    </div>
	
	
</template>

<script>

    import ProfileInfo from './ProfileInfo'

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
                selectInfo: false,
                contactInfo: null,
                groupInfo: null,
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
                    alert('Group Created!');
                    this.formInfo = "";
                })
                .catch(error => {
                    if (error.response.status === 422) {
                        this.errors = error.response.data.errors || {};
                    }
                });
            },
            displayInfo(what, which) {
                this.selectInfo = !this.selectInfo;
                if(which == 1)
                    this.groupInfo = what;
                else
                    this.contactInfo = what;

            },
            closeInfo() {
                this.selectInfo = !this.selectInfo;
                this.groupInfo = null;
                this.contactInfo = null;
            },
            deleteGroup(group) {
                axios.post(`/deleteGroup/${group.id}`)
                    .then((response) => {
                        alert('Group Deleted!');
                    })
                    .catch(error => {
                        if (error.response.status === 422) {
                            this.errors = error.response.data.errors || {};
                        }
                    });

                if(this.groupSelected == group)
                {
                    this.groupSelected = null;
                    this.selectInfo = null;
                }    
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
        components: {ProfileInfo}
        
	};
</script>

<style lang="scss" scoped>
    
.list {

    flex: 2;
    height: 599px;
    overflow: scroll;
    overflow-x: hidden;
    border-left: 1px solid lightgray;
    background: #b9b9b9;

    .contacts-list {
        border-left: 1px solid lightgray;
        background: #b9b9b9;

        .tab-content {
            padding: 0;
        }

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
		
                .li-div {
                    display: flex;
                    position: relative;
                    width: 100%;
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
} 

</style>