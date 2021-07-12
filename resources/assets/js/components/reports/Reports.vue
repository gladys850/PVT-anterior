<template>
  <v-container fluid>
    <ValidationObserver>
      <v-form>
        <v-card flat>
          <v-card-title class="pa-0 pb-3">
            <v-toolbar dense color="tertiary" class="font-weight-regular">
              <v-toolbar-title>REPORTES</v-toolbar-title>
            </v-toolbar>
          </v-card-title>
          <v-card-text>
            <v-tabs dark active-class="secondary" v-model="tab">
              <v-tab v-for="item in actions" :key="item.nameTab">{{item.nameTab}}</v-tab>
            </v-tabs>
            <v-tabs-items v-model="tab">
              <v-tab-item v-for="item in actions" :key="item.nameTab">
                <v-row align="center" no-gutters>
                  <v-col cols="4" class="pa-2">
                    <v-toolbar-title>
                      <b>Seleccione un reporte</b>
                    </v-toolbar-title>
                    <v-progress-linear class="mb-5"></v-progress-linear>
                    <v-select
                      dense
                      outlined
                      v-model="report_selected"
                      :items="computedReportsItems"
                      item-text="name"
                      return-object
                      label="Seleccione un reporte">
                    </v-select>
                    <!-- SOLAMENTE SE MOSTRARA CUANDO HAYA UN REPORTE SELECCIONADO -->
                    <template v-if="report_selected">
                      <template v-if="report_selected.criterios.includes('initial_date') || report_selected.criterios.includes('final_date') || report_selected.criterios.includes('date')">
                        <v-toolbar-title>
                          <b>Criterios de búsqueda</b>
                        </v-toolbar-title>
                      <v-progress-linear class="mb-5"></v-progress-linear>
                      </template>
                      <template v-if="report_selected.criterios.includes('initial_date')">
                        <v-text-field
                          dense
                          v-model="report_inputs.initial_date"
                          label="Fecha inicio"
                          hint="Día/Mes/Año"
                          type="date"
                          :max="$moment(Date.now()).format('YYYY-MM-DD')"
                          outlined
                        ></v-text-field>
                      </template>
                      <template v-if="report_selected.criterios.includes('final_date')">
                        <v-text-field
                          dense
                          v-model="report_inputs.final_date"
                          label="Fecha final"
                          hint="Día/Mes/Año"
                          type="date"
                          :min="report_inputs.initial_date"
                          :max="$moment(Date.now()).format('YYYY-MM-DD')"
                          outlined
                        ></v-text-field>
                      </template>
                      <template v-if="report_selected.criterios.includes('date')">
                        <v-text-field
                          dense
                          v-model="report_inputs.date"
                          :label="report_selected.label"
                          hint="Día/Mes/Año"
                          type="date"
                          outlined
                        ></v-text-field>
                      </template>
                      <v-btn
                        color="primary"
                        :loading="loading_button"
                        @click.stop="downloadReport()"
                        >Descargar reporte</v-btn>
                    </template>
                  </v-col>
                </v-row>
              </v-tab-item>
            </v-tabs-items>
          </v-card-text>
        </v-card>
      </v-form>
    </ValidationObserver>
  </v-container>
</template>

<script>
export default {
  name: "Reports",
  data: () => ({
    tab: null,
    actions: [
      { nameTab: "Reportes de Préstamos", value: "prestamos" },
      { nameTab: "Reportes de Amortizaciones", value: "amortizaciones" },
    ],
    loading_button: false,
    // Reports
    reports_items: [],
    report_selected: null,
    report_inputs: {
      initial_date: null,
      final_date: null,
      date: null,
    },
  }),
  created() {
    this.reports_items = [
      {
        id: 1,
        name: "Rep. Amortizaciones de descuentos Titular - Garante",
        tab: 1,
        criterios: ["initial_date", "final_date"],
        service: "/report_amortization_discount_months",
      },
      {
        id: 2,
        name: "Rep. Amortizaciones en efectivo y deposito en cuenta",
        tab: 1,
        criterios: ["initial_date", "final_date"],
        service: "/report_amortization_cash_deposit",
      },
      {
        id: 3,
        name: "Rep. Amortizaciones por ajustes",
        tab: 1,
        criterios: ["initial_date", "final_date"],
        service: "/report_amortization_ajust",
      },
      {
        id: 4,
        name: "Rep. Amortizaciones pendientes de confirmacion de acuerdo al comprobante de generacion",
        tab: 1,
        criterios: ["initial_date", "final_date"],
        service: "/report_amortization_pending_confirmation",
      },
      {
        id: 5,
        name: "Rep. Amortizaciones por complemento y fondo de retiro",
        tab: 1,
        criterios: ["initial_date", "final_date"],
        service: "/report_amortization_fondo_complement",
      },
      {
        id: 6,
        name: "Rep. Préstamos desembolsados",
        tab: 0,
        criterios: ["initial_date", "final_date"],
        service: "/report_loan_vigent",
      },
      {
        id: 7,
        name: "Rep. Préstamos del estado de cartera",
        tab: 0,
        criterios: ["initial_date", "final_date"],
        service: "/report_loan_state_cartera",
      },
      {
        id: 8,
        name: "Rep. Información de préstamos para solicitud de descuentos",
        tab: 0,
        criterios: ["date"],
        service: "/loan_information",
        label:"Periódo (Seleccione el último dia del mes)"
      },
      {
        id: 9,
        name: "Rep. Préstamos en mora",
        tab: 0,
        criterios: [],
        service: "/report_loans_mora",
      },
      {
        id: 10,
        name: "Rep. Préstamos amortizados mensualmente mediante descuentos por garantía",
        tab: 0,
        criterios: [],
        service: "/loan_defaulted_guarantor",
      },
      {
        id: 11,
        name: "Rep. Préstamos vigentes simultaneos en PVT y SISMU",
        tab: 0,
        criterios: ["date"],
        service: "/loan_pvt_sismu_report",
        label: "Fecha final"
      },
    ];
  },
  methods: {
    clearInputs() {
      this.report_selected = null;
      this.report_inputs.initial_date = null;
      this.report_inputs.final_date = null;
      this.report_inputs.date = null;
    },
    async downloadReport() {
      if (this.report_selected) {
        let params = [];
        //this.validateCriterios()
        const formData = new FormData();
        // let validation = true
        this.report_selected.criterios.forEach((criterio) => {
          let respuesta = this.report_inputs[criterio];
          if (respuesta != null) {
            params += criterio + "=" + this.report_inputs[criterio] + "&";
            formData.append(criterio, this.report_inputs[criterio]);
          } else {
            // validation =false
          }
        });
        // if(validation) {
        //   console.log(`${this.report_selected.service}?${params}`)
        // } else {
        //   this.toastr.error("Debe ingresar todos los campos");
        // }
        // console.log(`${this.report_selected.service}?${params}`)
        this.loading_button = true;
        await axios({
          url: this.report_selected.service,
          method: "GET",
          responseType: "blob", // important
          headers: { Accept: "application/vnd.ms-excel" },
          //headers: { Accept: "text/plain" },
          data: formData,
          params: {
            initial_date: this.report_inputs.initial_date,
            final_date: this.report_inputs.final_date,
            date: this.report_inputs.date,
          },
        })
          .then((response) => {
            console.log(response.data);
            const url = window.URL.createObjectURL(new Blob([response.data]));
            const link = document.createElement("a");
            link.href = url;
            link.setAttribute("download", this.report_selected.name + ".xls");
            document.body.appendChild(link);
            link.click();
            this.clearInputs();
          })
          .catch((e) => {
            console.log(e);
            this.loading_button = false;
          });
        this.loading_button = false;
      }
    },
  },
  computed: {
    computedReportsItems() {
      return this.reports_items.filter((item) => item.tab == this.tab);
    },
  },
};
</script>
