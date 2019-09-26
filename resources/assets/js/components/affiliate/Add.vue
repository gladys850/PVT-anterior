<template>
  <v-container fluid >
    <v-form>
      <v-row justify="center">
        <v-col cols="12" md="7" >
          <v-toolbar-title>NUEVO AFILIADO</v-toolbar-title>
              <v-container class="py-0">
                <v-row>
                    <v-col cols="12" md="6" >
                      <v-text-field
                        class="purple-input"
                        label="Primer Nombre"
                        v-validate.initial="'required|min:1|max:250'"
                        :error-messages="errors.collect('primer nombre')"
                        data-vv-name="primer nombre"
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12" md="6" >
                      <v-text-field
                        label="Segundo Nombre"
                        class="purple-input"
                        data-vv-name="segundo nombre"
                      /></v-text-field>
                    </v-col>
                    <v-col cols="12" md="6" >
                      <v-text-field
                        label="Primer Apellido"
                        class="purple-input"
                        v-validate.initial="'required|min:1|max:250'"
                        :error-messages="errors.collect('primer apellido')"
                        data-vv-name="primer apellido"
                      /></v-text-field>
                    </v-col>
                    <v-col cols="12" md="6" >
                      <v-text-field
                        label="Segundo Apellido"
                        class="purple-input"
                        v-validate.initial="'required|min:1|max:250'"
                        :error-messages="errors.collect('segundo apellido')"
                        data-vv-name="segundo apellido"
                      /></v-text-field>
                    </v-col>
                    <v-col cols="12" md="4" >
                      <v-text-field
                        class="purple-input"
                        label="Cedula de Identidad"
                        v-validate.initial="'required|numeric|min:1|max:50'"
                        :error-messages="errors.collect('cedula identidad')"
                        data-vv-name="cedula identidad"
                      /></v-text-field>
                    </v-col>
                    <v-col cols="12" md="4" >
                      <v-select
                        name="lugar"
                        :items="Lugar"
                        label="Lugar Expedido"
                        v-model="expedidoTypeSelected"
                      ></v-select>
                    </v-col>
                    <v-col cols="12" md="4">
                      <v-menu
                        ref="menu"
                        v-model="menu"
                        :close-on-content-click="false"
                        :return-value.sync="dateVencimiento"
                        transition="scale-transition"
                        offset-y
                        min-width="290px"
                      >
                        <template v-slot:activator="{ on }">
                          <v-text-field
                            v-model="dateVencimiento"
                            label="Fecha Vencimiento CI"
                            prepend-icon=""
                            readonly
                            v-on="on"
                          ></v-text-field>
                        </template>
                        <v-date-picker v-model="dateVencimiento" no-title scrollable>
                          <div class="flex-grow-1"></div>
                          <v-btn text color="primary" @click="menu = false">Cancel</v-btn>
                          <v-btn text color="primary" @click="$refs.menu.save(dateVencimiento)">OK</v-btn>
                        </v-date-picker>
                      </v-menu>
                    </v-col>
                    <v-col cols="12" md="4" >
                      <v-menu
                        ref="menu1"
                        v-model="menu1"
                        :close-on-content-click="false"
                        :return-value.sync="dateNacimiento"
                        transition="scale-transition"
                        offset-y
                        min-width="290px"
                      >
                        <template v-slot:activator="{ on }">
                          <v-text-field
                            v-model="dateNacimiento"
                            label="Fecha Nacimiento"
                            prepend-icon=""
                            readonly
                            v-on="on"
                          ></v-text-field>
                        </template>
                        <v-date-picker v-model="dateNacimiento" no-title scrollable>
                          <div class="flex-grow-1"></div>
                          <v-btn text color="primary" @click="menu1 = false">Cancel</v-btn>
                          <v-btn text color="primary" @click="$refs.menu1.save(dateNacimiento)">OK</v-btn>
                        </v-date-picker>
                      </v-menu>
                    </v-col>
                    <v-col cols="12" md="4" >
                      <v-select
                        :items="Nacimiento"
                        name="nacimiento"
                        label="Lugar de Nacimiento"
                        v-model="nacimientoTypeSelected"
                      ></v-select>
                    </v-col>
                    <v-col cols="12" md="4" >
                      <v-select
                        :items="Civil"
                        label="Estado Civil"
                        v-model="civilTypeSelected"
                      ></v-select>
                    </v-col>
                    <v-col cols="12" md="6">
                       <v-menu
                        ref="menu2"
                        v-model="menu2"
                        :close-on-content-click="false"
                        :return-value.sync="dateFallesimiento"
                        transition="scale-transition"
                        offset-y
                        min-width="290px"
                      >
                        <template v-slot:activator="{ on }">
                          <v-text-field
                            v-model="dateFallesimiento"
                            label="Fecha Nacimiento"
                            prepend-icon=""
                            readonly
                            v-on="on"
                          ></v-text-field>
                        </template>
                        <v-date-picker v-model="dateFallesimiento" no-title scrollable>
                          <div class="flex-grow-1"></div>
                          <v-btn text color="primary" @click="menu2 = false">Cancel</v-btn>
                          <v-btn text color="primary" @click="$refs.menu2.save(dateFallesimiento)">OK</v-btn>
                        </v-date-picker>
                      </v-menu>
                    </v-col>
                    <v-col cols="12" md="6">
                      <v-text-field
                        label="Causa Fallecimiento"
                        class="purple-input"
                      ></v-text-field>
                    </v-col>
                  <v-col cols="12" md="6" >
                    <v-text-field
                      label="Celular"
                      class="purple-input"
                      v-validate.initial="'required|numeric|min:1|max:20'"
                      :error-messages="errors.collect('celular')"
                      data-vv-name="celular"
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12" md="6" >
                    <v-text-field
                      label="Telefono"
                      class="purple-input"
                      v-validate.initial="'required|numeric|min:1|max:20'"
                      :error-messages="errors.collect('telefono')"
                      data-vv-name="telefono"
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12">
                    <v-toolbar-title>DIRECCION DOMICILARIA</v-toolbar-title>
                  </v-col>
                  <v-col cols="12" md="6" >
                    <v-select
                      :items="City"
                      name="ciudad"
                      label="Ciudad"
                      v-model="cityTypeSelected"
                    ></v-select>
                  </v-col>               
                  <v-col cols="12" md="6">
                    <v-text-field
                      label="Zona"
                      class="purple-input"
                      v-validate.initial="'required|min:1|max:250'"
                      :error-messages="errors.collect('zona')"
                      data-vv-name="zona"
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12" md="6">
                    <v-text-field
                      label="Calle"
                      class="purple-input"
                      v-validate.initial="'required|min:1|max:250'"
                      :error-messages="errors.collect('calle')"
                      data-vv-name="calle"
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12" md="6" >
                    <v-text-field
                      label="Nro"
                      class="purple-input"
                      v-validate.initial="'required|numeric|min:1|max:10000'"
                      :error-messages="errors.collect('nro')"
                      data-vv-name="nro"
                    ></v-text-field>
                  </v-col>
                </v-row>
              </v-container>
        </v-col>
        <v-col cols="12" md="5" class="v-card-profile" >
            <v-avatar
              slot="offset"
              class="mx-auto d-block elevation-10"
              size="170"
            >
              <img  src="https://demos.creative-tim.com/vue-material-dashboard/img/marc.aba54d65.jpg" >
            </v-avatar>
            <v-card-text class="text-center">       
              <v-btn color="primary">
                Adicionar Foto
              </v-btn>
              <v-btn color="primary" @click.stop="captureFingerprint = true" v-if="affiliate.hasOwnProperty('id')">
                <v-icon left>mdi-fingerprint</v-icon>
                Capturar huella
              </v-btn>
              <v-btn color="primary">
                Informacion Conyugue
              </v-btn>
            </v-card-text>
              <v-row justify="center">
              <v-col cols="12">
                <v-toolbar-title>INFORMACION POLICIAL</v-toolbar-title>
              </v-col>
            <v-col cols="12" md="7" >
              <v-select
                :items="Estado"
                label="Estado"
                v-model="estadoTypeSelected"
              ></v-select>
            </v-col>
            <v-col cols="12" md="5" >
              <v-menu
                ref="menu3"
                v-model="menu3"
                :close-on-content-click="false"
                :return-value.sync="dateIngreso"
                transition="scale-transition"
                offset-y
                min-width="290px"
              > 
                <template v-slot:activator="{ on }">
                  <v-text-field
                    v-model="dateIngreso"
                    label="Fecha Ingreso a la Institucion Policial"
                    prepend-icon=""
                    readonly
                    v-on="on"
                  ></v-text-field>
                </template>
                <v-date-picker v-model="dateIngreso" no-title scrollable>
                  <div class="flex-grow-1"></div>
                  <v-btn text color="primary" @click="menu3 = false">Cancel</v-btn>
                  <v-btn text color="primary" @click="$refs.menu3.save(dateIngreso)">OK</v-btn>
                </v-date-picker>
              </v-menu>
            </v-col>
            <v-col cols="12" md="6" >
              <v-select
                :items="Categoria"
                label="Categoria"
                v-model="categoriaTypeSelected"
              ></v-select>
            </v-col>
            <v-col cols="12" md="6">
              <v-text-field
                label="Años de Servicio"
                class="purple-input"
                v-validate.initial="'required|numeric|min:1|max:100'"
                :error-messages="errors.collect('nro')"
                data-vv-name="nro"
              ></v-text-field>
            </v-col>
            <v-col cols="12" md="6" >
              <v-text-field
                label="Meses de Servicio"
                class="purple-input"
                v-validate.initial="'required|numeric|min:1|max:100'"
                :error-messages="errors.collect('nro')"
                data-vv-name="nro"
              ></v-text-field>
            </v-col>
            <v-col cols="12" md="6" >
              <v-select
                :items="Grado"
                label="Grado"
                v-model="gradoTypeSelected"
                ></v-select>
              </v-col>
            <v-col cols="12" >
              <v-select
                :items="Gestor"
                label="Ente Gestor"
                v-model="gestorTypeSelected"
              ></v-select>
            </v-col>
            <v-col cols="12">
              <v-menu
                ref="menu4"
                v-model="menu4"
                :close-on-content-click="false"
                :return-value.sync="dateDesvinculacion"
                transition="scale-transition"
                offset-y
                min-width="290px"
              >
                <template v-slot:activator="{ on }">
                  <v-text-field
                    v-model="dateDesvinculacion"
                    label="Fecha Desvinculacion"
                    prepend-icon=""
                    readonly
                    v-on="on"
                  ></v-text-field>
                </template>
                <v-date-picker v-model="dateDesvinculacion" no-title scrollable>
                  <div class="flex-grow-1"></div>
                  <v-btn text color="primary" @click="menu4 = false">Cancel</v-btn>
                  <v-btn text color="primary" @click="$refs.menu4.save(dateDesvinculacion)">OK</v-btn>
                </v-date-picker>
              </v-menu>
              </v-col> 
              <v-col cols="12" md="6" class="text-center" >
                <v-btn color="success">
                  Guardar
                </v-btn>
              <v-btn
          color="warning"
         
          :to="{name:'affiliateIndex'}"
        >
          Atras
        </v-btn>
              </v-col>    
            </v-row>
        </v-col>
      </v-row>
    </v-form>
    <v-dialog
      v-model="captureFingerprint"
      persistent
      width="400"
    >
      <v-card
        color="primary"
        class="py-3"
        dark
      >
        <v-card-text>
          <div class="subtitle-1 font-weight-light">Continue el proceso en el equipo biométrico ...</div>
          <v-progress-linear
            indeterminate
            color="white"
            class="mt-4"
          ></v-progress-linear>
        </v-card-text>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script>
import List from '@/components/affiliate/List'

export default {
  data: () => ({
    captureFingerprint: false,
      Lugar: [
      'La Paz',
      'Oruro',
      'Potosi',
      'Santa Cruz'
    ],
     Nacimiento: [
      'La Paz',
      'Oruro',
      'Potosi',
      'Santa Cruz'
    ],
    City: [
      'La Paz',
      'Oruro',
      'Potosi',
      'Santa Cruz'
    ],
    Genero: [
      'Femenino',
      'Masculino'
    ],
    Civil: [
      'Soltero',
      'Casado',
      'Viudo',
    ],
     Estado: [
      'Jubilado',
      'Activo',
    ],
    Categoria: [
      '100%',
      '90%',
      '80%',
      '70%',
      '60%',
      '50%',
      '40%',
    ],
     Grado: [
      'Teniente Coronel',
      'Teniente',
      'Coronel',
    ],
     Gestor: [
      'Afp Futuro',
      'Afp Provision',
      'Senasir',
    ],
    gradoTypeSelected: null,
    gestorTypeSelected: null,
    categoriaTypeSelected: null,
    estadoTypeSelected: null,
    civilTypeSelected: null,
    cityTypeSelected: null,
    expedidoTypeSelected: null,
    nacimientoTypeSelected: null,
    lugarTypeSelected: null,
    generoSelected: null,
      date: new Date().toISOString().substr(0, 10),
      dateVencimiento: new Date().toISOString().substr(0, 10),
      dateNacimiento: new Date().toISOString().substr(0, 10),
      dateFallesimiento: new Date().toISOString().substr(0, 10),
      dateIngreso: new Date().toISOString().substr(0, 10),
      dateDesvinculacion: new Date().toISOString().substr(0, 10),
        menu: false,
        menu1: false,
        modal: false,
        menu2: false,
        menu3: false,
        menu4: false,
  }),
  components: {
    List
  },
  watch: {
    search: _.debounce(function () {
      this.bus.$emit('search', this.search)
    }, 1000),
  },
  beforeMount() {
    Echo.channel('fingerprint').listen('.saved', (data) => {
      this.captureFingerprint = false
    })
  }
  }
</script>

