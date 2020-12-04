export default class AmortizationLoan {
  constructor() {
    this.resource = `module/6/amortization_loan` 
  }

  async get(id = null, params = {}) {
    try {
      let res = await axios.get(id ? `${this.resource}/${id}` : this.resource)
      return res
    } catch (e) {
      return e
    }
  }
}