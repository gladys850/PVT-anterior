<template>
  <v-container class="ma-0 pa-0">
    <v-row>
      <v-col cols="8" class="text-center">
        <v-card color="#EDF2F4" shaped class="mx-5">
          <v-card-title>Préstamos</v-card-title>
          <v-card-text>
          </v-card-text>
        </v-card>
      </v-col>
      <v-col cols="4" class="ma-0 pa-0">
        <v-card color="secondary">
           <v-card-text class="ma-0 pa-2">
            <v-col cols="12" color="#EDF2F4" class="red--text text--lighten-5 ma-0 pa-0">
              <center>
               
                <strong>PRESTATARIO:</strong> {{$options.filters.fullName(affiliate, true)}}
                <br/>
                <strong>GRADO:</strong> {{degree_name}}
                <br />
                <strong>UNIDAD:</strong> {{unit_name}}
                <br>
                <strong>MODALIDAD:</strong> {{procedure_modality_name | uppercase }}
                <br />
                <strong>MONTO SOLICITADO:</strong> {{loan.amount_requested + ' Bs'}}
                <br />
                <strong>MESES PLAZO:</strong> {{loan.loan_term}}
                <br />
               
                <!--TIPO: {{affiliate.type}}-->
                 <!-- <v-icon color="#EDF2F4">mdi-account-heart</v-icon>
                Estado Civil: {{affiliate.civil_status=='C'? 'CASADO':affiliate.civil_status=='S'? 'SOLTERO':affiliate.civil_status=='D'?'DIVORCIADO':'VIUDO'}}-->
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
  name: "flow-dashboard",
  props: {
    affiliate: {
      type: Object,
      required: true
    },
    loan:{
      type: Object,
      required: true
    }
  },
  data: () => ({
    loading: false,
    degree_name: null,
    unit_name: null,
    procedure_modality_name: ''
  }),
  computed: {
    isNew() {
      return this.$route.params.id == "new";
    }
  },

  watch: {
    affiliate(newVal, oldVal) {
      if (oldVal != newVal) {
        if (newVal.hasOwnProperty('degree_id')) this.getDegree_name(newVal.degree_id)
        if (newVal.hasOwnProperty('unit_id')) this.getUnit_name(newVal.unit_id)
      }
    },
    loan(newVal, oldVal){
      if (oldVal != newVal) {
        if (newVal.hasOwnProperty('procedure_modality_id')) this.getProcedureModalityName(newVal.procedure_modality_id)
      }
    }
  },
  methods: {
    async getDegree_name(id) {
      try {
        this.loading = true;
        let res = await axios.get(`degree/${id}`);
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
        let res = await axios.get(`unit/${id}`);
        this.unit_name = res.data.name;
      } catch (e) {
        console.log(e);
      } finally {
        this.loading = false;
      }
    },
    async getProcedureModalityName(id) {
      try {
        this.loading = true;
        let res = await axios.get(`procedure_modality/${id}`);
        this.procedure_modality_name = res.data.name
        console.log(this.procedure_modality_name)      
      } catch (e) {
        console.log(e);
      } finally {
        this.loading = false;
      }
    },
  }
};
</script>