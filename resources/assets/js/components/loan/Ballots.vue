<template>
  <v-flex xs12 class="px-3">
    <v-row justify="center">
      <v-col cols="12"  >
        <v-card>
          <v-container class="py-0">
            <v-row>
              <v-col cols="12" md="9" >
                BOLETAS DE PAGO
              </v-col>
              <v-col cols="12" md="4" class="py-0"  >
                <v-text-field
                  dense
                  v-model="payable_liquid[0]"
                  v-validate.initial="'min:1|max:20'"
                  :error-messages="errors.collect('1raBoleta')"
                  data-vv-name="1raBoleta"
                  label="1ra Boleta"
                  :readonly="!editar"
                  :outlined="editar"
                ></v-text-field>
              </v-col>
              <v-col cols="12" md="4" class="py-0" v-if="visible">
                <v-text-field
                  dense
                  v-model="payable_liquid[1]"
                  v-validate.initial="'min:1|max:20'"
                  :error-messages="errors.collect('2daBoleta')"
                  data-vv-name="2daBoleta"
                  label="2ra Boleta"
                  :readonly="!editar"
                  :outlined="editar"
                ></v-text-field>
              </v-col>
              <v-col cols="12" md="4" class="py-0" v-if="visible" >
                <v-text-field
                  dense
                  v-model="payable_liquid[2]"
                  v-validate.initial="'min:1|max:20'"
                  :error-messages="errors.collect('3raBoleta')"
                  data-vv-name="3raBoleta"
                  label="3ra Boleta"
                  :readonly="!editar"
                  :outlined="editar"
                ></v-text-field>
              </v-col>
              <v-col cols="12" class="py-0" >
                BONOS
              </v-col>
              <v-col cols="12" md="3" >
                <v-text-field
                  dense
                  v-model="border_bonus"
                  v-validate.initial="'min:1|max:20'"
                  :error-messages="errors.collect('1erBono')"
                  data-vv-name="1erBono"
                  label="Bono Frontera"
                  :readonly="!editar"
                  :outlined="editar"
                ></v-text-field>
              </v-col>
              <v-col cols="12" md="3">
                <v-text-field
                  dense
                  v-model="east_bonus"
                  v-validate.initial="'min:1|max:20'"
                  :error-messages="errors.collect('2doBono')"
                  data-vv-name="2doBono"
                  label="Bono Oriente"
                  :readonly="!editar"
                  :outlined="editar"
                ></v-text-field>
              </v-col>
              <v-col cols="12" md="3" >
                <v-text-field
                  dense
                  v-model="seniority_bonus"
                  v-validate.initial="'min:1|max:20'"
                  :error-messages="errors.collect('3erBono')"
                  data-vv-name="3erBono"
                  label="Bono Cargo"
                  :readonly="!editar"
                  :outlined="editar"
                ></v-text-field>
              </v-col>
              <v-col cols="12" md="3">
                <v-text-field
                  dense
                  v-model="public_security_bonus"
                  v-validate.initial="'min:1|max:20'"
                  :error-messages="errors.collect('4toBono')"
                  data-vv-name="4toBono"
                  label="Bono Seguridad Ciudadana"
                  :readonly="!editar"
                  :outlined="editar"
                ></v-text-field>
              </v-col>
            </v-row>
          </v-container>
        </v-card>
      </v-col>
    </v-row>
  </v-flex>
</template>
<script>
import { Validator } from 'vee-validate'
export default {
  inject: ['$validator'],
  name: "dashboard-index",
  data: () => ({
    payable_liquid: [null,null,null],
    seniority_bonus: null,
    border_bonus: null,
    public_security_bonus: null,
    east_bonus: null,
    plazo_meses : 24,
    monto_solicitado : null,
    loanTypeSelected:null,
    visible: null,
    editar:true,
    refinanciamiento: false,
    muserpol: null,
    boletas1:null,
    num:3
  }),
   beforeMount() {
    this.getBallots(this.$route.query.affiliate_id);
  },
  methods:
 {
    async getBallots(id) {
    try {
      let res = await axios.get(`affiliate/${id}/contribution`, {
        params:{
          city_id: this.$store.getters.cityId,
          sortBy: ['month_year'],
          sortDesc: ['true'],
          per_page: this.num,
          page: 1,
        }
      })
      console.log('respuesta de la ciudad')
      if(this.num>1)
      {
        this.visible=true
      }
      if(res.data.valid)
      {
        this.editar=false
        this.datos=res.data.data
        for (this.i = 0; this.i< this.datos.length; this.i++) {
          this.payable_liquid[this.i]= this.datos[this.i].payable_liquid,
          this.seniority_bonus= this.datos[0].seniority_bonus,
          this.border_bonus= this.datos[0].border_bonus,
          this.public_security_bonus= this.datos[0].public_security_bonus,
          this.east_bonus= this.datos[0].east_bonus
        }
      }
    } catch (e) {
      console.log(e)
    } finally {
      this.loading = false
    }
  },
 }
};
</script>