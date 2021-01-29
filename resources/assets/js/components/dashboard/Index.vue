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
                        dense
                        label="CI ó Matrícula"
                        v-model="affiliate_ci"
                        class="py-0"
                        single-line
                        hide-details
                        clearable
                        :loading="loading"
                        @keyup.enter="getLoansHistory()"
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
                    <h3 class="error--text aling-text--center">
                      <ul style="list-style: none" class="pa-0">
                        <li
                          v-for="(message, index) in loans.message"
                          :key="index"
                          class="pb-2"
                        >
                          {{ message }}
                        </li>
                      </ul>
                    </h3>
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
                      MATRÍCULA: {{ loans.registration }}
                    </v-col>
                    <template v-if="loans.tit_pvt">
                      <v-col cols="12" md="6" class="ma-0 pb-0">
                        CATEGORÍA: {{ category_name }}
                      </v-col>
                      <v-col cols="12" md="6" class="ma-0 pb-0">
                        ESTADO: {{ state_name_status }}</v-col
                      >
                      <v-col cols="12" md="6" class="ma-0 pb-0">
                        GRADO: {{ degree_name }}
                      </v-col>
                      <v-col cols="12" md="6" class="ma-0 pb-2">
                        UNIDAD: {{ unit_name }}
                      </v-col>
                    </template>
                    <template v-if="loans.spouse_pvt || loans.spouse_sismu">
                      <v-col cols="12" md="6" class="ma-0 pb-2">
                        CONYUGUE
                      </v-col>
                    </template>

                    <v-col
                      cols="12"
                      md="8"
                      class="font-weight-black caption ma-0 py-0"
                    >
                      NRO DE PRÉSTAMOS SOLICITADOS:
                      {{ loans.loans.length }}
                    </v-col>
                    <v-col
                      cols="12"
                      md="8"
                      class="font-weight-black caption ma-0 pt-0 pb-1"
                    >
                      NRO DE PRÉSTAMOS GARANTIZADOS:
                      {{ loans.guarantees.length }}
                    </v-col>
                    <v-col
                      cols="12"
                      md="8"
                      class="font-weight-black caption ma-0 pt-0 pb-1 red--text"
                      v-if="loans.observables.length > 0"
                    >
                      <ul style="list-style: none" class="pa-0">
                        <li
                          v-for="(message, index) in loans.message"
                          :key="index"
                          class="pb-2"
                        >
                          {{ message }}
                        </li>
                      </ul>
                    </v-col>
                  </template>
                </v-row>
              </v-card>
            </v-col>
          </v-row>
          <template>
            <template v-if="ver && exist_affiliate && loans.loans.length > 0">
              <h3 class="pa-1 text-center">PRESTAMOS SOLICITADOS</h3>
              <v-row>
                <v-col cols="12" md="12" class="py-0">
                  <v-card>
                    <v-data-table
                      class="text-uppercase"
                      dense
                      :headers="headers_loans"
                      :items="loans.loans"
                      :items-per-page="4"
                      hide-default-footer
                    >
                      <template v-slot:[`item.shortened`]="{ item }">
                        <v-tooltip top>
                          <template v-slot:activator="{ on }">
                            <span v-on="on">{{ item.shortened }}</span>
                          </template>
                          <span> {{ item.modality }}</span>
                        </v-tooltip>
                      </template>

                      <template v-slot:[`item.request_date`]="{ item }">
                        {{ item.request_date | date }}
                      </template>
                      <template v-slot:[`item.disbursement_date`]="{ item }">
                        {{ item.disbursement_date | date }}
                      </template>
                      <template v-slot:[`item.amount`]="{ item }">
                        {{ item.amount | moneyString }}
                      </template>
                      <template v-slot:[`item.estimated_quota`]="{ item }">
                        {{ item.estimated_quota | moneyString }}
                      </template>
                      <template v-slot:[`item.balance`]="{ item }">
                        {{ item.balance | moneyString }}
                      </template>

                      <template v-slot:[`item.actions`]="{ item }">
                        <v-tooltip top>
                          <template v-slot:activator="{ on }">
                            <v-btn
                              v-if="item.origin == 'PVT'"
                              icon
                              small
                              v-on="on"
                              color="warning"
                              :to="{
                                name: 'flowAdd',
                                params: { id: item.id },
                              }"
                              target="_blank"
                              ><v-icon>mdi-eye</v-icon>
                            </v-btn>
                            <v-btn
                              v-else
                              icon
                              small
                              v-on="on"
                              color="warning"
                              @click.stop="routeSismu(item.id)"
                              ><v-icon>mdi-eye</v-icon>
                            </v-btn>
                          </template>
                          <span>Ver información</span>
                        </v-tooltip>
                      </template>
                    </v-data-table>
                  </v-card>
                </v-col>
              </v-row>
            </template>
            <template
              v-if="ver && exist_affiliate && loans.guarantees.length > 0"
            >
              <h3 class="pa-1 text-center">PRESTAMOS GARANTIZADOS</h3>
              <v-row>
                <v-col cols="12" md="12" class="py-0">
                  <v-card>
                    <v-data-table
                      class="text-uppercase"
                      dense
                      :headers="headers_loans"
                      :items="loans.guarantees"
                      :items-per-page="4"
                      hide-default-footer
                    >
                      <template v-slot:[`item.shortened`]="{ item }">
                        <v-tooltip top>
                          <template v-slot:activator="{ on }">
                            <span v-on="on">{{ item.shortened }}</span>
                          </template>
                          <span> {{ item.modality }}</span>
                        </v-tooltip>
                      </template>

                      <template v-slot:[`item.request_date`]="{ item }">
                        {{ item.request_date | date }}
                      </template>
                      <template v-slot:[`item.disbursement_date`]="{ item }">
                        {{ item.disbursement_date | date }}
                      </template>
                      <template v-slot:[`item.amount`]="{ item }">
                        {{ item.amount | moneyString }}
                      </template>
                      <template v-slot:[`item.estimated_quota`]="{ item }">
                        {{ item.estimated_quota | moneyString }}
                      </template>
                      <template v-slot:[`item.balance`]="{ item }">
                        {{ item.balance | moneyString }}
                      </template>

                      <template v-slot:[`item.actions`]="{ item }">
                        <v-tooltip top>
                          <template v-slot:activator="{ on }">
                            <v-btn
                              v-if="item.origin == 'PVT'"
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
                            <v-btn
                              v-else
                              icon
                              small
                              v-on="on"
                              color="warning"
                              @click.stop="routeSismu(item.id)"
                              ><v-icon>mdi-eye</v-icon>
                            </v-btn>
                          </template>
                          <span>Ver información</span>
                        </v-tooltip>
                      </template>
                    </v-data-table>
                  </v-card>
                </v-col>
              </v-row>
            </template>

            <template v-if="ver && loans.observables.length > 0">
              <h3 class="pa-1 text-center">PRÉSTAMOS COINCIDENTES</h3>
              <v-row>
                <v-col cols="12" md="12" class="py-0">
                  <v-card>
                    <v-data-table
                      class="text-uppercase"
                      dense
                      :headers="headers_observables"
                      :items="loans.observables"
                      :items-per-page="10"
                    >
                      <template v-slot:[`item.PrdDsc`]="{ item }">
                        <v-tooltip top>
                          <template v-slot:activator="{ on }">
                            <span v-on="on">{{ item.PrdDsc }}</span>
                          </template>
                          <span> {{ item.PrdDsc }}</span>
                        </v-tooltip>
                      </template>

                      <template v-slot:[`item.request_date`]="{ item }">
                        {{ item.request_date | date }}
                      </template>
                      <template v-slot:[`item.disbursement_date`]="{ item }">
                        {{ item.disbursement_date | date }}
                      </template>
                      <template v-slot:[`item.PresMntDesembolso`]="{ item }">
                        {{ item.PresMntDesembolso | moneyString }}
                      </template>
                      <template v-slot:[`item.PresCuotaMensual`]="{ item }">
                        {{ item.PresCuotaMensual | moneyString }}
                      </template>
                      <template v-slot:[`item.PresSaldoAct`]="{ item }">
                        {{ item.PresSaldoAct | moneyString }}
                      </template>

                      <template v-slot:[`item.actions`]="{ item }">
                        <v-tooltip top>
                          <template v-slot:activator="{ on }">
                            <v-btn
                              v-if="item.origin == 'PVT'"
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
                            <v-btn
                              v-else
                              icon
                              small
                              v-on="on"
                              color="warning"
                              @click.stop="routeSismu(item.IdPrestamo)"
                              ><v-icon>mdi-eye</v-icon>
                            </v-btn>
                          </template>
                          <span>Ver información</span>
                        </v-tooltip>
                      </template>
                    </v-data-table>
                  </v-card>
                </v-col>
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
    headers_loans: [
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
        value: "shortened",
        width: "5%",
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
        text: "Tipo Trámite",
        class: ["normal", "white--text"],
        align: "left",
        value: "origin",
        width: "10%",
      },
      {
        text: "Estado",
        class: ["normal", "white--text"],
        align: "left",
        value: "state",
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
    headers_observables: [
      {
        text: "CI",
        class: ["normal", "white--text"],
        align: "left",
        value: "PadCedulaIdentidad",
        width: "15%",
      },
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
    loans: {},
    affiliate_ci: null,
    affiliate: {},
    exist_affiliate: false,
    ver: false,
    loading: false,
    degree_name: null,
    category_name: null,
    unit_name: null,
    state_name_status: null,
  }),
  watch: {
    affiliate_ci() {
      this.ver = false;
    },
  },
  methods: {
    async getLoansHistory() {
      try {
        this.loading = true;
        let message = [];
        let res = await axios.get(`affiliate_record`, {
          params: {
            ci: this.affiliate_ci,
          },
        });
        this.loans = res.data;
        message = this.loans.message[0];
        if (message != "afiliado-inexistente") {
          this.exist_affiliate = true;
          this.ver = true;
          if (this.loans.tit_pvt) {
            this.getAffiliate(this.loans.id);
          }
        } else {
          this.exist_affiliate = false;
          this.ver = true;
          console.log("no coincide");
        }
      } catch (e) {
        console.log(e);
      } finally {
        this.loading = false;
      }
    },
    routeSismu(id) {
      window.open(
        "http://sismu.muserpol.gob.bo/musepol/akardex.aspx?" + id,
        "_blank"
      );
    },
    async getAffiliate(id) {
      try {
        this.loading = true;
        let res = await axios.get(`affiliate/${id}`);
        this.affiliate = res.data;
        this.getDegree_name(this.affiliate.degree_id);
        this.getCategory_name(this.affiliate.category_id);
        this.getUnit_name(this.affiliate.unit_id);
        this.getState_name(id);
      } catch (e) {
        console.log(e);
      } finally {
        this.loading = false;
      }
    },
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
    async getCategory_name(id) {
      try {
        this.loading = true;
        let res = await axios.get(`category/${id}`);
        this.category_name = res.data.name;
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
    async getState_name(id) {
      try {
        this.loading = true;
        let res = await axios.get(`affiliate/${id}/state`);
        this.state_name = res.data;
        this.state_name_type = this.state_name.affiliate_state_type.name;
        this.state_name_status = this.state_name.name;
      } catch (e) {
        console.log(e);
      } finally {
        this.loading = false;
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