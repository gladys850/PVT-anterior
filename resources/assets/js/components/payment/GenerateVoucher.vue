<template>
  <v-container fluid>
      <v-form>
        <!--v-card-->
          <v-row justify="center">
            <v-col cols="12">
              <v-container class="py-0">
                <v-row>
                  <slot name="title"></slot>
                  <br />
                  <v-col cols="12" md="3">
                    <v-layout row wrap>
                      <v-flex xs12 class="px-2">
                        <fieldset class="pa-3">
                        </fieldset>
                      </v-flex>
                    </v-layout>
                  </v-col>
                  <v-col cols="12" md="4">
                    <v-card-text class="py-0">
                      <v-layout row wrap>
                        <v-flex xs12 class="px-2">
                          <fieldset class="pa-3">
                          </fieldset>
                        </v-flex>
                      </v-layout>
                    </v-card-text>
                  </v-col>
                  <v-col cols="12" md="4">
                    <v-card-text class="py-0">
                      <v-layout row wrap>
                        <v-flex xs12 class="px-2">
                          <fieldset class="pa-3">
                          </fieldset>
                        </v-flex>
                      </v-layout>
                    </v-card-text>
                  </v-col>
                  <v-col cols="12" md="1" class="ma-0 pa-0"></v-col>
                </v-row>
              </v-container>
            </v-col>
          </v-row>
        <!--/v-card-->
      </v-form>
  </v-container>
</template>
<script>
export default {
  name: "generate-voucher",
  methods: {
  }
}
</script>