<template>
  <div>
    <v-stepper v-model="e1">
      <v-stepper-header>
        <template>
          <v-stepper-step
            :key="`${1}-step`"
            :complete="e1 > 1"
            :step="1"
            editable
          >Datos Garante
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
          >Calculo Boletas
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
          >Requisitos
          </v-stepper-step>
          <v-divider
            v-if="3 !== steps"
            :key="3"
          ></v-divider>
        </template>
      </v-stepper-header>
      <v-stepper-items>
        <v-stepper-content
          :key="`${1}-content`"
          :step="1"
        >
          <v-card color="grey lighten-1">
            <Guarantor/>
            <v-container class="py-0">
              <v-row>
                <v-spacer></v-spacer>
                <v-spacer></v-spacer>
                <v-spacer></v-spacer>
                <v-col class="py-0">
                  <v-btn text>Cancel</v-btn>
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
            <Ballots/>
            <v-container class="py-0">
              <v-row>
                <v-spacer></v-spacer>
                <v-spacer></v-spacer>
                <v-spacer></v-spacer>
                <v-col class="py-0">
                  <v-btn text>Cancel</v-btn>
                  <v-btn
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
          <v-card
            class="mb-2"
            color="grey lighten-1"
          >
            <Requirement/>
          </v-card>
          <v-container class="py-0">
            <v-row>
              <v-spacer></v-spacer>
              <v-spacer></v-spacer>
              <v-spacer></v-spacer>
              <v-col class="py-0">
                <v-btn text>Cancel</v-btn>
                <v-btn right
                  color="primary"
                  @click="nextStep(3)">
                  Continue
                </v-btn>
              </v-col>
            </v-row>
          </v-container>
        </v-stepper-content>
      </v-stepper-items>
    </v-stepper>
  </div>
</template>

<script>
import Guarantor from '@/components/loan/Guarantor'
import Ballots from '@/components/loan/Ballots'
import Requirement from '@/components/loan/Requirement'
export default {
  name: "loan-steps",
  components: {
    Requirement,
    Ballots,
    Guarantor
  },
  data () {
    return {
      e1: 1,
      steps: 4,

    reload: false,
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
  methods: {
    nextStep (n) {
      if (n === this.steps) {
        this.e1 = 1
      } else {
        this.e1 = n + 1
      }
    },
  },
}
</script>