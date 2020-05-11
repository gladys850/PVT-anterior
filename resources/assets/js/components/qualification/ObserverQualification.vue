<template>
  <v-container class="mb-0 " >
    <v-row justify="center" class="mb-0">
      <v-col cols="12" class="mb-0" >
        <v-row justify="center" class="mb-0">
          <v-col cols="12" class="mb-0">
            <v-tabs
              background-color="primary"
              center-active
              dark
            >
              <v-tab>Observaciones</v-tab>
                <v-tab-item>
                  <v-card flat tile >
                    <v-card-text class="mb-0">
                      <v-tooltip top>
                        <template v-slot:activator="{ on }">
                          <v-btn
                            fab
                            dark
                            x-small
                            :color="'success'"
                            bottom
                            right
                            v-on="on"
                            style="margin-right: 0px; margin-left: 950px; margin-top:-11px; "
                            @click.stop="bus.$emit('openDialog', { edit:false })"
                          >
                            <v-icon>mdi-plus</v-icon>
                          </v-btn>
                        </template>
                        <div>
                          <span>Agregar Observacion</span>
                        </div>
                      </v-tooltip>
                    </v-card-text>
                  </v-card>
                  <v-card flat tile >
                    <v-card-text class="mb-0">
                      <v-col cols="12" class="mb-0">
                        <v-data-table
                          :headers="headers1"
                          :items="observations"
                          :items-per-page="4"
                          class="elevation-1"
                        >
                          <template v-slot:item="items">
                            <tr>
                              <td>{{items.item.user_id}}</td>
                              <td>{{items.item.observation_type_id}}</td>
                              <td>{{items.item.message}}</td>
                              <td>{{items.item.date}}</td>
                              <td>
                                <v-tooltip top>
                                  <template v-slot:activator="{ on }">
                                    <v-btn
                                      icon
                                      small
                                      color="info"
                                      @click.stop="bus.$emit('openDialog', {...items.item, ...{edit: true}})"
                                      v-on="on"
                                    >    
                                      <v-icon>mdi-pencil</v-icon>
                                    </v-btn>
                                  </template>
                                  <span class="caption">Editar</span>
                                </v-tooltip>   
                              </td>
                            </tr>
                          </template>
                        </v-data-table>
                      </v-col>
                    </v-card-text>
                  </v-card>
                </v-tab-item>
              <v-tab>HISTORIAL DEL TRAMITE </v-tab>
                <v-tab-item>
                  <v-card flat tile >
                    <v-card-text>
                      <v-col cols="12" class="mb-0">
                        <v-data-table
                          :headers="headers"
                          :items="record"
                          :items-per-page="4"
                          class="elevation-1"
                        >
                          <template v-slot:item="items">
                            <tr>
                              <td>{{items.item.user_id}}</td>
                              <td>{{items.item.record_type_id}}</td>
                              <td>{{items.item.recordable_id}}</td>
                              <td>{{items.item.action}}</td>
                            </tr>
                          </template>
                        </v-data-table>
                      </v-col>
                    </v-card-text>
                  </v-card>
                </v-tab-item>
              </v-tabs>
          </v-col>

<v-col cols="4">
        <v-select
          label="Devolver"
  v-model="valDevolver"
       
        
          
        
          item-text="text"
          item-value="value"
        ></v-select>
      </v-col>
      <v-col cols="1">
        <v-btn small color="danger" @click.stop>Devolver</v-btn>
      </v-col>


           <v-col cols="1" class="py-0">
            <v-checkbox    
              :label="`Revisar`"
              v-model="valor"
@click.stop="save()"
            ></v-checkbox>
          </v-col> 
           <v-col cols="1">
        <v-btn small color="success" @click.stop>Guardar</v-btn>
      </v-col>
             
          <v-spacer></v-spacer>
          <v-col cols="10"></v-col>
          <v-col cols="2" class="py-0">
            <v-btn 
              color="primary" 
              @click.stop="saveLoanRol()">Finalizar</v-btn>   
            </v-col>
          </v-row>

        </v-col>
      </v-row>
                 <AddObservation :bus="bus"/>
  </v-container>
    
   
</template>

<script>

import AddObservation from '@/components/qualification/AddObservation'

export default {
  name: "observer-qualification",

   components: {
    AddObservation
  },

  data: () => ({
     valDevolver: null,
    loanFlow:[],
    valor:true,
   
    observation_type:[],
    loanTypeSelected:null,
    observationSelected:null,
    observation_description:null,
    visible:false,
    visible1:false,
    visible2:false,
    listAreas: [],
    valDerivar: null,
    flow: {},
    mapAreas: null,
    bus: new Vue(),
    lista:[
    {
      id:'1',
      name:'DEVOLVER'
    },
    {
      id:'2',
      name:'DERIVAR'
    },
    {
      id:'3',
      name:'REVISAR'
    }
    ],
    
    dialog: false,
    headers: [
        { text: 'Usuario',class: ['normal', 'white--text'], align: 'left', value: 'user_id' },
        { text: 'Record',class: ['normal', 'white--text'], align: 'left', value: 'record_type_id' },
        { text: 'Nombre',class: ['normal', 'white--text'], align: 'left', value: 'recordable_id' },
        { text: 'Accion',class: ['normal', 'white--text'], align: 'left', value: 'accion' }
        ],
    headers1: [
        { text: 'Usuario', class: ['normal', 'white--text'], align: 'left', value: 'user_id' },
        { text: 'Observacion', class: ['normal', 'white--text'], align: 'left', value: 'observation_type_id' },
        { text: 'Mensaje', class: ['normal', 'white--text'], align: 'left', value: 'message' },
        { text: 'Fecha', class: ['normal', 'white--text'], align: 'left', value: 'date' },
        { text: 'Acciones', class: ['normal', 'white--text'], align: 'center', width: '11%', sortable: false
      }
        ],
    
  }),
  props: {
    observations: {
      type: Array,
      required: true
    },
     record: {
      type: Array,
      required: true
    }},
  
  beforeMount() {
   
  
    this.getObservationType()
   
  },
  mounted(){
      this.getFlow()
       this.bus.$on('saveObservation', (observation) => {
        console.log("entro al bus"+observation)
      
        
            this.observations.unshift(observation)
       
        



    })
  
  },
  
  methods: {
    save(){
      this.valor=false,
      console.log("se grabo")
    },
    Onchange(){
      for (this.i = 0; this.i< this.lista.length; this.i++) {
        if(this.loanTypeSelected==1)
        {
          this.visible=true
          this.visible1=false
          this.visible2=false
        }
        else{
          if(this.loanTypeSelected==2)
         {
            this.visible=false
            this.visible1=true
            this.visible2=false
          }
          else{
            if(this.loanTypeSelected==3)
            {
              this.visible=false
              this.visible1=false
              this.visible2=true
            }
          }
        }
      }      
    },
  async getFlowsss(id) {
    try {
      this.loading = true
      let res = await axios.get(`loan/${id}/flow`)
      this.loanFlow = res.data
      console.log('este es el flow'+this.loanFlow)
    } catch (e) {
      console.log(e)
    } finally {
        this.loading = false
      }
    },
    async getFlow() {
      try {
        this.loading = true
        let response = await axios.get(`loan/${4}/flow`)
        if (response.data) {
          this.flow = response.data
        }
      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    },
  async saveLoanRol() {
    try {
      let res = await axios.patch(`loan/${1}`, {
      rol_id:73,
    });
      console.log('se cambio el rol')
    } catch (e) {
      console.log(e)
    } finally {
      this.loading = false
    }
  },

  },
  
  }
</script>