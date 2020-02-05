<template>
  <v-container fluid>
    <v-card>
      <v-row justify="center">
        <v-col cols="12"  >
          <v-container class="py-0">
            <v-row>
              <v-col cols="12" class="py-0">
                REQUISITOS PARA CORTO PLAZO PASIVO
              </v-col>
            </v-row>
          </v-container>
        </v-col>
      </v-row>
      <v-card-text>
         <v-row justify="center">
        <v-col cols="12"  >
          <v-container class="py-0">
            <v-row>
              <v-col cols="12" md="8" class="py-0">
                1.-Comprobante de depósito bancario de Bs.- 15,00 por concepto de adquisición de folder y formularios, en la cuenta fiscal de la MUSERPOL.
              </v-col>
            </v-row>
          </v-container>
        </v-col>
      </v-row>
        <v-layout row wrap>
              <p>1.-Comprobante de depósito bancario de Bs.- 15,00 por concepto de adquisición de folder y formularios, en la cuenta fiscal de la MUSERPOL.</p>
              <p>2.-Comprobante de depósito bancario de Bs.- 15,00 por concepto de adquisición de folder y formularios, en la cuenta fiscal de la MUSERPOL.</p>
              <p>3.-Comprobante de depósito bancario de Bs.- 15,00 por concepto de adquisición de folder y formularios, en la cuenta fiscal de la MUSERPOL.</p>
              <p>4.-Comprobante de depósito bancario de Bs.- 15,00 por concepto de adquisición de folder y formularios, en la cuenta fiscal de la MUSERPOL.</p>
              <p>5.-Comprobante de depósito bancario de Bs.- 15,00 por concepto de adquisición de folder y formularios, en la cuenta fiscal de la MUSERPOL.</p>
              <p>6.-Comprobante de depósito bancario de Bs.- 15,00 por concepto de adquisición de folder y formularios, en la cuenta fiscal de la MUSERPOL.</p>
              <p>7.-Comprobante de depósito bancario de Bs.- 15,00 por concepto de adquisición de folder y formularios, en la cuenta fiscal de la MUSERPOL.</p>
              <p>8.-Comprobante de depósito bancario de Bs.- 15,00 por concepto de adquisición de folder y formularios, en la cuenta fiscal de la MUSERPOL.</p>
       
        
        </v-layout>

         <v-container fluid>
    <v-data-iterator
      :items="items"
      :items-per-page.sync="itemsPerPage"
      hide-default-footer
    >
      <template v-slot:header>
        <v-toolbar
          class="mb-2"
          color="indigo darken-5"
          dark
          flat
        >
          <v-toolbar-title> REQUISITOS PARA CORTO PLAZO PASIVO</v-toolbar-title>
        </v-toolbar>
      </template>

      <template v-slot:default="props">
        <v-row>
          <v-col
            v-for="item in props.items"
            :key="item.name"
            cols="12"
            sm="6"
            md="4"
            lg="3"
          >
            <v-card>
              <v-card-title class="subheading font-weight-bold">{{item.name   }}</v-card-title>

              <v-divider></v-divider>

              <v-list dense>
                <v-list-item>
                  <v-list-item-content>Calories:</v-list-item-content>
                  <v-list-item-content class="align-end">{{item.name  }}</v-list-item-content>
                </v-list-item>
              </v-list>
            </v-card>
          </v-col>
        </v-row>
      </template>
    </v-data-iterator>
  </v-container>




      </v-card-text>
    </v-card>
  </v-container>
  
</template>

 

<script>
import { Validator } from 'vee-validate'
export default {
inject: ['$validator'],
name: "loan-requirement",
data: () => ({
  boletas: [null,null,null],
  bonos: [null,null,null,null],
  plazo_meses : 24,
  monto_solicitado : null,
  loanTypeSelected:null,
  item:[
    {
      name:'3 ultimas boletas de pago con renta en AFP original',
    },
    {
      name:'3 ultimas boletas de pago con renta en SENASIR original',
    },
    {
      name:'Aceptación de Herencia del (la) hermano(a) ante el fallecimiento del (la) titular en copia legalizada emitida por autoridad competente.',
    },
    {
      name:'Aceptación de Herencia del (la) hermano(a) ante el fallecimiento del (la) titular en original emitida por autoridad competente.',
    },
    {
      name:'Boleta de pago en copia simple.',
    },
    {
      name:'Certificación de baja definitiva en original.',
    },
    {
      name:'Certificación Auxilio Mortuorio',
    },
    {
      name:'Certificación de salud en copia legalizada emitida por la Dirección Nacional de Salud y Bienestar Social de la Policía Boliviana.',
    },
    {
      name:'Certificado de años de servicio desglosado en fotocopia Legalizada emitido por el Comando General de la Policía Boliviana',
    },
  ],
}),
watch: {
  monto_solicitado() {
    this.$validator.validateAll()
  },
},
  computed: {
    suma_bono() {
      if (this.bonos.length > 0) {
          return this.bonos.reduce((a, b) => { return Number(a) + Number(b) })
      } else {
          return 0
      }
    },
    promedio() {
      if (this.boletas.length > 0) {
          return this.boletas.reduce((a, b) => { return Number(a) + Number(b) }) / this.boletas.length
      } else {
          return 0
      }
    },
    calcular_cuota()
    {
    if (this.plazo_meses >0 && this.monto_solicitado>0){
      var resultado = 0;
      return (((0.01666)/(1-(1/Math.pow((1+0.01666),this.plazo_meses))))*this.monto_solicitado)
    } else{
        var cuota_maxima=0
      if (this.boletas.length > 0) {
      let total_bono = 0
      var liquido_calificacion=0;
      var promedio = this.boletas.reduce((a, b) => { return Number(a) + Number(b) }) / this.boletas.length
      if (this.bonos.length > 0) {
        total_bono = this.bonos.reduce((a, b) => { return Number(a) + Number(b) })
      }
      liquido_calificacion = promedio -total_bono
        var monto_maximo = (1-(1/Math.pow((1+0.01666),this.plazo_meses)))*(0.5*liquido_calificacion)/0.01666
        if (monto_maximo > 25000){
          monto_maximo = 25000
        } else {
          monto_maximo = Math.trunc(Math.round(Math.floor(monto_maximo))/1000)*1000
        }
        var cuota_maxima = (((0.01666)/(1-(1/Math.pow((1+0.01666),this.plazo_meses))))*monto_maximo)
        return cuota_maxima
      }else{
        return 0
      }
    }
    },
    monto_maximo() {
      if (this.boletas.length > 0) {
      let total_bono = 0
      var liquido_calificacion=0;
      var promedio = this.boletas.reduce((a, b) => { return Number(a) + Number(b) }) / this.boletas.length
      if (this.bonos.length > 0) {
        total_bono = this.bonos.reduce((a, b) => { return Number(a) + Number(b) })
      }
      liquido_calificacion = promedio -total_bono
        var monto_maximo = (1-(1/Math.pow((1+0.01666),this.plazo_meses)))*(0.5*liquido_calificacion)/0.01666
        if (monto_maximo > 25000){
          monto_maximo = 25000
        } else {
          monto_maximo = Math.trunc(Math.round(Math.floor(monto_maximo))/1000)*1000
        }
      }
        this.monto_solicitado = monto_maximo
        return monto_maximo
    }
  }
};
</script>