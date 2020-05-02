<template>
  <v-container class="mb-0 " >
    <v-row justify="center" class="mb-0">
      <v-col cols="12" class="mb-0" >
        <v-row justify="center" class="mb-0">
          <v-col cols="12" class="mb-0">
            <v-toolbar-title>HISTORIAL DEL TRAMITE</v-toolbar-title>
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
            <v-col cols="6">
              <v-toolbar-title>OBSERVACIONES</v-toolbar-title>
            </v-col>
            <v-col cols="6" class="ma-0 pr-10">
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
                    style="margin-right: 0px; margin-left: 0px; margin-top:10px; "
                    @click.stop="dialog = true"
                  >
                    <v-icon>mdi-plus</v-icon>
                  </v-btn>
                </template>
                <div>
                  <span>Agregar Observacion</span>
                </div>
              </v-tooltip>
            </v-col>
            <v-dialog
              v-model="dialog"
              width="600"
            >
              <v-card>
                <v-card-title    
                  class="headline primary text-white"
                  >Adicionar Observación
                </v-card-title>
              <v-card-text>
                <v-container>
                  <v-row>
                    <v-col cols="4">
                      <label>Tipo de Observacion:</label>
                    </v-col>
                    <v-col cols="8">
                      <v-select
                        dense
                        :items="observation_type"
                        item-text="name"
                        item-value="id"
                        persistent-hint
                      ></v-select>
                    </v-col>
                    <v-col cols="12">
                      <v-text-field
                        dense
                        label="Descripcion"
                      ></v-text-field>
                    </v-col>                   
                  </v-row>
                </v-container>
              </v-card-text>
              <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn
                  color="green darken-1"
                  @click="saveObservation()"
                >
                  Guardar
                </v-btn>
                <v-btn
                  color="red"
                  @click="dialog = false"
                >
                  Cancelar
                </v-btn>
              </v-card-actions>
            </v-card>
          </v-dialog>
          <v-col cols="12">
            <v-data-table
              :headers="headers1"
              :items="observation"
              hide-default-footer
              class="elevation-1"
            >  
              <template v-slot:item="items">
                <tr>
                  <td>{{items.item.user_id}}</td>
                  <td>{{items.item.observation_type_id}}</td>
                  <td>{{items.item.message}}</td>
                  <td>{{items.item.date}}</td>
                </tr>
              </template>
            </v-data-table>
          </v-col>
          <v-col cols="3">
            <v-toolbar-title class="text-left">ACCION DE LA TAREA :</v-toolbar-title>
          </v-col>
          <v-col cols="2" >
            <v-select
              dense
              v-model="loanTypeSelected"
              :onchange="Onchange()"
              :items="lista"
              item-text="name"
              item-value="id"
              persistent-hint
            ></v-select>
          </v-col> 
          <v-col v-show="visible" cols="4" >
            <v-select
              dense
              :items="loanFlow.previous"
              item-text="id"
              item-value="id"
              persistent-hint
            ></v-select>
          </v-col>
          <v-col v-show="visible1" cols="4" >
            <v-select
              dense
              :items="loanFlow.next"
              item-text="id"
              item-value="id"
              persistent-hint
            ></v-select>
          </v-col>  
         <v-col v-show="visible2" cols="4" class="py-0">
            <v-checkbox    
              :label="`Revisado`"
            ></v-checkbox>
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
  </v-container>
</template>

<script>
export default {
  name: "observer-qualification",

  data: () => ({
    loanFlow:[],
    observation_type:[],
    loanTypeSelected:null,
    visible:false,
    visible1:false,
    visible2:false,
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
        { text: 'Usuario', align: 'left', value: 'user_id' },
        { text: 'Record', align: 'left', value: 'record_type_id' },
        { text: 'Nombre', align: 'left', value: 'recordable_id' },
        { text: 'Accion', align: 'left', value: 'accion' }
        ],
    headers1: [
        { text: 'Usuario', align: 'left', value: 'user_id' },
        { text: 'Observacion', align: 'left', value: 'observation_type_id' },
        { text: 'Mensaje', align: 'left', value: 'message' },
        { text: 'Fecha', align: 'left', value: 'date' }
        ],
    
  }),
  
  beforeMount() {
    this.getFlow(1)
    this.getRecords()
    this.getObservationType()
    this.getObservation(1)
  },
  
  methods: {
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
  async getFlow(id) {
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
  async getObservationType() {
    try {
      this.loading = true
      let res = await axios.get(`module/${6}/observation_type`)
      this.observation_type = res.data
      console.log('estas son las observaciones'+this.observation_type)
    } catch (e) {
      console.log(e)
    } finally {
      this.loading = false
    }
    },
  async getRecords() {
    try {
      this.loading = true
      let res = await axios.get(`record`,{param:{
      loan_id:1
    }})
      this.record = res.data.data
      console.log('este el record'+this.record)
    } catch (e) {
      console.log(e)
    } finally {
        this.loading = false
    }
  },
  async getObservation(id) {
    try {
      this.loading = true
      let res = await axios.get(`loan/${id}/observation`)
      this.observation = res.data
      console.log('este la observacion'+this.observation)
    } catch (e) {
      console.log(e)
    } finally {
      this.loading = false
    }
  },
  async saveObservation() {
    try {
      let res = await axios.post(`loan/${1}/observation`, {
      observation_type_id:2,
      message:"observacion adicionada"
    });
      console.log('se grabo el mensaje')
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
  }
  }
</script>