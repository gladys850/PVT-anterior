<template>
  <v-container fluid>
    <v-card>
    <v-data-iterator
      :items="items"
      hide-default-footer
    >
      <template v-slot:header>
        <v-toolbar
          class="mb-0"
          color="ternary"
          dark
          flat
        >
          <v-toolbar-title> REQUISITOS PARA CORTO PLAZO PASIVO  {{selected}}{{selected1}}</v-toolbar-title>
        </v-toolbar>
      </template>
      <template >
        <v-row>
          <v-col
            v-for="(group,i) in items" :key="i"
            cols="11"
            class="py-1"
          >
            <v-card >
              <v-col  cols="12"  class="py-0" v-for="(doc,j) in group" :key="doc.id">
                <v-list dense class="py-0">
                  <v-list-item class="py-0" >
                    <v-col cols="1" class="py-0" v-if="j==0">
                      <v-list-item-content  class="align-end font-weight-light"><h1>{{i+1}}</h1></v-list-item-content>
                    </v-col>
                    <v-col cols="1" class="py-0" v-if="j>0">
                    </v-col>
                    <v-col cols="9" class="py-0">
                      <v-list-item-content  class="align-end font-weight-light" >{{doc.name}}</v-list-item-content>
                      </v-col>

<v-col cols="1" class="py-0" >
  <v-radio-group  :mandatory="false">
    <v-radio    color="info" v-model="radios" :value="j"   @change="selectDoc1(doc.id,j,i)" ></v-radio>
  </v-radio-group>
</v-col>
<v-col cols="1"  class="py-0">
  <v-checkbox    color="info" v-model="selected" :value="doc.id"   @change="selectDoc1(doc.id,j,i)"></v-checkbox>
</v-col>
                  </v-list-item>


                </v-list >
              </v-col>

            </v-card>
          </v-col>
        </v-row>
      </template>
    </v-data-iterator>

      <v-toolbar-title class="align-end font-weight-black text-center "><h3>Documentos Opcionales</h3></v-toolbar-title>
                 <v-divider></v-divider>
    <v-data-iterator
      :items="optional"
      hide-default-footer
    >
      <template v-slot:default="props">
        <v-row>
          <v-col
            v-for="(item,index) in props.items"
            :key="index"
            cols="11"
            class="py-1"

          >
            <v-card>
              <v-list dense class="py-0">
                <v-list-item class="py-0">
<v-col  cols="1" class="py-0">
                <v-list-item-content  class="align-end font-weight-light"><h1>{{index+1}}</h1></v-list-item-content>
</v-col>
<v-col cols="10" class="py-0">
                  <v-list-item-content  class="align-end font-weight-light">{{item['name']}}</v-list-item-content>
 </v-col>
<v-col cols="1" class="py-0">
                  <v-checkbox
                        color="info"
                        v-model="selected1" :value="item['id']"
                      ></v-checkbox>
       </v-col>
                </v-list-item>
              </v-list>
            </v-card>
          </v-col>
        </v-row>
      </template>
    </v-data-iterator>
 
   </v-card>
  </v-container>
</template>
<script>
import { Validator } from 'vee-validate'
export default {
inject: ['$validator'],
name: "loan-requirement",
data: () => ({

  itemsPerPage:10,
  items:[],
  optional:[],
  requirement:[],
  index:[],
  prueba:null,
  cont:0,

checks:[],
selected1:[],

  selected: [],

  radios: '1',
}),
beforeMount(){
this.getRequirement(9)
},
methods:{

    selectDoc1(id,j,i) {

    console.log(id+'este es id'+'=> este j' + j+'este es i'+i+'por fin entro')

    console.log(this.selected+'=>este es el vector')
  },

   async getRequirement(id) {
      try {
        this.loading = true
        let res = await axios.get(`procedure_modality/${id}/requirements`)
        this.requirement = res.data
        this.items=this.requirement.required
        this.optional=this.requirement.optional
      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    },
}
};
</script>