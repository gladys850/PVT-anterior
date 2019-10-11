<template>
  <v-container fluid >
      <v-row justify="center">
        <v-col cols="12" md="11" class="v-card-profile" >
              <v-row justify="center">
              <v-col cols="12">
                <v-toolbar-title>INFORMACION POLICIAL</v-toolbar-title>
              </v-col>
            <v-col cols="12" md="7" >
              <v-select
                :loading="loading"
                :items="affiliateState"
                data-vv-name="Estado"
                item-text="name"
                item-value="id"
                label="Estado"
                v-model="affiliate.affiliate_state_id"
                hint="Activo"
                persistent-hint
                :readonly="!editable"
              ></v-select>
            </v-col>
            <v-col cols="12" md="5" >
              <v-menu
                v-model="dates.dateEntry.show"
                :close-on-content-click="false"
                transition="scale-transition"
                offset-y
                max-width="290px"
                min-width="290px"
                :disabled="!editable"
              >
                <template v-slot:activator="{ on }">
                  <v-text-field
                    v-model="dates.dateEntry.formatted"
                    label="Fecha Ingreso a la Institucion Policial"
                    hint="Día/Mes/Año"
                    persistent-hint
                    append-icon="mdi-calendar"
                    readonly
                    v-on="on"
                  ></v-text-field>
                </template>
                <v-date-picker v-model="affiliate.date_entry" no-title @input="dates.dateEntry.show = false"></v-date-picker>
              </v-menu>
            </v-col>
            <v-col cols="12" md="6" >
              <v-select
                :loading="loading"
                data-vv-name="categoria"
                :items="category"
                item-text="name"
                item-value="id"
                label="Categoria"
                name="categoria"
                v-model="affiliate.category_id"
                :readonly="!editable"
              ></v-select>
            </v-col>
            <v-col cols="12" md="6">
              <v-text-field
                v-model="affiliate.service_years"
                label="Años de Servicio"
                v-validate.initial="'numeric|min:1|max:100'"
                :error-messages="errors.collect('nro')"
                data-vv-name="nro"
                :readonly="!editable"
              ></v-text-field>
            </v-col>
            <v-col cols="12" md="6" >
              <v-text-field
                v-model="affiliate.service_months"
                label="Meses de Servicio"
                v-validate.initial="'numeric|min:1|max:100'"
                :error-messages="errors.collect('nro')"
                data-vv-name="nro"
                :readonly="!editable"
              ></v-text-field>
            </v-col>
            <v-col cols="12" md="6" >
              <v-select
                :loading="loading"
                data-vv-name="Grado"
                :items="degree"
                item-text="name"
                item-value="id"
                label="Grado"
                name="Grado"
                v-model="affiliate.degree_id"
                :readonly="!editable"
                ></v-select>
              </v-col>
            <v-col cols="12" >
              <v-select
                :loading="loading"
                data-vv-name="Gestor"
                :items="pension_entity"
                item-text="name"
                item-value="id"
                label="Ente Gestor"
                name="Grado"
                v-model="affiliate.pension_entity_id"
                :readonly="!editable"
            ></v-select>
            </v-col>
            <v-col cols="12">
              <v-menu
                v-model="dates.dateDerelict.show"
                :close-on-content-click="false"
                transition="scale-transition"
                offset-y
                max-width="290px"
                min-width="290px"
                :disabled="!editable"
              >
                <template v-slot:activator="{ on }">
                  <v-text-field
                    v-model="dates.dateDerelict.formatted"
                    label="Fecha Desvinculacion"
                    hint="Día/Mes/Año"
                    persistent-hint
                    append-icon="mdi-calendar"
                    readonly
                    v-on="on"
                  ></v-text-field>
                </template>
                <v-date-picker v-model="affiliate.date_derelict" no-title @input="dates.dateDerelict.show = false"></v-date-picker>
              </v-menu>
            </v-col>
          </v-row>
        </v-col>
      </v-row>
  </v-container>
</template>

<script>
  export default {
  name: "affiliate-police-data",
  props: {
    affiliate: {
      type: Object,
      required: true
    },
    editable: {
      type: Boolean,
      required: true
    },
  },
  data: () => ({
    affiliateState: [],
    category: [],
    degree: [],
    pension_entity: [],
    dates: {
      dateEntry: {
        formatted: null,
        picker: false
      },
      dateDerelict: {
        formatted: null,
        picker: false
      }
    }
  }),
  computed: {
    getCalculateCategory(){
    let years = this.affiliate.service_years;
    let months = this.affiliate.service_months;
    if(this.affiliate.service_years==null ||this.affiliate.service_months ==null )
    {
      return this.affiliate.category_id
    }
    else{
      if (years < 0 || years >100  ) {
          return "error";
        }
        else{
          if (months > 0) {
          years++;
        }
        let categoria = this.category.find(c =>{
          return c.from <= years && c.to >= years
        })
        if(!!categoria){
          this.affiliate.category_id = categoria.id
        }
        }
    }
  },
  },
  beforeMount() {
    this.getCategory();
    this.getDegree();
    this.getPensionEntity();
    this.getAffiliateState();
  },
  watch: {
    'affiliate.date_entry': function(date) {
      if (date) this.dates.dateEntry.formatted = this.$moment(date).format('L')
    },
    'affiliate.date_derelict': function(date) {
      if (date) this.dates.dateDerelict.formatted = this.$moment(date).format('L')
    }
  },
  mounted() {
    if (this.$route.params.id != 'new') {
      this.getAffiliate(this.$route.params.id)
    }
  },
  methods: {
    async getCategory() {
    try {
      this.loading = true
      let res = await axios.get(`category`);
      this.category = res.data;
    } catch (e) {
      this.dialog = false;
      console.log(e);
    }finally {
        this.loading = false
      }
  },
    async getAffiliateState() {
    try {
      this.loading = true
      let res = await axios.get(`affiliateState`);
      this.affiliateState = res.data;
    } catch (e) {
      this.dialog = false;
      console.log(e);
    }finally {
        this.loading = false
      }
  },
    async getDegree() {
    try {
      this.loading = true
      let res = await axios.get(`degree`);
      this.degree = res.data;
    } catch (e) {
      this.dialog = false;
      console.log(e);
    }finally {
        this.loading = false
      }
  },
    async getPensionEntity() {
      try {
      this.loading = true
      let res = await axios.get(`pensionEntity`);
      this.pension_entity = res.data;
    } catch (e) {
      this.dialog = false;
      console.log(e);
    }finally {
        this.loading = false
      }
    },
  }
  }
</script>