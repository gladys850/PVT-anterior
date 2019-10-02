<template>
  <div style="margin-right: -20px;" class="mt-3">
    <v-speed-dial
      v-model="fab"
      direction="left"
      transition="slide-x-reverse-transition"
      top
      right
    >
      <template v-slot:activator>
        <v-btn
          v-model="fab"
          color="success"
          dark
          fab
          small
        >
          <v-icon v-if="fab">mdi-close</v-icon>
          <v-icon v-else>mdi-account-circle</v-icon>
        </v-btn>
      </template>
      <Add :bus="bus" @closeFab="fab = false"/>
      <Sync :bus="bus" @closeFab="fab = false"/>
    </v-speed-dial>
  </div>
</template>

<script>
import Add from '@/components/user/Add'
import Sync from '@/components/user/Sync'

export default {
  name: 'user-fab',
  components: {
    Add,
    Sync
  },
  props: {
    bus: {
      type: Object,
      required: true
    }
  },
  data: () => ({
    fab: false
  })
}
</script>