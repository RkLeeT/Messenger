<template>

    <div class="profileInfo">
        <div class="head">
            <h3>Group Info</h3>
            <span @click="cancel">X</span>
        </div>
        <div class="body">
            <div class="upper">
                <div class="group-avatar">
                    <img :src="group.profile_image" :alt="group.name">
                </div>
                <div class="title">
                    <p class="name">{{ group.name }}</p>
                    <span>Edit</span>
                </div>
                
            </div>
            <hr>
            <div class="participants">

                

                <p>Participants: {{ groupUsers.length }}</p>
                <ul class="innerList">
                    <li v-for="groupUser in groupUsers">
                        <div class="avatar">
                            <img :src="groupUser.profile_image" :alt="groupUser.name">
                        </div>
                        <div class="contact">
                            <p class="name">{{ groupUser.name }}</p>
                            <p class="email">{{ groupUser.email }}</p>
                        </div>
                    </li>
                </ul>
                <span @click="initialise" style="cursor: pointer;">Click: Add Participants</span>
                <div v-if="bool">

                    <multiselect v-model="addUsers" :options="xxx" :multiple="true" :close-on-select="false" :clear-on-select="false" :preserve-search="true" placeholder="Pick some" label="name" track-by="name" :preselect-first="true"></multiselect>

                    <button class="btn btn-default" @click="addUsersToGroup(group)">Add Selected</button>

                </div>

            </div>
            <hr>
            <a href="#">Exit Group</a>
            <hr>
            <a href="#">Delete Group</a>
        </div>
        
        

    </div>
	
	
</template>

<script>

    import Multiselect from 'vue-multiselect';

	export default {
		props: {
			contact: {
				type: Object
			},
            group: {
                type: Object
            },
		},
		data() {
			return {
                groupUsers: [],
                otherUsers: [],
                xxx: [],
                selected: [],
                bool: false,
                addUsers: [],
                 options: [
                   { name: 'Vue.js', language: 'JavaScript' },
                   { name: 'Adonis', language: 'JavaScript' },
                   { name: 'Rails', language: 'Ruby' },
                   { name: 'Sinatra', language: 'Ruby' },
                   { name: 'Laravel', language: 'PHP' },
                   { name: 'Phoenix', language: 'Elixir' }
                 ]
			};
		},
        beforeCreate() {
            this.$nextTick(function(){
                $('.selectpicker').selectpicker('refresh');

                axios.get(`/getGroupInfo/${this.group.id}`)
                    .then((response) => {
                        this.groupUsers = response.data.group_users;
                        this.otherUsers = response.data.other_users;
                        console.log(response.data);

                    });
            });
        },
		methods: {
            cancel() {
                this.$emit('cancel');
            },
            initialise() {
                for (let i = 0; i < this.otherUsers.length; i++)
                {
                    Vue.set(this.xxx, i, this.otherUsers[i]);
                }

                this.bool = !this.bool;
            },
            addUsersToGroup(group){
                if(this.addUsers == null)
                    return ;
                else{
                    axios.post(`/addUsersToGroup/${group.id}`, this.addUsers)
                        .then((response) => {
                            console.log(response.data);
                        })
                }
            }
		},
        components: {
            Multiselect
        },
	};
</script>

<style lang="scss" scoped>



    .profileInfo {
        height: 100%;
        width: 100%;
        padding: 15px;
        display: flex;
        flex-direction: column;

        .head{
            display: flex;
            justify-content: space-between;

            span{
                align-self: center;
                cursor: pointer;
            }
        }

        .body {
            .upper {
                display: flex;
                flex-direction: column; 

                .group-avatar {
                    display: flex;
                    align-items: center;

                    img {
                        width: 200px;
                        border-radius: 50%;
                        margin: 0 auto;
                        border: 3px solid #909090;
                    }
                }

                .title {
                    display: flex;
                    justify-content: space-between;
                    font-size: 15px;
                }
            }
            
            .participants {

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
                


                .selectpicker {
                    display: block !important;
                }
    
            }

        }


    }

</style>