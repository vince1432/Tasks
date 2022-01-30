<template>
    <div>
        <!-- Task List -->
        <v-card
            class="mx-auto mt-6"
        >
            <!-- Header -->
            <v-toolbar
                color="pink"
                dark
                >
                <v-toolbar-title>Todo</v-toolbar-title>
                <v-spacer></v-spacer>
                <v-spacer></v-spacer>
                <v-spacer></v-spacer>
                <v-menu
                                v-model="date_menu_search"
                                :close-on-content-click="false"
                                :nudge-right="40"
                                transition="scale-transition"
                                class="text-center startDate"
                            >
                                <template v-slot:activator="{ on, attrs }">
                                <v-text-field
                                class="text-center"
                                    v-model="search_date"
                                    hide-details
                                    readonly
                                    v-bind="attrs"
                                    v-on="on"
                                ></v-text-field>
                                </template>
                                <v-date-picker
                                v-model="search_date"
                                @input="date_menu = false"
                                @change="getTasks"
                                ></v-date-picker>
                            </v-menu>
                <v-spacer></v-spacer>
                <v-spacer></v-spacer>
                <v-spacer></v-spacer>
                <v-icon color="white lighten-1" v-on:click="showModal('add')">
                    mdi-clipboard-plus
                </v-icon>
            </v-toolbar>
            <v-list
            flat
            subheader
            two-line
            >
            <div v-if="tasks.length">
                <!-- Items -->
                <v-list-item-group
                    v-model="selected"
                    multiple
                    active-class=""
                    v-for="(task, index) in tasks" :key="task.id"
                >
                    <!-- add devider to second item and uo -->
                    <v-divider v-if="index != 0"></v-divider>
                    <v-list-item>
                        <template v-slot:default="{ active }">
                            <v-list-item-action>
                                <v-checkbox :input-value="active" v-model="task.is_done" v-on:click="updateStatus(task)"></v-checkbox>
                            </v-list-item-action>

                            <v-list-item-content>
                                <v-list-item-title>{{task.name}}</v-list-item-title>
                                <v-list-item-subtitle>{{task.description}}</v-list-item-subtitle>
                                <!-- <v-icon>{{ icons.mdiAccount }}</v-icon> -->
                            </v-list-item-content>

                            <v-icon color="grey lighten-1" v-on:click="showModal('update', task)">
                                mdi-pencil
                            </v-icon>
                            <v-icon color="grey lighten-1" v-on:click="deleteTask(task)">
                                mdi-delete
                            </v-icon>
                        </template>
                    </v-list-item>
                </v-list-item-group>
            </div>
            <!-- If tasks is empty -->
            <div v-else>
                <v-list-item-group>
                    <v-list-item>
                        <v-list-item-content>
                            <v-list-item-title class="text-center">No Task for Today</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                </v-list-item-group>
            </div>
            </v-list>
        </v-card>

        <!-- Modal -->
        <v-row justify="center">
            <v-dialog
            v-model="dialog"
            persistent
            max-width="600px"
            >
            <v-card>
                <v-card-title>
                <span class="text-h5">Task</span>
                </v-card-title>
                <v-card-text>
                <v-container>
                    <v-row>
                        <v-col cols="12">
                            <v-text-field
                                label="Task*"
                                v-model="task.name"
                                persistent-hint
                                required
                                maxlength=25
                            ></v-text-field>
                        </v-col>
                        <v-col cols="12">
                           <v-menu
                                v-model="date_menu"
                                :close-on-content-click="false"
                                :nudge-right="40"
                                transition="scale-transition"
                                offset-y
                                min-width="auto"
                            >
                                <template v-slot:activator="{ on, attrs }">
                                <v-text-field
                                    v-model="task.date"
                                    label="Date*"
                                    readonly
                                    v-bind="attrs"
                                    v-on="on"
                                ></v-text-field>
                                </template>
                                <v-date-picker
                                v-model="task.date"
                                @input="date_menu = false"
                                ></v-date-picker>
                            </v-menu>
                        </v-col>
                        <v-col cols="12">
                            <v-textarea
                            label="Description"
                            maxlength=255
                            v-model="task.description"
                            ></v-textarea>
                        </v-col>
                    </v-row>
                </v-container>
                </v-card-text>
                <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn
                    color="blue darken-1"
                    text
                    @click="dialog = false"
                >
                    Close
                </v-btn>
                <v-btn
                    color="blue darken-1"
                    text
                    @click="(type == 'update') ? updateTask(task) : createTask()"
                >
                    Save
                </v-btn>
                </v-card-actions>
            </v-card>
            </v-dialog>
        </v-row>
    </div>
</template>

<script>
export default {
    data: () => {
        return {
            date_menu_search: false,
            search_date: (new Date(Date.now() - (new Date()).getTimezoneOffset() * 60000)).toISOString().substr(0, 10),
            task: {
                date: (new Date(Date.now() - (new Date()).getTimezoneOffset() * 60000)).toISOString().substr(0, 10),
            },
            tasks: [],
            selected: [],
            dialog: false,
            date_menu: false,
            type: 'add'
        }
    },
    mounted: function()  {
        this.getTasks();
    },
    methods:  {
        getTasks() {
            this.$http.get(`/api/task?date=${this.search_date}`)
                .then((result) => {
                    this.tasks = result.data.data;
                }).catch((err) => {
                    this.$swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Internal Server error',
                    });
                });
        },
        getTask() {
            this.$http.patch(`/api/task/${this.task.id}`, this.task)
                .then((result) => {
                    this.task = result.data.data;
                }).catch((err) => {
                    this.$swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Internal Server error',
                    });
                });
        },
        createTask() {
            this.$swal.fire({
                title: "Add Task?",
                text: `This will add this task.`,
                icon: "question",
                showCancelButton: true,
                cancelButtonText : `Cancel`,
                confirmButtonText : `Save`
            })
            .then((result) => {
                if(result.isConfirmed){
                    this.$http.post('/api/task', this.task)
                        .then((result) => {
                            if(this.search_date == this.task.date)
                                this.tasks.push(result.data.data);
                            this.$swal.fire({
                                title: 'Task Created',
                                icon: 'success',
                                confirmButtonText: 'OK',
                            })
                            this.dialog = false;
                        }).catch((err) => {
                            if(err.response.status == 422) {
                                this.$swal.fire({
                                    icon: 'error',
                                    title: 'Validation error',
                                    text: 'Please fill all required data.',
                                });
                            }
                            else {
                                this.$swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Internal Server error',
                                });
                            }
                        });
                }
                else{
                    return false;
                }
            });
        },
        updateTask(task) {
            this.$swal.fire({
                title: "Update Task?",
                text: `This will update this task.`,
                icon: "question",
                showCancelButton: true,
                cancelButtonText : `Cancel`,
                confirmButtonText : `Save`
            })
            .then((result) => {
                if(result.isConfirmed){
                    this.task = task;
                    this.task.is_done = null;
                    this.$http.patch(`/api/task/${this.task.id}`, this.task)
                        .then((result) => {
                            // remove if diff date
                            if(this.search_date != this.task.date) {
                                //get the index of the item
                                let index = this.tasks.map(x => {
                                    return x.id;
                                }).indexOf(this.task.id);
                                // remove uusign the retrieved insdex
                                this.tasks.splice(index, 1);
                            }

                            this.$swal.fire({
                                title: 'Task Updated',
                                icon: 'success',
                                confirmButtonText: 'OK',
                            });
                            this.dialog = false;
                        }).catch((err) => {
                            if(err.response.status == 422) {
                                this.$swal.fire({
                                    icon: 'error',
                                    title: 'Validation error',
                                    text: 'Please fill all required data.',
                                });
                            }
                            else {
                                this.$swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Internal Server error',
                                });
                            }
                        });
                }
                else{
                    return false;
                }
            });

        },
        updateStatus(task) {
            task.is_done = task.is_done ? 1 : 0;
            this.task = task;
            this.$http.patch(`/api/task/${task.id}`, task)
                .then((result) => {

                }).catch((err) => {
                    this.$swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Internal Server error',
                    });
                });
        },
        deleteTask(task) {
            this.$swal.fire({
                title: "Delete Task?",
                text: `This will delete this task.`,
                icon: "question",
                showCancelButton: true,
                cancelButtonText : `Cancel`,
                confirmButtonText : `Save`
            })
            .then((result) => {
                if(result.isConfirmed){
                    this.task = task;
                    this.$http.delete(`/api/task/${this.task.id}`)
                        .then((result) => {
                            //get the index of the item
                            let index = this.tasks.map(x => {
                                return x.id;
                            }).indexOf(this.task.id);
                            // remove uusign the retrieved insdex
                            this.tasks.splice(index, 1);
                            this.$swal.fire({
                                title: 'Task Removed',
                                icon: 'success',
                                confirmButtonText: 'OK',
                            })
                        })
                        .catch((err) => {
                            this.$swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Internal Server errors',
                            });
                        });
                }
                else{
                    return false;
                }
            });
        },
        showModal(type, task = {}) {
            this.type = type;
            this.dialog = true;
            this.task = task;
        }
    }
}
</script>
