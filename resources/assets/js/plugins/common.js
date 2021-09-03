export default {
  randomColor: function() {
    let colors = ['red', 'pink', 'purple', 'blue', 'light-blue', 'cyan', 'teal', 'green', 'light-green',  'amber', 'orange', 'deep-orange', 'deep-orange']
    return colors[Math.floor(Math.random() * colors.length)]
  },
  cleanSpace(text){
    if(text != null){
      if(text.trim() != '')
        return text
      else
        return null
    }else{
      return null
    }
  }
}