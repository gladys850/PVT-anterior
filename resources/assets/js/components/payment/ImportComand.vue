<template>
<v-card flat>
    <v-card-title>
        <v-toolbar dense color="tertiary">
            <v-toolbar-title>Importacion Comando</v-toolbar-title>
        </v-toolbar>
    </v-card-title>
<v-card-text>
    <v-row align="center" no-gutters>
    <v-col cols="12" md="12">
    <v-card>
        <v-card-title>
        <!--<h4 class="pull-left">Importar Comando</h4>-->
        <div class="ibox-tools"></div>
        </v-card-title>
        <v-card-title>
        <input class="form-control" type="file" id="file-upload" :disabled="loadingButton"/>
        <br>
        </v-card-title>
      </v-card>
    </v-col>
    </v-row>
</v-card-text>
</v-card>
</template>

<script>

export default {
    name: "payment-ImportComand",
    data() {
    return {
        loadingButton: false,
        found: 0,
        notFound: [],
        showResults: false,
        refresh: false,
        };
    },
    methods: {
    async refreshData() {
        this.refresh = true;
        this.sendForm()
    },
    }
};
</script>

<style>
</style>
