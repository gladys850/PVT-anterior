<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>

<body>
    <form id="form1" name="form1" method="post" action="">
    <label> Vives con tu Familia<br />
    </label>
    <p>
    <label>
    <input type="checkbox" name="RadioGroup1" value="1" id="si" onclick="Si()"/>
    si</label>
    <br />
    <label>
    <input type="checkbox" name="RadioGroup1" value="2" id="no" onclick="No()"/>
    no</label>
    <br />
    <br />
    </p>
    <label></label>

    </p>
    </form>

    <div class="row">
    <div class="col-lg-5">
        <input type="checkbox" name="tipoGrado"  id="checkGrado" value="1"  class="only-one">Saldos por Grado
    </div>
    <div class="col-lg-5">
        <input type="checkbox" name="tipoResumen"  id="checkResumen" value="2"  class="only-one"> Resumen de Saldos
    </div>
</div>



<script>
var si=document.getElementById ( "si" )
var no=document.getElementById ( "no" )

function Si()
{
    no.checked=false
}
function No()
{
    si.checked=false
}

var myArray = [[12, 2],[3, 4, 5], [6], [7, 8, 9,0]];


for (var i = 0; i < myArray.length; ++i) {
    console.log("<br> tamano i= "+myArray[i].length);
    if(myArray[i].length>1){
        for (var j = 0; j < myArray[i].length; ++j){
        console.log("<br>tiene varios elementos ji= "+ myArray[j,i]);
    }
    }else{
        for (var j = 0; j < myArray[i].length; ++j){
            console.log("<br>tiene un elemento ji= "+ myArray[j,i]);
    }

    }

}

let Checked = null;
//The class name can vary
for (let CheckBox of document.getElementsByClassName('only-one')){
	CheckBox.onclick = function(){
  	if(Checked!=null){
      Checked.checked = false;
      Checked = CheckBox;
    }
    Checked = CheckBox;
  }
}

//console.log(myNewArray);
// [1, 2, 3, 4, 5, 6, 7, 8, 9]


var arreglo = [
    {dato: 'asdasd'},
    {dato:'234234'}
];

/**
 * Array.prototype.map(fn: callback) es un método que se ejecutará por
 * cada elemento dentro del arreglo sobre el cúal fue llamado.
 *
 * @see {@link https://developer.mozilla.org/es/docs/Web/JavaScript/Referencia/Objetos_globales/Array/map}
 * @param {Object} o
 */
var nuevoArreglo = arreglo.map(function(o) {
    /**
     * Array.prototype.reduce(fn: callback, value: any) es un método que pasa un valor inicial
     * a cada iteración, el valor devuelto será pasado por cada iteración y devuelto como
     * resultado al final del bucle.
     *
     * @see {@link https://developer.mozilla.org/es/docs/Web/JavaScript/Referencia/Objetos_globales/Array/reduce}
     * @param {Array} array  Con un valor inicial [].
     * @param {string} key   Llave del objeto.
     */
    return Object.keys(o).reduce(function(array, key) {
        return array.concat([key, o[key]]);
    }, []);
});

console.log(nuevoArreglo);

</script>


</body>
</html>

