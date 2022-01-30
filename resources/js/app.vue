<template>
    <div>
        <!-- <v-app>
            <v-main>
                <v-container>
                    <router-view></router-view>
                </v-container>
            </v-main>
        </v-app> -->
        <v-app id="inspire">

            <!-- <v-app-bar app>
            <v-toolbar-title>Todo</v-toolbar-title>
            </v-app-bar> -->

            <v-main>
                <v-container>
                    <router-view></router-view>
                </v-container>
            </v-main>
        </v-app>
    </div>
</template>

<script>
    export default {
        mounted() {
            console.log('Component mounted.')
        },
    }
</script>
