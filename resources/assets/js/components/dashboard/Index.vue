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
                    <v-col cols="12" md="12" class="ma-0 pb-0">
                      AFILIADO:
                      {{
                        $options.filters.fullName(affiliate.affiliate, true)
                      }}</v-col
                    >
                    <v-col cols="12" md="6" class="ma-0 pb-0">
                      C.I: {{ affiliate.affiliate.identity_card }}</v-col
                    >
                    <v-col cols="12" md="6" class="ma-0 pb-0">
                      MATRÍCULA:
                    </v-col>
                    <v-col cols="12" md="6" class="ma-0 pb-0">
                      CATEGORÍA: {{ affiliate.affiliate.category_id }}</v-col
                    >
                    <v-col cols="12" md="6" class="ma-0 pb-1">
                      ESTADO: {{ affiliate.affiliate.affiliate_state}}
                    </v-col>
                    <v-col
                      cols="12"
                      md="8"
                      class="font-weight-black caption ma-0 py-0"
                    >
                      NRO DE PRÉSTAMOS SOLICITADOS:
                      {{ loans.sismu_tit.length + loans.sismu_tit.length }}
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
            <h3 class="pa-1 text-center" v-if="(loans.pvt_tit.length + loans.sismu_tit.length) > 0">PRESTAMOS SOLICITADOS</h3>
            <v-row>
              <v-col
                cols="12"
                md="12"
                class="py-0"
                v-if="loans.pvt_tit.length > 0"
              >
                <v-card>
                  <h4 class="pa-1">PVT</h4>
                  <v-data-table
                    dense
                    :headers="headers"
                    :items="loans.pvt_tit"
                    :items-per-page="4"
                    hide-default-footer
                  >
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
                      <v-tooltip top>
                        <template v-slot:activator="{ on }">
                          <v-btn
                            icon
                            small
                            v-on="on"
                            color="error"
                            :to="{ name: 'flowAdd', params: { id: item.id } }"
                            ><v-icon>mdi-message-alert</v-icon>
                          </v-btn>
                        </template>
                        <span>Ver mensaje</span>
                      </v-tooltip>
                    </template>
                  </v-data-table>
                </v-card>
              </v-col>
              <v-col
                cols="12"
                md="12"
                class="py-0"
                v-if="loans.sismu_tit.length > 0"
              >
                <v-card>
                  <h4 class="pa-1">SISMU</h4>
                  <v-data-table
                    dense
                    :headers="headers"
                    :items="loans.sismu_tit"
                    :items-per-page="4"
                    hide-default-footer
                  >
                    <template v-slot:item.actions="{ item }">
                      <v-tooltip top>
                        <template v-slot:activator="{ on }">
                          <v-btn
                            icon
                            small
                            v-on="on"
                            color="error"
                            :to="{ name: 'flowAdd', params: { id: item.id } }"
                            ><v-icon>mdi-message-alert</v-icon>
                          </v-btn>
                        </template>
                        <span>Ver mensaje</span>
                      </v-tooltip>
                    </template>
                  </v-data-table>
                </v-card>
              </v-col>
            </v-row>
            <h3 class="pa-1 text-center" v-if="(loans.pvt_gar.length + loans.sismu_gar.length) > 0">PRESTAMOS GARANTIZADOS</h3>
            <v-row>
              <v-col
                cols="12"
                md="12"
                class="py-0"
                v-if="loans.pvt_gar.length > 0"
              >
                <v-card>
                  <h4 class="pa-1">PVT</h4>
                  <v-data-table
                    dense
                    :headers="headers"
                    :items="loans.pvt_gar"
                    :items-per-page="4"
                    hide-default-footer
                  >
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
                      <v-tooltip top>
                        <template v-slot:activator="{ on }">
                          <v-btn
                            icon
                            small
                            v-on="on"
                            color="error"
                            :to="{ name: 'flowAdd', params: { id: item.id } }"
                            ><v-icon>mdi-message-alert</v-icon>
                          </v-btn>
                        </template>
                        <span>Ver mensaje</span>
                      </v-tooltip>
                    </template>
                  </v-data-table>
                </v-card>
              </v-col>
              <v-col
                cols="12"
                md="12"
                class="py-0"
                v-if="loans.sismu_gar.length > 0"
              >
                <v-card>
                  <h4 class="pa-1">SISMU</h4>
                  <v-data-table
                    dense
                    :headers="headers"
                    :items="loans.sismu_gar"
                    :items-per-page="4"
                    hide-default-footer
                  >
                    <template v-slot:item.actions="{ item }">
                      <v-tooltip top>
                        <template v-slot:activator="{ on }">
                          <v-btn
                            icon
                            small
                            v-on="on"
                            color="error"
                            :to="{ name: 'flowAdd', params: { id: item.id } }"
                            ><v-icon>mdi-message-alert</v-icon>
                          </v-btn>
                        </template>
                        <span>Ver mensaje</span>
                      </v-tooltip>
                    </template>
                  </v-data-table>
                </v-card>
              </v-col>
            </v-row>
          </template>
        </v-card>
      </v-container>
    </v-card-text>

    <div>{{ this.loans }}</div>
  </v-card>
</template>

<script>
export default {
  name: "dashboard-index",
  data: () => ({
    headers1: [
      {
        text: "Codigo",
        class: ["normal", "white--text"],
        align: "left",
        value: "code",
      },
      {
        text: "Modalidad",
        class: ["normal", "white--text"],
        align: "left",
        value: "amount_approved",
      },
      {
        text: "Monto solicitado",
        class: ["normal", "white--text"],
        align: "left",
        value: "amount_approved",
      },
      {
        text: "Plazo",
        class: ["normal", "white--text"],
        align: "left",
        value: "loan_term",
      },
      {
        text: "Cuota",
        class: ["normal", "white--text"],
        align: "left",
        value: "estimated_quota",
      },
      {
        text: "Saldo",
        class: ["normal", "white--text"],
        align: "left",
        value: "estimated_quota",
      },
      {
        text: "Estado",
        class: ["normal", "white--text"],
        align: "left",
        value: "estimated_quota",
      },
      {
        text: "Ver",
        class: ["normal", "white--text"],
        align: "left",
        value: "estimated_quota",
      },
    ],
    headers: [
      {
        text: "PresNumero",
        class: ["normal", "white--text"],
        align: "left",
        value: "code",
      },
      {
        text: "Modalidad",
        class: ["normal", "white--text"],
        align: "left",
        value: "PrdDsc",
      },
      {
        text: "Monto solicitado",
        class: ["normal", "white--text"],
        align: "left",
        value: "PresMntDesembolso",
      },
      {
        text: "Plazo",
        class: ["normal", "white--text"],
        align: "left",
      },
      {
        text: "Cuota",
        class: ["normal", "white--text"],
        align: "left",
        value: "PresCuotaMensual",
      },
      {
        text: "Saldo",
        class: ["normal", "white--text"],
        align: "left",
        value: "PresSaldoAct",
      },
      {
        text: "Estado",
        class: ["normal", "white--text"],
        align: "left",
        value: "PresEstDsc",
      },
      {
        text: "Acciones",
        value: "actions",
        class: ["normal", "white--text"],
        align: "center",
        sortable: false,
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
    async getLoansHistory() {
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
    },
  },
};
</script>