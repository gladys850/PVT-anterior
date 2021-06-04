<template>
  <v-form>
    <ValidationProvider v-slot="{ errors }" vid="first_name" name="Nombres" rules="required|alpha_spaces|min:3">
      <v-text-field
        :error-messages="errors"
        v-model="user.first_name"
        label="Nombres"
        clearable
        persistent-hint
        hint="Ej.: JUAN PABLO"
      ></v-text-field>
    </ValidationProvider>
    <ValidationProvider v-slot="{ errors }" vid="last_name" name="Apellidos" rules="required|alpha_spaces|min:3">
      <v-text-field
        :error-messages="errors"
        v-model="user.last_name"
        label="Apellidos"
        clearable
        persistent-hint
        hint="Ej.: PÉREZ PINTO"
      ></v-text-field>
    </ValidationProvider>
    <ValidationProvider v-slot="{ errors }" vid="position" name="Cargo" rules="required|min:3">
      <v-text-field
        :error-messages="errors"
        v-model="user.position"
        label="Cargo"
        clearable
        persistent-hint
        hint="Ej.: RESPONSABLE DEL ÁREA DE RECEPCIÓN"
      ></v-text-field>
    </ValidationProvider>
    <ValidationProvider v-slot="{ errors }" vid="username" name="Usuario" rules="required|alpha_num|min:3">
      <v-text-field
        :error-messages="errors"
        v-model="user.username"
        label="Usuario"
        clearable
        persistent-hint
        hint="Ej.: jperez"
      ></v-text-field>
    </ValidationProvider>
    <ValidationProvider v-slot="{ errors }" vid="password" name="Contraseña" rules="required|min:5">
      <v-text-field
        :error-messages="errors"
        v-model="user.password"
        label="Contraseña"
        clearable
        persistent-hint
        hint="Ej.: xfkAS.$281-M"
        type="password"
      ></v-text-field>
    </ValidationProvider>
  </v-form>
</template>

<script>
export default {
  name: 'user-form',
  props: {
    bus: {
      type: Object,
      required: true
    }
  },
  data: () => ({
    user: {}
  }),
  watch: {
    user(val) {
      if (val) {
        this.$emit('input', val)
      }
    }
  }
}
</script>