index final
<template>
  <v-card flat>
    <v-card-title>
      <v-toolbar dense color="tertiary">
        <v-toolbar-title>Información de Préstamos</v-toolbar-title>
      </v-toolbar>
    </v-card-title>
    <v-card-text>
      <v-container class="py-0">
        <v-card color="grey lighten-1" class="ma-0 pa-3">
          <v-row>
            <v-col cols="12" md="4">
              <v-card>
                <v-container class="py-0">
                  <v-row>
                    <v-col cols="12" md="4"></v-col>
                    <v-col cols="12" md="6"> Afiliado </v-col>
                    <v-col cols="12" md="2"></v-col>
                    <v-col cols="12" md="1"></v-col>
                    <v-col cols="12" md="8">
                      <v-text-field
                        label="C.I."
                        v-model="affiliate_ci"
                        class="py-0"
                        single-line
                        hide-details
                        clearable
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12" md="2">
                      <v-tooltip top>
                        <template v-slot:activator="{ on }">
                          <v-btn
                            fab
                            dark
                            x-small
                            v-on="on"
                            color="info"
                            @click.stop="getLoansHistory()"
                          >
                            <v-icon>mdi-magnify</v-icon>
                          </v-btn>
                        </template>
                        <span>Buscar afiliado</span>
                      </v-tooltip>
                    </v-col>
                  </v-row>
                </v-container>
              </v-card>
            </v-col>

            <v-col cols="12" md="8">
              <v-card>
                <v-row class="ma-0 pb-0 text-uppercase">
                  <v-col
                    class="text-center"
                    cols="12"
                    md="8"
                    v-show="!exist_affiliate"
                    v-if="ver"
                  >
                    <h2 class="error--text">NO EXISTE COINDICENCIAS</h2>
                  </v-col>
                  <template v-if="ver && exist_affiliate">
                    <v-col cols="12" md="12" class="ma-0 pb-0 text-center">
                      <h2>
                        {{ $options.filters.fullName(loans, true) }}
                      </h2></v-col
                    >
                    <v-col cols="12" md="6" class="ma-0 pb-0">
                      C.I: {{ loans.identity_card }}</v-col
                    >
                    <v-col cols="12" md="6" class="ma-0 pb-0">
                      MATRÍCULA:{{ loans.registration }}
                    </v-col>
                    <v-col cols="12" md="6" class="ma-0 pb-0">
                      CATEGORÍA:
                    </v-col>
                    <v-col cols="12" md="6" class="ma-0 pb-1"> ESTADO: </v-col>
                    <v-col
                      cols="12"
                      md="8"
                      class="font-weight-black caption ma-0 py-0"
                    >
                      NRO DE PRÉSTAMOS SOLICITADOS:
                      {{ loans.pvt_tit.length + loans.sismu_tit.length }}
                    </v-col>
                    <v-col
                      cols="12"
                      md="8"
                      class="font-weight-black caption ma-0 pt-0 pb-1"
                    >
                      NRO DE PRÉSTAMOS GARANTIZADOS:
                      {{ loans.pvt_gar.length + loans.sismu_gar.length }}
                    </v-col>
                  </template>
                </v-row>
              </v-card>
            </v-col>
          </v-row>
          <template v-if="ver && exist_affiliate">
            <template v-if="loans.pvt_tit.length + loans.sismu_tit.length > 0">
            <h3
              class="pa-1 text-center"
              
            >
              PRESTAMOS SOLICITADOS
            </h3>
            <v-row>
              <v-col cols="12" md="12" class="py-0">
                <v-card>
                  <!-- <h4 class="pa-1">PVT</h4>-->
                  <v-data-table
                    class="text-uppercase"
                    dense
                    :headers="headers2"
                    :items="lender_loans"
                    :items-per-page="4"
                    hide-default-footer
                  >
                    <template v-slot:item.request_date="{ item }">
                      {{ item.request_date | date }}
                    </template>
                    <template v-slot:item.disbursement_date="{ item }">
                      {{ item.disbursement_date | date }}
                    </template>
                    <template v-slot:item.amount="{ item }">
                      {{ item.amount | moneyString }}
                    </template>
                    <template v-slot:item.estimated_quota="{ item }">
                      {{ item.estimated_quota | moneyString }}
                    </template>
                    <template v-slot:item.balance="{ item }">
                      {{ item.balance | moneyString }}
                    </template>

                    <template v-slot:item.actions="{ item }">
                      <v-tooltip top>
                        <template v-slot:activator="{ on }">
                          <v-btn
                            icon
                            small
                            v-on="on"
                            color="warning"
                            :to="{
                              name: 'flowAdd',
                              params: { id: item.id },
                            }"
                            ><v-icon>mdi-eye</v-icon>
                          </v-btn>
                        </template>
                        <span>Ver trámite</span>
                      </v-tooltip>
                    </template>
                  </v-data-table>
                </v-card>
              </v-col>
              <!-- <v-col
                cols="12"
                md="12"
                class="py-0"
                v-if="loans.sismu_tit.length > 0"
              >
                <v-card>
                  <h4 class="pa-1">SISMU</h4>
                  <v-data-table
                    class="text-uppercase"
                    dense
                    :headers="headers2"
                    :items="loans.sismu_tit"
                    :items-per-page="4"
                    hide-default-footer
                    :item-class="itemRowBackground"
                  >
                  </v-data-table>
                </v-card>
              </v-col>-->
            </v-row>
            </template>
            <template v-if="loans.pvt_gar.length + loans.sismu_gar.length > 0">
            <h3
              class="pa-1 text-center"
              
            >
              PRESTAMOS GARANTIZADOS
            </h3>
            <v-row>
              <v-col cols="12" md="12" class="py-0">
                <v-card>
                  <!--<h4 class="pa-1">PVT</h4>-->
                  <v-data-table
                    class="text-uppercase"
                    dense
                    :headers="headers2"
                    :items="guaranteed_loans"
                    :items-per-page="4"
                    hide-default-footer
                  >
                    <template v-slot:item.request_date="{ item }">
                      {{ item.request_date | date }}
                    </template>
                    <template v-slot:item.disbursement_date="{ item }">
                      {{ item.disbursement_date | date }}
                    </template>
                    <template v-slot:item.amount="{ item }">
                      {{ item.amount | moneyString }}
                    </template>
                    <template v-slot:item.estimated_quota="{ item }">
                      {{ item.estimated_quota | moneyString }}
                    </template>
                    <template v-slot:item.balance="{ item }">
                      {{ item.balance | moneyString }}
                    </template>
                    <template v-slot:item.actions="{ item }">
                      <v-tooltip top>
                        <template v-slot:activator="{ on }">
                          <v-btn
                            icon
                            small
                            v-on="on"
                            color="warning"
                            :to="{ name: 'flowAdd', params: { id: item.id } }"
                            ><v-icon>mdi-eye</v-icon>
                          </v-btn>
                        </template>
                        <span>Ver trámite</span>
                      </v-tooltip>
                    </template>
                  </v-data-table>
                </v-card>
              </v-col>
              <!--<v-col
                cols="12"
                md="12"
                class="py-0"
                v-if="loans.sismu_gar.length > 0"
              >
                <v-card>
                  <h4 class="pa-1">SISMU</h4>
                  <v-data-table
                    class="text-uppercase"
                    dense
                    :headers="headers2"
                    :items="loans.sismu_gar"
                    :items-per-page="4"
                    hide-default-footer
                    :item-class="itemRowBackground"
                  >
                  </v-data-table>
                </v-card>
              </v-col>-->
            </v-row>
            </template>
          </template>
        </v-card>
      </v-container>
    </v-card-text>
  </v-card>
</template>

<script>
export default {
  name: "dashboard-index",
  data: () => ({
    headers2: [
      {
        text: "Código",
        class: ["normal", "white--text"],
        align: "left",
        value: "code",
        width: "15%",
      },
      {
        text: "Modalidad",
        class: ["normal", "white--text"],
        align: "left",
        value: "modality",
        width: "20%",
      },
      {
        text: "Fecha Solicitud",
        class: ["normal", "white--text"],
        align: "left",
        value: "request_date",
        width: "10%",
      },
      {
        text: "Fecha Desembolso",
        class: ["normal", "white--text"],
        align: "left",
        value: "disbursement_date",
        width: "10%",
      },
      {
        text: "Monto solicitado",
        class: ["normal", "white--text"],
        align: "left",
        value: "amount",
        width: "10%",
      },

      {
        text: "Plazo",
        class: ["normal", "white--text"],
        align: "left",
        value: "loan_term",
        width: "10%",
      },
      {
        text: "Cuota",
        class: ["normal", "white--text"],
        align: "left",
        value: "estimated_quota",
        width: "10%",
      },
      {
        text: "Saldo",
        class: ["normal", "white--text"],
        align: "left",
        value: "balance",
        width: "10%",
      },
      {
        text: "Estado",
        class: ["normal", "white--text"],
        align: "left",
        value: "state1",
        width: "15%",
      },
      {
        text: "Acciones",
        value: "actions",
        class: ["normal", "white--text"],
        align: "center",
        sortable: false,
        width: "5%",
      },
    ],
    headers: [
      {
        text: "Código",
        class: ["normal", "white--text"],
        align: "left",
        value: "PresNumero",
        width: "15%",
      },
      {
        text: "Modalidad",
        class: ["normal", "white--text"],
        align: "left",
        value: "PrdDsc",
        width: "20%",
      },
      {
        text: "Monto solicitado",
        class: ["normal", "white--text"],
        align: "left",
        value: "PresMntDesembolso",
        width: "10%",
      },
      {
        text: "Plazo",
        class: ["normal", "white--text"],
        align: "left",
        value: "PresMeses",
        width: "10%",
      },
      {
        text: "Cuota",
        class: ["normal", "white--text"],
        align: "left",
        value: "PresCuotaMensual",
        width: "10%",
      },
      {
        text: "Saldo",
        class: ["normal", "white--text"],
        align: "left",
        value: "PresSaldoAct",
        width: "10%",
      },
      {
        text: "Estado",
        class: ["normal", "white--text"],
        align: "left",
        value: "PresEstDsc",
        width: "15%",
      },
      {
        text: "Acciones",
        value: "actions",
        class: ["normal", "white--text"],
        align: "center",
        sortable: false,
        width: "10%",
      },
    ],
    loan: [
      {
        code: "Frozen Yogurt",
        amount_approved: 159,
        loan_term: 2,
        estimated_quota: 24,
      },
    ],
    loans: [],
    affiliate_ci: null,
    affiliate: {},
    exist_affiliate: false,
    ver: false,
  }),
  methods: {
    /*async getLoansHistory2() {
      try {
        //let loansHistory = await axios.get(`affiliate_record/${this.affiliate_ci}`)

        let resp = await axios.get(`affiliate_existence`, {
          params: {
            identity_card: this.affiliate_ci,
          },
        });
        this.affiliate = resp.data;
        //console.log(this.affiliate.affiliate.affiliate_state);
        this.exist_affiliate = this.affiliate.state;
        this.ver = true;
        if (this.exist_affiliate) {
          let resp2 = await axios.get(`affiliate_record/${this.affiliate_ci}`);
          this.loans = resp2.data;
        } else {
          this.exist_affiliate = false;
          this.ver = true;
          console.log("no coincide");
        }
      } catch (e) {
        console.log(e);
      }
    },*/
    async getLoansHistory() {
      try {
        this.guaranteed_loans = [];
        this.lender_loans = [];
        let message = "";
        let res = await axios.get(`affiliate_record/${this.affiliate_ci}`);
        this.loans = res.data;
        this.guaranteed_loans = this.loans.pvt_gar.concat(this.loans.sismu_gar);
        this.lender_loans = this.loans.pvt_tit.concat(this.loans.sismu_tit);
        message = this.loans.message;

        if (message != "inexistente") {
          this.exist_affiliate = true;
          this.ver = true;
        } else {
          this.exist_affiliate = false;
          this.ver = true;
          console.log("no coincide");
        }
      } catch (e) {
        console.log(e);
      }
    },
    itemRowBackground: function (item) {
      return item.state === false ? "style-1" : "style-2";
    },
  },
};
</script>
<style>
.style-1 {
  background-color: rgb(82, 87, 43);
}
</style>