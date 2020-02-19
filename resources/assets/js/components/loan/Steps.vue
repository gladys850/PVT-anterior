<template>
  <div>
    <v-stepper v-model="e1" >
      <v-stepper-header class=" !pa-0 ml-0" >
        <template>
          <v-stepper-step
            :key="`${1}-step`"
            :complete="e1 > 1"
            :step="1"
            editable
          >Datos Afiliado
          </v-stepper-step>
          <v-divider
            v-if="1 !== steps"
            :key="1"
          ></v-divider>
          <v-stepper-step
            :key="`${2}-step`"
            :complete="e1 > 2"
            :step="2"
            editable
          >Datos Prestamo
          </v-stepper-step>
          <v-divider
            v-if="2 !== steps"
            :key="2"
          ></v-divider>
          <v-stepper-step
            :key="`${3}-step`"
            :complete="e1 > 3"
            :step="3"
            editable
          >Calculo Boletas
          </v-stepper-step>
          <v-divider
            v-if="3 !== steps"
            :key="3"
          ></v-divider>
          <v-stepper-step
            :key="`${4}-step`"
            :complete="e1 > 4"
            :step="4"
            editable
          >Resultado Calculo
          </v-stepper-step>
          <v-divider
            v-if="4 !== steps"
            :key="4"
          ></v-divider>
          <v-stepper-step
            :key="`${5}-step`"
            :complete="e1 > 5"
            :step="5"
            editable
          >Requisitos
          </v-stepper-step>
          <v-divider
            v-if="5 !== steps"
            :key="5"
          ></v-divider>
        </template>
      </v-stepper-header>
      <v-stepper-items>
        <v-stepper-content
          :key="`${1}-content`"
          :step="1"
        >
          <v-card color="grey lighten-1">
            <PersonalInformation
              :affiliate.sync="affiliate"
              :addresses.sync="addresses"
            />
              <v-container class="py-0">
                <v-row>
                  <v-spacer></v-spacer>
                  <v-spacer></v-spacer>
                  <v-spacer></v-spacer>
                    <v-col class="py-0">
                      <v-btn text>Cancelar</v-btn>
                      <v-btn
                        color="primary"
                        @click="nextStep(1)">
                        Continue
                      </v-btn>
                    </v-col>
                  </v-row>
                </v-container>
          </v-card>
        </v-stepper-content>
        <v-stepper-content
          :key="`${2}-content`"
          :step="2"
        >
          <v-card color="grey lighten-1">
            <LoanInformation
            :modalities.sync="modalities"
            />
            <v-container class="py-0">
              <v-row>
                <v-spacer></v-spacer>
                <v-spacer></v-spacer>
                <v-spacer></v-spacer>
                <v-col class="py-0">
                  <v-btn text
                  @click="beforeStep(2)">Atras</v-btn>
                  <v-btn right
                    color="primary"
                    @click="nextStep(2)">
                    Continue
                  </v-btn>
                </v-col>
              </v-row>
            </v-container>
          </v-card>
        </v-stepper-content>
        <v-stepper-content
          :key="`${3}-content`"
          :step="3"
        >
          <v-card color="grey lighten-1">
            <Ballots/>
            <v-container class="py-0">
              <v-row>
                <v-spacer></v-spacer>
                <v-spacer></v-spacer>
                <v-spacer></v-spacer>
                <v-col class="py-0">
                  <v-btn text
                  @click="beforeStep(3)">Atras</v-btn>
                  <v-btn right
                    color="primary"
                    @click="nextStep(3)">
                    Continue
                  </v-btn>
                </v-col>
              </v-row>
            </v-container>
          </v-card>
        </v-stepper-content>
        <v-stepper-content
          :key="`${4}-content`"
          :step="4"
          >
          <v-card color="grey lighten-1">
            <BallotsResult/>
            <v-container class="py-0">
              <v-row>
                <v-spacer></v-spacer>
                <v-spacer></v-spacer>
                <v-spacer></v-spacer>
                <v-col class="py-0">
                  <v-btn text
                    @click="beforeStep(4)">Atras</v-btn>
                  <v-btn
                    color="primary"
                    @click="nextStep(4)">
                    Continue
                  </v-btn>
                </v-col>
              </v-row>
            </v-container>
          </v-card>
        </v-stepper-content>
        <v-stepper-content
          :key="`${5}-content`"
          :step="5"
          >
          <v-card color="grey lighten-1">
            <Requirement/>
            <v-container class="py-0">
              <v-row>
                <v-spacer></v-spacer>
                <v-spacer></v-spacer>
                <v-spacer></v-spacer>
                <v-col class="py-0">
                  <v-btn text
                    @click="beforeStep(5)">Atras</v-btn>
                  <v-btn
                    color="primary"
                    @click="nextStep(5)">
                    Continue
                  </v-btn>
                </v-col>
              </v-row>
            </v-container>
          </v-card>
        </v-stepper-content>
      </v-stepper-items>
    </v-stepper>
  </div>
</template>
<style >
.v-expansion-panel-content__wrap {
    padding: 0 0px 0px;
}
.v-stepper__content {
    padding: 0px 0px 0px;
}
</style>
<script>
import Ballots from '@/components/loan/Ballots'
import Requirement from '@/components/loan/Requirement'
import BallotsResult from '@/components/loan/BallotsResult'
import PersonalInformation from '@/components/affiliate/PersonalInformation'
import LoanInformation from '@/components/loan/LoanInformation'
import { Validator } from 'vee-validate'
export default {
  inject: ['$validator'],
  name: "loan-steps",
  props: {
    affiliate: {
      type: Object,
      required: true
    },
    addresses: {
      type: Array,
      required: true
    },
  },
  components: {
    Requirement,
    Ballots,
    PersonalInformation,
    LoanInformation,
    BallotsResult
  },
  data () {
    return {
      e1: 1,
      steps: 5,
      modalities: [],
    }
  },
  computed: {
    isNew() {
      return this.$route.params.hash == 'new'
    }
  },
  watch: {
    steps (val) {
      if (this.e1 > val) {
        this.e1 = val
      }
    },
  },
  beforeMount(){
    this.getProcedureType();
  },
  methods: {
    nextStep (n) {
      if (n === this.steps) {
        this.e1 = 1
      } else {
        this.e1 = n + 1
      }
    },
    beforeStep (n) {
      this.e1 = n -1
    },
    async getProcedureType() {
      try {
        let resp = await axios.get(`module`,{
          params: {
            name: 'prestamos',
            sortBy: ['name'],
            sortDesc: ['false'],
            per_page: 10,
            page: 1
            }
        })
        this.modulo= resp.data.data[0].id
        let res = await axios.get(`module/${this.modulo}/procedure_type`)
        this.modalities = res.data
      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    },

  },
}
</script>