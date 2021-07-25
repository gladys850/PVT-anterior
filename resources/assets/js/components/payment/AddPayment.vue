<template>
  <v-container >
      <v-card>
        <v-card-text class="ma-0 pb-0">
          <v-container fluid class="ma-0 pb-0">
            <v-row justify="center" class="ma-0 pb-0">
              <v-col cols="12" >
                <v-layout row wrap>
                  <v-flex xs12 class="px-2">
                    <fieldset class="pa-3">
                      <center>
                        <v-toolbar-title>DATOS DE LA AMORTIZACION</v-toolbar-title>
                      </center>
                      <v-progress-linear></v-progress-linear>
                      <v-container >
                        <template>
                          <v-row>
                            <v-col cols="6" class="ma-0 py-2">
                              <label><b>Pago a Capital:</b></label>
                              {{payment.capital_payment | moneyString}}
                            </v-col>
                              <v-col cols="6" class="ma-0 py-2">
                              <label><b>Fecha de Calculo:</b></label>
                              {{payment.estimated_date | date}}
                            </v-col>
                                <v-col cols="6" class="ma-0 py-2">
                              <label><b>Interes Corriente de Días:</b></label>
                              {{payment.estimated_days.current+' Total'}}
                              {{payment.estimated_days.current_generated | moneyString}}
                            </v-col>
                              <v-col cols="6" class="ma-0 py-2">
                              <label><b>Fecha de Transaccion:</b></label>
                                {{ payment.now_date | date}}
                            </v-col>
                            <v-col cols="6" class="ma-0 py-2">
                              <label><b>Interes Penal de Días:</b></label>
                              {{payment.estimated_days.penal +' Total'}}
                              {{payment.estimated_days.penal_generated | moneyString}}
                            </v-col>
                              <v-col cols="6" class="ma-0 py-2">
                              <label><b>Saldo Anterior:</b></label>
                              {{payment.balance | moneyString }}
                            </v-col>
                            <v-col cols="6" class="ma-0 py-2">
                              <label><b>Interes Corriente Pendiente:</b></label>
                              {{payment.interest_remaining | moneyString}}
                            </v-col>
                            <v-col cols="6" class="ma-0 py-2">
                              <label><b style="color:teal">Saldo Actual:</b></label>
                              <b style="color:teal">{{payment.next_balance | moneyString }}</b>
                            </v-col>
                            <v-col cols="12" class="ma-0 py-2">
                              <label><b>Interes Penal Pendiente:</b></label>
                              {{payment.penal_remaining | moneyString}}
                            </v-col>
                              <v-col cols="12" class="ma-0 py-2">
                              <label><b>Total Pagado:</b></label>
                              {{payment.estimated_quota | moneyString}}
                            </v-col>
                              <v-progress-linear></v-progress-linear>
                            <v-col cols="6" class="ma-0 py-2">
                              <label><b>Intereses Corrientes Pendientes:</b></label>
                              {{payment.interest_accumulated | moneyString}}
                            </v-col>
                            <v-col cols="6" class="ma-0 py-2">
                              <label><b>Interes Penales Pendientes:</b></label>
                              {{payment.penal_accumulated | moneyString}}
                            </v-col>
                          </v-row>
                        </template>
                      </v-container>
                    </fieldset>
                  </v-flex>
                </v-layout>
            </v-col>
          </v-row>
        </v-container>
      </v-card-text>
    </v-card>
  </v-container>
</template>
<script>
export default {
  name: "add-payment",
  props: {
    payment: {
      type: Object,
      required: true
    }
  },
}
</script>