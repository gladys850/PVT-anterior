<template>
  <v-container fluid>
    <ValidationObserver ref="observerHipotecaryData">
      <v-form>
        <v-card flat>
          <v-card-title class="pa-0 pb-3">
            <v-toolbar dense color="tertiary" class="font-weight-regular">
              <v-toolbar-title>IMPORTACIÓN / EXPORTACIÓN</v-toolbar-title>
            </v-toolbar>
          </v-card-title>
          <v-card-text>
            <v-row align="center" no-gutters>
              <v-col cols="12">
                <v-card-text class="py-0">
                  <v-layout row wrap>
                    <v-toolbar-title>
                      <b>Información para descuento</b>
                    </v-toolbar-title>
                    <v-progress-linear></v-progress-linear>
                    <br />
                    <v-col cols="12" md="4" class="py-0">
                      <ValidationProvider
                        v-slot="{ errors }"
                        name="Acción a realizar"
                        rules="required"
                      >
                        <v-select
                          :error-messages="errors"
                          dense
                          :items="actions"
                          item-text="name"
                          item-value="value"
                          label="Acción a realizar"
                          outlined
                          v-model="import_export.action"
                        ></v-select>
                      </ValidationProvider>
                    </v-col>
                    <template v-if="import_export.action!=null">
                      <v-col cols="12" md="4" class="py-0">
                        <v-menu
                          v-model="dates.disbursementDate.show"
                          :close-on-content-click="false"
                          transition="scale-transition"
                          offset-y
                          max-width="290px"
                          min-width="290px"
                        >
                          <template v-slot:activator="{ on }">
                            <v-text-field
                              dense
                              v-model="dates.disbursementDate.formatted"
                              label="Fecha de corte"
                              hint="Día/Mes/Año"
                              persistent-hint
                              append-icon="mdi-calendar"
                              v-on="on"
                              outlined
                            ></v-text-field>
                          </template>
                          <v-date-picker
                            v-model="import_export.disbursement_date"
                            no-title
                            @input="dates.disbursementDate.show = false"
                          ></v-date-picker>
                        </v-menu>
                      </v-col>
                      <v-col cols="12" md="4" class="py-0">
                        <ValidationProvider
                          v-slot="{ errors }"
                          name="Estado del afiliado"
                          rules="required"
                        >
                          <v-select
                            :error-messages="errors"
                            dense
                            :items="state_affiliate"
                            item-text="name"
                            item-value="value"
                            label="Estado del afiliado"
                            outlined
                          ></v-select>
                        </ValidationProvider>
                      </v-col>
                      <v-col cols="12" md="4" class="py-0">
                        <ValidationProvider
                          v-slot="{ errors }"
                          name="Código comprobante"
                          rules="required"
                        >
                          <v-text-field
                            :error-messages="errors"
                            dense
                            label="Código comprobante"
                            outlined
                          ></v-text-field>
                        </ValidationProvider>
                      </v-col>
                      <v-col cols="12" md="4" class="py-0" v-if="import_export.action=='import'">
                        <v-file-input
                          counter
                          show-size
                          truncate-length="30"
                          outlined
                          small-chips
                          dense
                          label="Importar información"
                        ></v-file-input>
                      </v-col>

                      <br />
                      <v-col cols="12" md="12" class="py-0" v-if="import_export.action=='export'">
                        <v-btn color="primary" @click.stop="excel()">Generar Información</v-btn>
                      </v-col>
                      <br />
                    </template>
                    <br />
                    <v-progress-linear></v-progress-linear>
                  </v-layout>
                </v-card-text>
              </v-col>
              <!--<v-col cols="12" md="12">
    <v-card>
        <v-card-title>
        <h4 class="pull-left">Importar Comando</h4>
        <div class="ibox-tools"></div>
        </v-card-title>
        <v-card-title>
        <input class="form-control" type="file" id="file-upload" :disabled="loadingButton"/>
        <br>
        </v-card-title>
      </v-card>
              </v-col>-->
            </v-row>
          </v-card-text>
        </v-card>
      </v-form>
    </ValidationObserver>
  </v-container>
</template>

<script>
export default {
  name: "payment-ImportExport",
  data: () => ({
    /*return {
        loadingButton: false,
        found: 0,
        notFound: [],
        showResults: false,
        refresh: false,
        };*/
    import_export: {
      action: null
    },
    datos: [],
    dates: {
      disbursementDate: {
        formatted: null,
        picker: false
      }
    },
    actions: [
      { name: "Importación", value: "import" },
      { name: "Exportación", value: "export" }
    ],
    state_affiliate: [
      { name: "Activo", value: "A" },
      { name: "Pasivo", value: "P" }
    ]
  }),
  mounted() {
    this.formatDate("disbursementDate", this.import_export.disbursement_date);
  },
  watch: {
    "import_export.disbursement_date": function(date) {
      this.formatDate("disbursementDate", date);
    }
  },
  methods: {
    formatDate(key, date) {
      if (date) {
        this.dates[key].formatted = this.$moment(date).format("L");
      } else {
        this.dates[key].formatted = null;
      }
    },
    async excel1() {
      try {
        let res = await axios.get(`excel`);
        console.log(res.data);
      } catch (e) {
        console.log(e);
      }
    },
    async excel() {
      //this.loadingButton = true;
      await axios({
        url: "/excel",
        method: "GET",
        responseType: "blob", // important
        headers: { Accept: "application/vnd.ms-excel" },
        data: this.datos
      })
        // .post("eco_com_report_excel", this.form)
        .then(response => {
          console.log(response);
          const url = window.URL.createObjectURL(new Blob([response.data]));
          const link = document.createElement("a");
          link.href = url;
          link.setAttribute("download", "file.xlsx");
          document.body.appendChild(link);
          link.click();
        })
        .catch(error => {
          console.log(error);
        });
      //this.loadingButton = false;
    }
    /* async refreshData() {
        this.refresh = true;
        this.sendForm()
    },
    }*/
  }
};
</script>
