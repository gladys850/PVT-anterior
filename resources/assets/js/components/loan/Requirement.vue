<template>
  <v-container fluid>
    <v-card>
      <v-card-text>
        <v-layout row wrap>
          <v-flex xs9 class="px-2">
            <fieldset class="pa-3">
              <legend class=" mx-2 px-1">REQUISITOS</legend>
              <p>1.-Comprobante de depósito bancario de Bs.- 15,00 por concepto de adquisición de folder y formularios, en la cuenta fiscal de la MUSERPOL.</p>
              <p>2.-Comprobante de depósito bancario de Bs.- 15,00 por concepto de adquisición de folder y formularios, en la cuenta fiscal de la MUSERPOL.</p>
              <p>3.-Comprobante de depósito bancario de Bs.- 15,00 por concepto de adquisición de folder y formularios, en la cuenta fiscal de la MUSERPOL.</p>
              <p>4.-Comprobante de depósito bancario de Bs.- 15,00 por concepto de adquisición de folder y formularios, en la cuenta fiscal de la MUSERPOL.</p>
              <p>5.-Comprobante de depósito bancario de Bs.- 15,00 por concepto de adquisición de folder y formularios, en la cuenta fiscal de la MUSERPOL.</p>
              <p>6.-Comprobante de depósito bancario de Bs.- 15,00 por concepto de adquisición de folder y formularios, en la cuenta fiscal de la MUSERPOL.</p>
              <p>7.-Comprobante de depósito bancario de Bs.- 15,00 por concepto de adquisición de folder y formularios, en la cuenta fiscal de la MUSERPOL.</p>
              <p>8.-Comprobante de depósito bancario de Bs.- 15,00 por concepto de adquisición de folder y formularios, en la cuenta fiscal de la MUSERPOL.</p>
            </fieldset>
          </v-flex>
          <v-flex xs3 class="px-2">
            <fieldset class="pa-3">
              <legend class=" mx-2 px-1">Datos del Préstamo</legend>
                <v-text-field
                label="Plazo en Meses"
                v-model="plazo_meses"
                v-validate.initial="`required|numeric|min_value:1|max_value:36`"
                :error-messages="errors.collect('plazo')"
                data-vv-name="plazo"
                ></v-text-field>
                <v-text-field
                label="Monto Maximo Sugerido"
                v-model ="monto_solicitado"
                v-validate.initial="`required|numeric|min_value:2001|max_value:25000`"
                :error-messages="errors.collect('monto_solicitado')"
                data-vv-name="monto_solicitado"
                ></v-text-field>
            </fieldset>
            <fieldset class="pa-3">
              <legend class=" mx-2 px-1">Cálculo</legend>
              <p>PROMEDIO LIQUIDO PAGABLE:  {{ promedio }}</p>
              <p>TOTAL BONOS: {{ suma_bono }}</p>
              <p>LIQUIDO PARA CALIFICACION: {{ promedio-suma_bono }}</p>
              <p>CALCULO DE CUOTA: {{ calcular_cuota }}</p>
              <p>INDICE DE ENDEUDAMIENTO: {{ promedio>0 ? (calcular_cuota/(promedio-suma_bono)*100 ): 0 }}</p>
              <p class="red--text">{{ promedio>0 ? (calcular_cuota/(promedio-suma_bono)*100)>50 ? "(LA CUOTA NO PUEDE SER MAYOR AL 50 % )" :"" : "" }}</p>
              <p>MONTO MAXIMO SUGERIDO : {{monto_maximo}}</p>
            </fieldset>
          </v-flex>
        </v-layout>
      </v-card-text>
    </v-card>
  </v-container>
</template>
<script>
export default {
name: "loan-requirement",
data: () => ({
  boletas: [null,null,null],
  bonos: [null,null,null,null],
  plazo_meses : 24,
  monto_solicitado : null,
  loanTypeSelected:null,
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