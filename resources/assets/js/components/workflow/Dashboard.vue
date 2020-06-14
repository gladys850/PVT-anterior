<template>
  <v-container  fluid class="ma-0 pa-0">
    <v-row>
      <v-col cols="6" class="text-center">
        <v-card
          class="py-0"
          color="#173B0B"
          dark
          max-width="600"
           max-height="1000"
        >
          <v-card-text class="headline font-weight-bold">
            <v-icon
              large
              left
              style="font-size: 150px;"
            >
              mdi-shield-account
            </v-icon>
             <h6><strong>PRESTATARIO:</strong> {{$options.filters.fullName(affiliate, true)}}</h6>
              <h6><strong>GRADO:</strong> {{degree_name}}</h6>
             <h6><strong>UNIDAD:</strong> {{unit_name}}</h6>
          </v-card-text>
        </v-card>
      </v-col>
      <v-col cols="6" class="text-center">
        <v-row>
          <v-col cols="6" class="text-center py-0">
          <v-card
            class="py-0"
            color="#585858"
            dark
            max-height="135"
          >
            <v-card-text class="headline font-weight-bold">
              <v-icon
                large
                left
                 style="font-size: 50px;"
              >
                mdi-currency-usd
              </v-icon>
              <h5>    <strong>MONTO SOLICITADO:</strong>  </h5>
              <h5>{{loan.amount_requested + ' Bs'}}
            </h5>
            </v-card-text>
          </v-card>
        </v-col>
        <v-col cols="6" class="text-center py-0">
          <v-card
            class="mx-auto"
            color="#424242"
            dark
            max-height="135"
          
          >
            <v-card-text class="headline font-weight-bold">
              <v-icon
                large
                left
                              style="font-size: 50px;"
    
              >
                mdi-timer-sand
              </v-icon>
               <h5>    <strong>MESES PLAZO:</strong> </h5><h5>{{loan.loan_term}}</h5>
          </v-card-text>
          </v-card>
        </v-col>
        <v-col cols="12" class="text-center ">
  <v-card
    class="mx-auto"
    color="#151515"
    dark
   max-height="285"
  >
  
      <v-icon
        large
        left

                              style="font-size: 50px;"
      >
        mdi-bank
      </v-icon>
  

    <v-card-text class="headline font-weight-bold">
  
         <strong>MODALIDAD:</strong> {{procedure_modality_name | uppercase }}
          
     </v-card-text>
  </v-card>
   </v-col>
  
      </v-row>
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