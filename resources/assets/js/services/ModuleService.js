export default class Module {
  constructor() {
    this.resource = `module`
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