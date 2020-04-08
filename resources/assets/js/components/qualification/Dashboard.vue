<template>
  <v-container class="ma-0 pa-0">
    <v-row>
      <v-col cols="8" class="text-center">
        <v-card color="#EDF2F4" shaped class="mx-5">
          <v-card-title>Calificacion-Prestamos</v-card-title>
          <v-card-text>
          </v-card-text>
        </v-card>
      </v-col>
      <v-col cols="4" class="ma-0 pa-0">
        <v-card color="secondary">
           <v-card-text class="ma-0 pa-0">
            <v-col cols="12" color="#EDF2F4" class="red--text text--lighten-5 ma-0 pa-0">
              <center>
                Grado: {{this.degree_name}}
                <br/>
                Unidad: {{this.unit_name}}
                <br />
                <!--TIPO: {{affiliate.type}}-->
                 <!-- <v-icon color="#EDF2F4">mdi-account-heart</v-icon>-->
                Estado Civil: {{affiliate.civil_status=='C'? 'CASADO':affiliate.civil_status=='S'? 'SOLTERO':affiliate.civil_status=='D'?'DIVORCIADO':'VIUDO'}}
              </center>
            </v-col>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import common from "@/plugins/common";
export default {
  name: "qualification-dashboard",
  props: {
    affiliate: {
      type: Object,
      required: true
    }
  },
  data: () => ({
    loading: false,
    degree_name: null,
    unit_name: null
  }),
  computed: {
    isNew() {
      return this.$route.params.id == "new";
    }
  },
  mounted() {
    if (!this.isNew) {
      this.getDegree_name(this.$route.params.id)
      this.getUnit_name(this.$route.params.id)
    }
  },
  methods: {
    async getDegree_name(id) {
      try {
        this.loading = true;
        let res = await axios.get(`affiliate/${id}/degree`);
        this.degree_name = res.data.name;
      } catch (e) {
        console.log(e);
      } finally {
        this.loading = false;
      }
    },
    async getUnit_name(id) {
      try {
        this.loading = true;
        let res = await axios.get(`affiliate/${id}/unit`);
        this.unit_name = res.data.name;
      } catch (e) {
        console.log(e);
      } finally {
        this.loading = false;
      }
    },
  }
};
</script>