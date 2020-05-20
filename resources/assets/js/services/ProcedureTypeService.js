export default class ProcedureType {
  constructor() {
    this.resource = `procedure_type`
  }

  async get(id = null, params = {}) {
    try {
      let res = await axios.get(id ? `${this.resource}/${id}` : this.resource, {
        params: params
      })
      return res.data
    } catch (e) {
      return e
    }
  }
}