<template>
  <v-container fluid >
    <v-row justify="center">
      <v-col cols="4" class="py-0" >
        <v-card>
          <v-container class="py-0">
            <v-row>
              <v-col cols="12" md="4"></v-col>
              <v-col cols="12" md="6" >
                Afiliado
              </v-col>
              <v-col cols="12" md="2"></v-col>
              <v-col cols="12" md="1"></v-col>
              <v-col cols="12" md="8" >
                <v-text-field
                  label="C.I."
                  class="py-0"
                  single-line
                  hide-details
                  clearable
                ></v-text-field>
              </v-col>
              <v-col cols="12" md="2">
                <v-tooltip>
                  <template v-slot:activator="{ on }">
                    <v-btn
                      fab
                      dark
                      x-small
                      v-on="on"
                      color="info"
                      @click.stop=""
                    >
                      <v-icon>mdi-magnify</v-icon>
                    </v-btn>
                  </template>
                </v-tooltip>
              </v-col>
            </v-row>
          </v-container>
        </v-card>
      </v-col>
      <v-col cols="8" class="py-0" >
        <v-card>
          <v-container class="py-0">
            <v-row>
              <v-col cols="12" md="4"></v-col>
              <v-col cols="12" md="6" >
                Afiliado
              </v-col>
              <v-col cols="12" md="2"></v-col>
              <v-col cols="12" md="2"></v-col>
              <v-col cols="12" md="10" >
                <v-text-field
                  append-icon="mdi-magnify"
                  label="C.I."
                  class="mr-5 pr-5"
                  single-line
                  hide-details
                  clearable
                ></v-text-field>
              </v-col>
            </v-row>
          </v-container>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>
<script>
  export default {
  name: "loan-information",
  data: () => ({
    modalities: [
      { name:"Anticipo",
        value:"1"
      },
      { name:"Corto Plazo",
        value:"2"
      },
      { name:"Largo Plazo",
        value:"3"
      },
      { name:"Hipotecario",
        value:"4"
      }
    ],
  }),
  computed: {
  },
  beforeMount() {
  },
  mounted() {
  },
  methods: {
  }
  }
</script>