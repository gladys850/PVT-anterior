<template>
  <v-flex xs12 class="px-3">
      <v-form>
        <v-row justify="center">
          <v-col cols="12"  >
              <v-container class="py-0">
                <v-row>
                  <v-col class="text-center" cols="12" md="8">
                    <h2 class="success--text" > ES AFILIADO DE LA MUSERPOL</h2>
                  </v-col>
                  <v-col cols="12" md="3" >
                    <v-text-field
                      label="C.I. CODEUDOR"
                      class="py-0"
                      single-line
                      hide-details
                      clearable
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12" md="1">
                    <v-tooltip>
                      <template v-slot:activator="{ on }">
                        <v-btn
                          fab
                          dark
                          x-small
                          v-on="on"
                          color="info"
                          @click.stop="activar()">
                          <v-icon>mdi-magnify</v-icon>
                        </v-btn>
                      </template>
                    </v-tooltip>
                  </v-col>
                  <v-data-table
                    :headers="headers1"
                    :items="contributions1"
                    sort-by="calories"
                    class="elevation-1"
                  >
                    <template v-slot:top>
                      <v-toolbar flat color="white">
                        <v-toolbar-title>BOLETAS DE PAGO HIPOTECARIO</v-toolbar-title>
                          <v-divider
                            class="mx-4"
                            inset
                            vertical
                          ></v-divider>
                          <v-dialog v-model="dialog" max-width="500px">
                            <template v-slot:activator="{ on, attrs }">
                              <v-btn
                                fab
                                dark
                                x-small
                                v-on="on"
                                color="info"
                                @click.stop="bus.$emit('openDialog', { edit: true })"
                                v-bind="attrs"
                              ><v-icon>mdi-plus</v-icon>
                              </v-btn>
                            </template>
                            <v-card>
                              <v-card-title class="pa-0">
                                <v-toolbar dense flat color="tertiary">
                                  <v-toolbar-title>{{ formTitle }}</v-toolbar-title>
                                  <v-spacer></v-spacer>
                                  <v-btn icon @click.stop="close()">
                                    <v-icon>mdi-close</v-icon>
                                  </v-btn>
                                </v-toolbar>
                              </v-card-title>
                              <v-card-text class="py-0">
                                <v-container>
                                  <v-row>
                                    <v-col cols="12" sm="6" md="4">
                                      <v-text-field v-model="editedItem.name" dense label="Nombre"></v-text-field>
                                    </v-col>
                                    <v-col cols="12" sm="6" md="4">
                                      <v-text-field v-model="editedItem.payable_liquid" dense label="Liquido Pagable"></v-text-field>
                                    </v-col>
                                    <v-col cols="12" sm="6" md="4">
                                      <v-text-field v-model="editedItem.border_bonus" dense label="Bono Frontera"></v-text-field>
                                    </v-col>
                                    <v-col cols="12" sm="6" md="4">
                                      <v-text-field v-model="editedItem.east_bonus" dense label="Bono Oriente"></v-text-field>
                                    </v-col>
                                    <v-col cols="12" sm="6" md="4">
                                      <v-text-field v-model="editedItem.seniority_bonus" dense label="Bono Cargo"></v-text-field>
                                    </v-col>
                                    <v-col cols="12" sm="6" md="4">
                                      <v-text-field v-model="editedItem.public_security_bonus" dense label="Bono Seguridad Ciudadana"></v-text-field>
                                    </v-col>
                                  </v-row>
                                </v-container>
                              </v-card-text>
                              <v-card-actions class="pt-0">
                                <v-spacer></v-spacer>
                                <v-btn color="blue darken-1" dense text @click="close">Cancelar</v-btn>
                                <v-btn color="blue darken-1" dense text @click="save">Guardar</v-btn>
                              </v-card-actions>
                            </v-card>
                          </v-dialog>
                        </v-toolbar>
                      </template>
                      <template v-slot:item.actions="{ item }">
                        <v-icon
                          small
                          class="mr-2"
                          @click="editItem(item)"
                        >
                          mdi-pencil
                        </v-icon>
                        <v-icon
                          small
                          @click="deleteItem(item)"
                        >
                          mdi-delete
                        </v-icon>
                      </template>
                      <template v-slot:no-data>
                        <v-btn color="primary" @click="initialize">Reset</v-btn>
                      </template>
                    </v-data-table>
                </v-row>
              </v-container>
          </v-col>
        </v-row>
      </v-form>
  </v-flex>
</template>
<script>
export default {
  name: "ballots_hipotecary",
  data: () => ({
    editar:true,
    monto:null,
    plazo:null,
    interval:[],
    loanTypeSelected:null,
    visible:false,
    num_type:9,
    contributions1:{},
    modalidad: {},
    bonos: {},
    payable_liquid: {},
    modalities: {},
    prueba: {},
    intervalos: {},

 headers1: [
        {
          text: 'Nombre Afiliado',
          align: 'start',
          sortable: false,
          value: 'name',
        },
        { text: 'Liquido Pagable', value: 'payable_liquid' },
        { text: 'Bono Frontera', value: 'border_bonus' },
        { text: 'Bono Oriente', value: 'east_bonus' },
        { text: 'Bono Cargo', value: 'seniority_bonus' },
        { text: 'Bono Seguridad Ciudadana', value: 'public_security_bonus' },
        { text: 'Actions', value: 'actions', sortable: false },
      ],

        headers: [
        {
          text: 'Nombre Afiliado',
          align: 'start',
          sortable: false,
          value: 'name',
        },
        { text: 'Liquido Pagable', value: 'calories' },
        { text: 'Bono Frontera', value: 'fat' },
        { text: 'Bono Oriente', value: 'carbs' },
        { text: 'Bono Cargo', value: 'protein' },
        { text: 'Bono Seguridad Ciudadana', value: 'protein' },
        { text: 'Actions', value: 'actions', sortable: false },
      ],
      desserts: [],
      editedIndex: -1,
      editedItem: {
        name: '',
        payable_liquid: 0,
        border_bonus: 0,
        east_bonus: 0,
        seniority_bonus: 0,
        public_security_bonus: 0,
      },
      defaultItem: {
      name: '',
        payable_liquid: 0,
        border_bonus: 0,
        east_bonus: 0,
        seniority_bonus: 0,
        public_security_bonus: 0,
      },

  }),
   props: {
    contributions1: {
      type: Array,
      required: true
    }
  },
 computed: {
      formTitle () {
        return this.editedIndex === -1 ? 'Nueva Boleta' : 'Editar Boleta'
      },
    },

      watch: {
      dialog (val) {
        val || this.close()
      },
    },

 created () {
      this.initialize()
    },


   beforeMount() {
    this.getLoanIntervals()
  },
    mounted(){
    this.getLoanModality(this.$route.query.affiliate_id)
  },
  methods:
 {//caldula los intervalos deacuerdo a una modalidad
    Onchange(){
      for (this.i = 0; this.i< this.interval.length; this.i++) {
        if(this.loanTypeSelected==this.interval[this.i].procedure_type_id)
        {
          this.monto= this.interval[this.i].minimum_amount+' - '+this.interval[this.i].maximum_amount,
          this.plazo= this.interval[this.i].minimum_term+' - '+this.interval[this.i].maximum_term
          //intervalos es el monto, plazo y modalidad y id de una modalidad
          this.intervalos.maximun_amoun=this.interval[this.i].maximum_amount
          this.intervalos.maximum_term= this.interval[this.i].maximum_term
          this.intervalos.minimun_amoun=this.interval[this.i].minimum_amount
          this.intervalos.minimum_term= this.interval[this.i].minimum_term
          this.intervalos.procedure_type_id= this.loanTypeSelected
          this.num_type=this.loanTypeSelected
               this.getLoanModality(this.$route.query.affiliate_id)
          this.getBallots(this.$route.query.affiliate_id)
          console.log('este es la modalidad del intervalo'+this.num_type)

        }
      }
 
   
    },
    clearForm()
    {
      this.payable_liquid[0]=0
      this.payable_liquid[1]=0
      this.payable_liquid[2]=0
      this.bonos[0]=0
      this.bonos[1]=0
      this.bonos[2]=0
      this.bonos[3]=0
    },
    //Medodo donde identifica la modalidad de acuerdo a las caracteristicas de un affiliado
    async getLoanModality(id) {
      try {
        let resp = await axios.get(`affiliate/${id}/loan_modality`,{
          params: {
            procedure_type_id:this.num_type,
            external_discount:0,
          }
        })
        console.log('entro a get modality'+this.num_type)
          this.loan_modality= resp.data
          this.modalidad.id=this.loan_modality.id
          this.modalidad.name=this.loan_modality.name
          this.modalidad.quantity_ballots=this.loan_modality.loan_modality_parameter.quantity_ballots
          this.modalidad.guarantors=this.loan_modality.loan_modality_parameter.guarantors
          this.modalidad.min_guarantor_category=this.loan_modality.loan_modality_parameter.min_guarantor_category
          this.modalidad.max_guarantor_category=this.loan_modality.loan_modality_parameter.max_guarantor_category
         this.modalidad.personal_reference=this.loan_modality.loan_modality_parameter.personal_reference
    
    
    //this.modalidad.personal_reference=true
        this.prueba[0]=this.loan_modality.loan_modality_parameter.guarantors
        this.prueba[1]=this.loan_modality.loan_modality_parameter.min_guarantor_category
        this.prueba[2]=this.loan_modality.loan_modality_parameter.max_guarantor_category
        this.prueba[3]=this.loan_modality.loan_modality_parameter.personal_reference
          if(this.loan_modality.loan_modality_parameter.quantity_ballots>1)
          {
          this.visible=true
          }
          else{
          this.visible=false
        }
      }catch (e) {
        console.log(e)
      }finally {
        this.loading = false
      }
    },
    //Intervalos de Plazo y Meses de una modalidad
    async getLoanIntervals() {
      try {
        let res = await axios.get(`loan_interval`)
        this.interval = res.data
       }catch (e) {
        console.log(e)
      }finally {
        this.loading = false
      }
    },
    //Metodo para sacar boleta de un afiliado
    async getBallots(id) {
    try {
      let res = await axios.get(`affiliate/${id}/contribution`, {
        params:{
          city_id: this.$store.getters.cityId,
          sortBy: ['month_year'],
          sortDesc: [1],
          per_page: this.modalidad.quantity_ballots,
          page: 1,
        }
      })
      if(res.data.valid)
      {
        this.editar=false
        this.datos=res.data.data
        for (this.i = 0; this.i< this.datos.length; this.i++) {
          this.payable_liquid[this.i]= this.datos[this.i].payable_liquid,
          this.bonos[0]= this.datos[0].border_bonus,
          this.bonos[1]= this.datos[0].east_bonus,
          this.bonos[2]= this.datos[0].seniority_bonus,
          this.bonos[3]= this.datos[0].public_security_bonus
        }
          for(this.j = 0; this.j< this.datos.length; this.j++)
        {
          this.contributions1[this.j].payable_liquid=this.datos[this.j].payable_liquid
          if(this.j==0){
            this.contributions1[this.j].border_bonus= this.datos[this.j].border_bonus,
            this.contributions1[this.j].east_bonus= this.datos[this.j].east_bonus,
            this.contributions1[this.j].seniority_bonus= this.datos[this.j].seniority_bonus,
            this.contributions1[this.j].public_security_bonus= this.datos[this.j].public_security_bonus
          }
          else{
            this.contributions1[this.j].border_bonus=0,
            this.contributions1[this.j].east_bonus= 0,
            this.contributions1[this.j].seniority_bonus=0,
            this.contributions1[this.j].public_security_bonus=0
          }
        }
      }
    } catch (e) {
      console.log(e)
    } finally {
      this.loading = false
    }
  },


  initialize () {
        this.desserts = [
          {
            name: 'Frozen Yogurt',
            calories: 159,
            fat: 6.0,
            carbs: 24,
            protein: 4.0,
          },
          {
            name: 'Ice cream sandwich',
            calories: 237,
            fat: 9.0,
            carbs: 37,
            protein: 4.3,
          },
        ]
      },

      editItem (item) {
        this.editedIndex = this.contributions1.indexOf(item)
        this.editedItem = Object.assign({}, item)
        this.dialog = true
      },

      deleteItem (item) {
        const index = this.contributions1.indexOf(item)
        confirm('Are you sure you want to delete this item?') && this.contributions1.splice(index, 1)
      },

      close () {
        this.dialog = false
        this.$nextTick(() => {
          this.editedItem = Object.assign({}, this.defaultItem)
          this.editedIndex = -1
        })
      },

      save () {
        if (this.editedIndex > -1) {
          Object.assign(this.contributions1[this.editedIndex], this.editedItem)
        } else {
          this.contributions1.push(this.editedItem)
        }
        this.close()
      },





 }
};
</script>