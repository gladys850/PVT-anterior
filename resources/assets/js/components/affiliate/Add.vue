<template>
  <v-container fluid>
  <div justify="space-around">
    <v-tabs
      v-model="tab"
      background-color="deep-blue accent-2"
      class="elevation-2"
      dark
      :vertical="vertical"
      :icons-and-text="icons"
    >
      <v-tabs-slider></v-tabs-slider>

      <v-tab
        :href="`#tab-1`"
      >
        <v-icon v-if="icons">mdi-trending-up</v-icon>
      </v-tab>
      <v-tab
        :href="`#tab-2`"
      >
        <v-icon v-if="icons">mdi-account</v-icon>
      </v-tab>
       <v-tab
        :href="`#tab-3`"
      >
        <v-icon v-if="icons">mdi-police-badge</v-icon>
      </v-tab>
        <v-tab
        :href="`#tab-4`"
      >
        <v-icon v-if="icons">mdi-account-heart</v-icon>
      </v-tab>
          <v-tab
        :href="`#tab-5`"
      >
        <v-icon v-if="icons">mdi-fingerprint</v-icon>
      </v-tab>

      <v-tab-item
        :value="'tab-1'"
      >
        <v-card flat tile >
          <v-card-text>{{ text }}</v-card-text>
        </v-card>
      </v-tab-item>
      <v-tab-item
        :value="'tab-2'"
      >
        <v-card flat tile >
          <v-card-text><Profile/></v-card-text>
        </v-card>
      </v-tab-item>
        <v-tab-item
        :value="'tab-3'"
      >
        <v-card flat tile >
          <v-card-text>{{ text2 }}</v-card-text>
        </v-card>
      </v-tab-item>
        <v-tab-item
        :value="'tab-4'"
      >
        <v-card flat tile >
          <v-card-text>{{ text3 }}</v-card-text>
        </v-card>
      </v-tab-item>
        <v-tab-item
        :value="'tab-5'"
      >
        <v-card flat tile >
         <v-card-text> {{ text4 }} </v-card-text>
        </v-card>
      </v-tab-item>
    </v-tabs>
  </div>
  </v-container>
</template>
<script>
import Profile from './Profile'
  export default {
    name: "affiliate-index",
    components: {
        Profile
    },
    data () {
      return {
        tab: null,
        text: 'hola',
        text1: 'como ',
        text2: 'estas',
        text3: 'chau',
        text4: 'huella',
        icons: true,
        vertical: true,
        tabs: 10,
      }
    },
  }
</script>