<template>
  <v-card flat>
    <v-card-text>
      <v-container class="py-0">
        <v-card color="grey lighten-1" class="ma-0 pa-3">

          <!--TITULAR--->
          <template v-if="Object.entries(loans_lender).length !== 0">
            <template v-if="ver && loans_lender.loans.length > 0">
              <h3 class="pa-1 text-center">PRÉSTAMOS SOLICITADOS POR EL TITULAR</h3>
              <v-row>
                <v-col cols="12" md="12" class="py-0">
                  <v-card>
                    <v-data-table
                      class="text-uppercase"
                      dense
                      :headers="_headers_loans"
                      :items="loans_lender.loans"
                      :loading="loading"
                      :items-per-page="10"
                      :footer-props="{ itemsPerPageOptions: [10, 15, 30] }"
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
                                name: 'tracingAdd',
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
            <template v-if="ver && loans_lender.guarantees.length > 0">
              <h3 class="pa-1 text-center">PRESTAMOS GARANTIZADOS POR EL TITULAR</h3>
              <v-row>
                <v-col cols="12" md="12" class="py-0">
                  <v-card>
                    <v-data-table
                      class="text-uppercase"
                      dense
                      :headers="headers_loans"
                      :items="loans_lender.guarantees"
                      :loading="loading"
                      :items-per-page="10"
                      :footer-props="{ itemsPerPageOptions: [10, 15, 30] }"
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
                                name: 'tracingAdd',
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
          </template>
            <!--CONYUGUE-->
            <template v-if="Object.entries(loans_spouse).length !== 0">
            <template v-if="loans_spouse.loans.length > 0 && ver">
              <h3 class="pa-1 text-center ">PRÉSTAMOS SOLICITADOS POR CONYUGUE</h3>
              <v-row>
                <v-col cols="12" md="12" class="py-0">
                  <v-card>
                    <v-data-table
                      class="text-uppercase"
                      dense
                      :headers="_headers_loans"
                      :items="loans_spouse.loans"
                      :loading="loading"
                      :items-per-page="10"
                      :footer-props="{ itemsPerPageOptions: [10, 15, 30] }"
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
                                name: 'tracingAdd',
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

            <template v-if="loans_spouse.guarantees.length > 0 && ver ">
              <h3 class="pa-1 text-center">PRESTAMOS GARANTIZADOS POR CONYUGUE</h3>
              <v-row>
                <v-col cols="12" md="12" class="py-0">
                  <v-card>
                    <v-data-table
                      class="text-uppercase"
                      dense
                      :headers="headers_loans"
                      :items="loans_spouse.guarantees"
                      :loading="loading"
                      :items-per-page="10"
                      :footer-props="{ itemsPerPageOptions: [10, 15, 30] }"
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
                                name: 'tracingAdd',
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
            </template>
        </v-card>
      </v-container>
    </v-card-text>
  </v-card>
</template>

<script>
export default {
  name: "data-loan-information",
   props: {
    loans_lender: {
      type: Object,
      required: true
    },
    loans_spouse: {
      type: Object,
      required: true
    },
    loading: {
      type: Boolean,
      required: true
    },
    ver: {
      type: Boolean,
      required: true
    },
  },
  data: () => ({
    headers_loans: [
      {
        text: "Código",
        class: ["normal", "white--text"],
        align: "left",
        value: "code",
        width: "15%",
        sortable: true
      },
      {
        text: "Titular",
        class: ["normal", "white--text"],
        align: "left",
        value: "full_name",
        width: "5%",
        sortable: false
      },
      {
        text: "CI",
        class: ["normal", "white--text"],
        align: "left",
        value: "identity_card",
        width: "5%",
        sortable: false
      },
      {
        text: "Matricula",
        class: ["normal", "white--text"],
        align: "left",
        value: "registration",
        width: "5%",
        sortable: false
      },
      {
        text: "Modalidad",
        class: ["normal", "white--text"],
        align: "left",
        value: "shortened",
        width: "5%",
        sortable: false
      },
      {
        text: "Fecha Solicitud",
        class: ["normal", "white--text"],
        align: "left",
        value: "request_date",
        width: "10%",
        sortable: true
      },
      {
        text: "Fecha Desembolso",
        class: ["normal", "white--text"],
        align: "left",
        value: "disbursement_date",
        width: "10%",
        sortable: true
      },
      {
        text: "Monto solicitado",
        class: ["normal", "white--text"],
        align: "left",
        value: "amount",
        width: "10%",
        sortable: false
      },

      {
        text: "Plazo",
        class: ["normal", "white--text"],
        align: "left",
        value: "loan_term",
        width: "5%",
        sortable: false
      },
      {
        text: "Cuota",
        class: ["normal", "white--text"],
        align: "left",
        value: "estimated_quota",
        width: "10%",
        sortable: false
      },
      {
        text: "Saldo",
        class: ["normal", "white--text"],
        align: "left",
        value: "balance",
        width: "10%",
        sortable: false
      },
      {
        text: "Tipo Trámite",
        class: ["normal", "white--text"],
        align: "left",
        value: "origin",
        width: "10%",
        sortable: false
      },
      {
        text: "Estado",
        class: ["normal", "white--text"],
        align: "left",
        value: "state",
        width: "15%",
        sortable: true
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
        sortable: true
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
        sortable: true
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
  }),

  methods: {
    routeSismu(id) {
      window.open(
        "http://sismu.muserpol.gob.bo/musepol/akardex.aspx?" + id,
        "_blank"
      );
    },
    itemRowBackground: function (item) {
      return item.state === false ? "style-1" : "style-2";
    },
  },
  computed:{
    _headers_loans(){
        return this.headers_loans.filter((x)=>x.value!=='full_name'&&x.value!=='identity_card'&&x.value!=='registration')
    }
  }
};
</script>
<style>
.style-1 {
  background-color: rgb(82, 87, 43);
}
</style>