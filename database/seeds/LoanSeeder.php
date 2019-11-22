<?php

use Illuminate\Database\Seeder;
use App\ProcedureModality;
use App\ProcedureType;
use App\ProcedureDocument;
use App\ProcedureRequirement;
use App\Module;
class LoanSeeder extends Seeder
{
    /**
     * Run the database seeds. 
     *
     * @return void
     */
    public function run()
    {
      $module = Module::whereName('prestamos')->first();
      
      $data = [
        'procedures' => [
          'anticipo' => [
            'type' => ['module_id' => $module->id,'name'=>'Préstamo Anticipo','second_name'=>'Préstamo Anticipo'],
            'modalities' => [
                // ANTICIPO SECTOR ACTIVO 
                ['name'=>'Anticipo sector activo','shortened'=>'ANT-SA','requirements'=>[
                  ['name' => 'Cédula de Identidad del (la) titular en copia simple.','number'=>1],
                  ['name' => 'Boleta de pago en copia simple.','number'=>2],  
                  ['name' => 'Certificado de haberes considerando el último mes percibido.','number'=>2],  // En caso de no contar con la boleta de pago      
                  ['name' => 'Estado de cuenta original, vigente y emitido por el Banco Unión S.A.','number'=>3], // Nuevo
                  /*Estos documentos son solicitados por MUSERPOL en caso de ser requeridos*/
                  ['name' => 'Solicitud de aclaración de datos personales en la Boleta de Pago.','number'=>0],  // En caso de que el afiliado tenga datos erroneos en su boleta de pago
                  ['name' => 'Certificado de no adeudo, emitido por la instancia correspondiente.','number'=>0],  // en caso de que el afiliado haya tenido deudas de otras entidades (Ej: COMIPOL , COVIPOL)
                  ['name' => 'Certificado de pago emitido por la entidad correspondiente.','number'=>0],  // en caso de que el afiliado tenga deudas con otras entidades (Ej: COMIPOL , COVIPOL)
                ]],
                    // ANTICIPO SECTOR PASIVO Y VIUDAS
                ['name'=>'Anticipo sector pasivo','shortened'=>'ANT-SP','requirements'=>[
                  ['name' => 'Cédula de Identidad del (la) titular en copia simple.','number'=>1],
                  ['name' => 'Boleta de renta de jubilación en copia simple.','number'=>2],// Nuevo
                  ['name' => 'Ultima boleta de pago con renta en SENASIR original','number'=>2], //Nuevo *caso pasivo con renta de SENASIR, quien solicita el préstamo
                  ['name' => 'Ultima boleta de pago con renta en AFP original','number'=>2],      //Nuevo *caso pasivo con renta en AFP , quien solicita el préstamo
                  ['name' => 'Certificado de haberes con siderando el último mes percibido.','number'=>2],  // En caso de no contar con la boleta de pago
                  ['name' => 'Estado de cuenta original, vigente y emitido por el Banco Unión S.A.','number'=>3], // Nuevo
                  /*Estos documentos son solicitados por MUSERPOL en caso de ser requeridos*/
                  ['name' =>'Solicitud de aclaración de datos personales en la Boleta de Pago.','number'=>0],  // En caso de que el afiliado tenga datos erroneos en su boleta de pago  
                  ['name' => 'Certificado de no adeudo, emitido por la instancia correspondiente.','number'=>0],  // en caso de que el afiliado haya tenido deudas de otras entidades (Ej: COMIPOL , COVIPOL)
                  ['name' => 'certificado de pago emitido por la entidad correspondiente.','number'=>0],  // en caso de que el afiliado tenga deudas con otras entidades (Ej: COMIPOL , COVIPOL) 
                  ['name' => 'Certificado de aportes para el Auxilio Mortuorio','number'=>0],  // *caso pasivo con renta en AFP , quien solicita el préstamo
                  ['name' => 'Memorándum de agradecimiento de servicios en copia simple emitido por el Comando General de la Policía Boliviana.o','number'=>0],  // *caso pasivo con renta en AFP , quien solicita el préstamo

                ]],
            ]
          ],
          'corto' => [
            // CORTO PLAZO
            'type' => ['module_id' => $module->id,'name'=>'Préstamo a corto plazo','second_name'=>'Préstamo a corto plazo'],
            'modalities' => [
              // CORTO PLAZO SECTOR ACTIVO SERVICIO
              ['name'=>'Corto plazo sector activo','shortened'=>'PCP-SA','requirements'=>[
              	['name' => 'Cédula de Identidad del (la) titular en copia simple.','number'=>1],
                ['name' => 'Boleta de pago en copia simple.','number'=>2], //(pueden ser dos opciones 1 o 3 boletas) (Se debe hallar el promedio de las tres ultimas boletas de pago)
                ['name' => 'Certificado de haberes considerando el último mes percibido.','number'=>2],  // En caso de no contar con la boleta de pago
                ['name' => 'Estado de cuenta original, vigente y emitido por el Banco Unión S.A.','number'=>3], // Nuevo
                /*Estos documentos son solicitados por MUSERPOL en caso de ser requeridos*/
                ['name' => 'Solicitud de aclaración de datos personales en la Boleta de Pago.','number'=>0],  // En caso de que el afiliado tenga datos erroneos en su boleta de pago
                ['name' => 'Certificado de no adeudo, emitido por la instancia correspondiente.','number'=>0],  // en caso de que el afiliado haya tenido deudas de otras entidades (Ej: COMIPOL , COVIPOL)
                ['name' => 'Certificado de pago emitido por la entidad correspondiente.','number'=>0],  // en caso de que el afiliado tenga deudas con otras entidades (Ej: COMIPOL , COVIPOL)
              ]],
              // CORTO PLAZO SECTOR ACTIVO DISPONIBILIDAD LETRA 'A'
              ['name'=>'Corto plazo con disponibilidad de letra "A"','shortened'=>'PCP-DLA','requirements'=>[
                ['name' => 'Cédula de Identidad del (la) titular en copia simple.','number'=>1],                  
            	  ['name' => 'Memorándum de destino a disponibilidad a la letra "A" en copia simple.','number'=>2], // Nuevo
                ['name' => 'Boleta de pago en copia simple.','number'=>3], //(pueden ser dos opciones 1 o 3 boletas)
                ['name' => 'Certificado de haberes considerando el último mes percibido.','number'=>3],  // En caso de no contar con la boleta de pago
                ['name' => 'Estado de cuenta original, vigente y emitido por el Banco Unión S.A','number'=>4],                       
            		['name' => 'Certificado de años de servicio desglosado en copia simple emitido por el Comando General de la Policía Boliviana.','number'=>5], // puede ser certif. de calificación comando general
                ['name' => 'Certificado de calificación de años de servicio CAS emitido por el Ministerio de Economia y Finanzas Publicas','number'=>6],
                 /*Estos documentos son solicitados por MUSERPOL en caso de ser adicionales*/
                ['name' => 'Solicitud de aclaración de datos personales en la Boleta de Pago.','number'=>0],  // En caso de que el afiliado tenga datos erroneos en su boleta de pago
                ['name' => 'Certificado de no adeudo, emitido por la instancia correspondiente.','number'=>0],  // en caso de que el afiliado haya tenido deudas de otras entidades (Ej: COMIPOL , COVIPOL)
                ['name' => 'Certificado de pago emitido por la entidad correspondiente.','number'=>0,  // en caso de que el afiliado tenga deudas con otras entidades (Ej: COMIPOL , COVIPOL)
              ]],
              // CORTO PLAZO SECTOR PASIVO Y VIUDAS
              ['name'=>'Corto plazo el sector pasivo y viudas','shortened'=>'PCP-SP','requirements'=>[
                ['name' => 'Cédula de Identidad del (la) titular en copia simple.','number'=>1],
                ['name' => '3 Ultimas boletas de pago con renta en SENASIR original','number'=>2], //Nuevo *caso pasivo con renta de SENASIR, quien solicita el préstamo
                ['name' => '3 Ultimas boletas de pago con renta en AFP original','number'=>2],      //Nuevo *caso pasivo con renta en AFP , quien solicita el préstamo
                ['name' => 'Garantia solidaria.','number'=>3], //(consultar con prestamos)
                ['name' => 'Estado de cuenta original, vigente y emitido por el Banco Unión S.A.','number'=>4],
                  /*Estos documentos son solicitados por MUSERPOL en caso de ser requeridos*/
                ['name' => 'Certificado de no adeudo, emitido por la instancia correspondiente','number'=>0],  // en caso de que el afiliado haya tenido deudas de otras entidades (Ej: COMIPOL , COVIPOL)
                ['name' => 'Certificado de pago emitido por la entidad correspondiente.','number'=>0],  // en caso de que el afiliado tenga deudas con otras entidades (Ej: COMIPOL , COVIPOL)
                ['name' => 'Certificado de aportes para el Auxilio Mortuorio de los 3 últimos meses de la unidad de Fondo de Retiro.','number'=>0], // para solicitantes AFPś 
                ['name' => 'Memorándum de agradecimiento de servicios en copia simple emitido por el Comando General de la Policía Boliviana.','number'=>0],  // *caso pasivo con renta en AFP , quien solicita el préstamo

              ]],
              // REFINANCIAMIENTO ACTIVO NO SE APLICA A CORTO PLAZO DISPONIBILIDAD LETRA A
              ['name'=>'Refinanciamiento de Préstamo a corto plazo para el sector  Activo','shortened'=>'PCP-R-SAPV','requirements'=>[
                ['name' => 'Cédula de Identidad del (la) titular en copia simple.','number'=>1], 
                ['name' => 'Boleta de pago en fotocopia simple','number'=>2],
                ['name' => 'Certificado de haberes considerando el último mes percibido.','number'=>2],  // En caso de no contar con la boleta de pago
                ['name' => 'Estado de cuenta original, vigente y emitido por el Banco Unión S.A','number'=>3],
                 /*Estos documentos son solicitados por MUSERPOL en caso de ser requeridos*/
                ['name' => 'Certificado de haberes considerando el último mes percibido.','number'=>0],  // En caso de no contar con la boleta de pago
                ['name' => 'Nota de aclaración de Boleta de Pago.','number'=>0],  // En caso de que el afiliado tenga datos erroneos en su boleta de pago
                ['name' => 'Certificado de no adeudo.','number'=>0],  // en caso de que el afiliado haya tenido deudas de otras entidades (Ej: COMIPOL , COVIPOL)
                ['name' => 'Certificado de pago emitido por la entidad correspondiente.','number'=>0],  // en caso de que el afiliado tenga deudas con otras entidades (Ej: COMIPOL , COVIPOL)
            	]]
            ],
               // REFINANCIAMIENTO PASIVOS Y VIUDAS
              ['name'=>'Refinanciamiento de Préstamo a corto plazo para el sector Pasivo y Viudas','shortened'=>'PCP-R-SAPV','requirements'=>[
                ['name' => 'Cédula de Identidad del (la) titular en copia simple.','number'=>1], 
               // ['name' => '3 Ultimas boletas de renta de jubilación fotocopia simple','number'=>2],
                ['name' => '3 ultimas boletas de pago con renta en SENASIR original','number'=>2], //Nuevo *caso pasivo con renta de SENASIR, quien solicita el préstamo
                ['name' => '3 ultimas boletas de pago con renta en AFP original','number'=>2],      //Nuevo *caso pasivo con renta en AFP , quien solicita el préstamo
                ['name' => 'Estado de cuenta original, vigente y emitido por el Banco Unión S.A','number'=>4],
                /*Estos documentos son solicitados por MUSERPOL en caso de ser requeridos*/
                ['name' => 'Certificado de haberes considerando el último mes percibido.','number'=>0],  // En caso de no contar con la boleta de pago
                ['name' => 'Nota de aclaración de Boleta de Pago.','number'=>0],  // En caso de que el afiliado tenga datos erroneos en su boleta de pago
                ['name' => 'Certificado de no adeudo.','number'=>0],  // en caso de que el afiliado haya tenido deudas de otras entidades (Ej: COMIPOL , COVIPOL)
                ['name' => 'Certificado de pago emitido por la entidad correspondiente.','number'=>0],  // en caso de que el afiliado tenga deudas con otras entidades (Ej: COMIPOL , COVIPOL)
                ['name' => 'Certificación Auxilio Mortuorio','number'=>0],  // *caso pasivo con renta en AFP , quien solicita el préstamo
                ['name' => 'Certificación de aportes voluntarios de los 3 últimos meses de la unidad de Fondo de Retiro.','number'=>0], // para solicitantes AFPś , no se encuentra en sus requisitos pero en una anterior modalidad  se menciona como requisito
                ['name' => 'Memorándum de agradecimiento de servicios en copia simple emitido por el Comando General de la Policía Boliviana.','number'=>0],  // *caso pasivo con renta en AFP , quien solicita el préstamo

            	]]
            ]
          ],
          'largo' => [
            'type' => ['module_id' => $module->id,'name'=>'Préstamo a largo plazo','second_name'=>'Préstamo a largo plazo'],
            'modalities' => [
                // LARGO PLAZO SECTOR ACTIVO Y ADMINISTRATIVO CON GARANTIA PERSONAL(2 GARANTES ACTIVOS)
              ['name'=>'Largo Plazo con garantía personal para el sector activo y personal Adm Policial','shortened'=>'PLP-GP-SAYADM','requirements'=>[
                ['name' => 'Cédula de Identidad del (la) titular en copia simple','number'=>1],
                ['name' => 'Boleta de pago en copia simple.','number'=>2], // Tambien de los 2 garantes
                ['name' => 'Certificado de años de servicio desglosado en original emitido por el Comando General de la Policía Boliviana','number'=>3],
                ['name' => 'Certificado de años de servicio desglosado en copia legalizada emitido por el Comando General de la Policía Boliviana.','number'=>3], 
                ['name' => 'Estado de cuenta original, vigente y emitido por el Banco Unión S.A','number'=>4],
                /*Estos documentos son solicitados por MUSERPOL en caso de ser requeridos*/
                ['name' => 'Certificado de haberes considerando el último mes percibido.','number'=>0],  // En caso de no tener la boleta de pago
                ['name' => 'Nota de aclaración de Boleta de Pago.','number'=>0],  // En caso de que el afiliado tenga datos erroneos en su boleta de pago
                ['name' => 'Certificado de no adeudo.','number'=>0],  // en caso de que el afiliado haya tenido deudas de otras entidades (Ej: COMIPOL , COVIPOL)
                ['name' => 'Certificado de pago emitido por la entidad correspondiente.','number'=>0],  // en caso de que el afiliado tenga deudas con otras entidades (Ej: COMIPOL , COVIPOL)
                ['name' => 'Fotocopia Testimonio de propiedad.','number'=>0],  // en caso de que el plazo sea mayor a 60 meses un prestamo para vivienda pero no hipotecario
                ['name' => 'Folio Real Actualizado o Información rapida del inmueble.','number'=>0],  // en caso de que el plazo sea mayor a 60 meses un prestamo para vivienda pero no hipotecario
                ['name' => 'Fotocopia de catastro.','number'=>0],  // en caso de que el plazo sea mayor a 60 meses un prestamo para vivienda pero no hipotecario


               ]],
                  // LARGO PLAZO SECTOR PASIVO CON GARANTIA PERSONAL ( 1 o 2 GARANTES ACTIVOS O PASIVOS)
              ['name'=>'Largo Plazo con garantía personal para el sector pasivo','shortened'=>'PLP-GP-SP','requirements'=>[
                ['name' => 'Cédula de Identidad del (la) titular en copia simple','number'=>1],// tambien el o los garantes
                //['name' => 'Boleta de prestación de jubilación fotocopia simple.','number'=>2], 
                ['name' => 'Ultima boleta de pago con renta en SENASIR original','number'=>2], //Nuevo *caso pasivo con renta de SENASIR, quien solicita el préstamo; tambien el garante
                ['name' => 'Ultima boleta de pago con renta en AFP original','number'=>2],      //Nuevo *caso pasivo con renta en AFP , quien solicita el préstamo
                ['name' => 'Boleta de pago en copia simple.','number'=>3], // caso de que el garante sea un afiliado activo
                ['name' => 'Certificación de aportes voluntarios de los 3 últimos meses de la unidad de Fondo de Retiro.','number'=>4], // para solicitantes AFPś , 
                ['name' => 'Estado de cuenta original, vigente y emitido por el Banco Unión S.A','number'=>5],
                /*Estos documentos son solicitados por MUSERPOL en caso de ser requeridos*/
                ['name' => 'Certificado de haberes considerando el último mes percibido.','number'=>0],  // En caso de no tener la boleta de pago
                ['name' => 'Nota de aclaración de Boleta de Pago.','number'=>0],  // En caso de que el afiliado tenga datos erroneos en su boleta de pago
                ['name' => 'Certificado de no adeudo.','number'=>0],  // en caso de que el afiliado haya tenido deudas de otras entidades (Ej: COMIPOL , COVIPOL)
                ['name' => 'Certificado de pago emitido por la entidad correspondiente.','number'=>0],  // en caso de que el afiliado tenga deudas con otras entidades (Ej: COMIPOL , COVIPO
                ['name' => 'Certificación Auxilio Mortuorio','number'=>0],  // *caso pasivo con renta en AFP , quien solicita el préstamo
                ['name' => 'Memorándum de agradecimiento de servicios en copia simple emitido por el Comando General de la Policía Boliviana.','number'=>0],  // *caso pasivo con renta en AFP , quien solicita el préstamo

              ]],
                // LARGO PLAZO UN SOLO GARANTE SECTOR ACTIVO --Afiliados CPOP
              ['name'=>'Largo Plazo con un solo garante para el sector activo','shortened'=>'PLP-S1G-SA','requirements'=>[
                ['name' => 'Cédula de Identidad del (la) titular en copia simple','number'=>1],
                ['name' => 'Boleta de pago en copia simple.','number'=>2], 
                ['name' => 'Certificado de años de servicio desglosado en original emitido por el Comando General de la Policía Boliviana','number'=>3],
                ['name' => 'Certificado de años de servicio desglosado en fotocopia Legalizada emitido por el Comando General de la Policía Boliviana','number'=>3],
	              ['name' => 'Certificado de años de servicio desglosado en original emitido por el Comando Regional de la Policía Boliviana','number'=>3],
                ['name' => 'Certificado de años de servicio desglosado en fotocopia Legalizada emitido por el Comando Regional de la Policía Boliviana','number'=>3],
                ['name' => 'Estado de cuenta original, vigente y emitido por el Banco Unión S.A','number'=>4],
                 /*Estos documentos son solicitados por MUSERPOL en caso de ser requeridos*/
                ['name' => 'Certificado de haberes considerando el último mes percibido.','number'=>0],  // En caso de no tener la boleta de pago
                ['name' => 'Nota de aclaración de Boleta de Pago.','number'=>0],  // En caso de que el afiliado tenga datos erroneos en su boleta de pago
                ['name' => 'Certificado de no adeudo.','number'=>0],  // en caso de que el afiliado haya tenido deudas de otras entidades (Ej: COMIPOL , COVIPOL)
                ['name' => 'Certificado de pago emitido por la entidad correspondiente.','number'=>0],  // en caso de que el afiliado tenga deudas con otras entidades (Ej: COMIPOL , COVIPOL)
              	['name' => 'Certificado de no ingreso a disponibilidad de las letras “C” y “A” en original emitido por el Comando General de la Policía Boliviana.','number'=>0], //verificacion, no encontrarse en disponibilidad de letra A o C o item 0
              ]],            
              // REFINANCIANCIAMIENTO SECTOR ACTIVO CON UN SOLO GARANTE ---> En este caso el garante solo puede ser activo
              ['name'=>'Refinanciamiento de prestamos a largo Plazo para el sector activo con un solo garante','shortened'=>'PLP-R-SA','requirements'=>[
                ['name'=>'Cédula de Identidad del (la) titular en copia simple','number'=>1],
                ['name' => 'Boleta de pago en copia simple.','number'=>2], 
            		['name' => 'Certificado de años de servicio desglosado en original emitido por el Comando General de la Policía Boliviana','number'=>3],
            		['name' => 'Certificado de años de servicio desglosado en fotocopia Legalizada emitido por el Comando General de la Policía Boliviana','number'=>3],
                ['name' => 'Certificado de años de servicio desglosado en original emitido por el Comando Regional de la Policía Boliviana','number'=>3],
                ['name' => 'Estado de cuenta original, vigente y emitido por el Banco Unión S.A','number'=>5],
                /*condiciones COP MUSERPOL*/
                /**/
                /*Estos documentos son solicitados por MUSERPOL en caso de ser requeridos*/
                ['name' => 'Certificado de haberes considerando el último mes percibido.','number'=>0],  // En caso de no tener la boleta de pago
                ['name' => 'Nota de aclaración de Boleta de Pago.','number'=>0],  // En caso de que el afiliado tenga datos erroneos en su boleta de pago
                ['name' => 'Certificado de no adeudo.','number'=>0],  // en caso de que el afiliado haya tenido deudas de otras entidades (Ej: COMIPOL , COVIPOL)
                ['name' => 'Certificado de pago emitido por la entidad correspondiente.','number'=>0],  // en caso de que el afiliado tenga deudas con otras entidades (Ej: COMIPOL , COVIPOL)

              ]],
                 // REFINANCIANCIAMIENTO SECTOR PASIVO CON UN SOLO GARANTE
               ['name'=>'Refinanciamiento de prestamos a largo Plazo para el sector pasivo con un solo garante','shortened'=>'PLP-R-SAP','requirements'=>[
                ['name'=>'Cédula de Identidad del (la) titular en copia simple','number'=>1],
              //  ['name' => 'Boleta de prestación de jubilación fotocopia simple.','number'=>2], 
                ['name' => 'Certificado de años de servicio desglosado en original emitido por el Comando General de la Policía Boliviana','number'=>3],
            		['name' => 'Certificado de años de servicio desglosado en fotocopia Legalizada emitido por el Comando General de la Policía Boliviana','number'=>3],
                ['name' => 'Certificado de años de servicio desglosado en original emitido por el Comando Regional de la Policía Boliviana','number'=>3],
                ['name' => 'Certificado de Aportes voluntarios de los tres últimos meses a MUSERPOL','number'=>4], //*caso pasivo con renta en AFP 
                ['name' => 'Estado de cuenta original, vigente y emitido por el Banco Unión S.A','number'=>5],
                ['name' => 'Ultima boleta de pago de renta en SENASIR original','number'=>6], // *caso pasivo con renta de SENASIR, quien solicita el préstamo; tambien para el garante solo SENASIR
                ['name' => 'Ultima boleta de pago de renta en AFP original','number'=>7],      //*caso pasivo con renta en AFP , quien solicita el préstamo
								['name' => 'Certificación Auxilio Mortuorio','number'=>8],  // *caso pasivo con renta en AFP , quien solicita el préstamo
								['name' => 'Boleta de pago en copia simple.','number'=>9],  // *caso garante activo
                /*condiciones COP MUSERPOL*/
                /**/
                /*Estos documentos son solicitados por MUSERPOL en caso de ser requeridos*/
                ['name' => 'Certificado de haberes considerando el último mes percibido.','number'=>0],  // En caso de no tener la boleta de pago
                ['name' => 'Nota de aclaración de Boleta de Pago.','number'=>0],  // En caso de que el afiliado tenga datos erroneos en su boleta de pago
                ['name' => 'Certificado de no adeudo.','number'=>0],  // en caso de que el afiliado haya tenido deudas de otras entidades (Ej: COMIPOL , COVIPOL)
                ['name' => 'Certificado de pago emitido por la entidad correspondiente.','number'=>0],  // en caso de que el afiliado tenga deudas con otras entidades (Ej: COMIPOL , COVIPOL)
                ['name' => 'Memorándum de agradecimiento de servicios en copia simple emitido por el Comando General de la Policía Boliviana.','number'=>0],  // *caso pasivo con renta en AFP , quien solicita el préstamo
              ]], 
            ]
          ],
          'hipotecario' => [
            'type' => ['module_id' => $module->id,'name'=>'Préstamo hipotecario','second_name'=>'Préstamo hipotecario'],
            'modalities' => [
               // LARGO PLAZO GARANTIA HIPOTECARIA
              ['name'=>'Préstamo con garantia hipotecaria de bien inmueble para el sector activo','shortened'=>'PLP-GH-SA','requirements'=>[
                ['name'=>'Cédula de Identidad del (la) titular en copia simple','number'=>2],
               ['name' => 'Certificado de años de servicio desglosado en original emitido por el Comando General de la Policía Boliviana','number'=>3],
               ['name' => 'Certificado de años de servicio desglosado en fotocopia Legalizada emitido por el Comando General de la Policía Boliviana','number'=>3],
               ['name' => 'Certificado de años de servicio desglosado en original emitido por el Comando Regional de la Policía Boliviana','number'=>3],
               ['name' => 'Croquis de ubicación del inmueble','number'=>4], //nuevo
               ['name' => 'Factura de Luz del Inmueble','number'=>5], //nuevo
               ['name' => 'Factura de Agua del Inmueble','number'=>5], //nuevo
               ['name' => 'Folio Real','number'=>6], //nuevo
               ['name' => 'Informacion rapida del Inmueble','number'=>6], //nuevo
               ['name' => 'Fotocopia de Testimonio de Propiedad','number'=>7], //nuevo
               ['name' => 'Fotocopia de Impuestos 3 Ultimos Años','number'=>8], //nuevo
               ['name' => 'Fotocopia de Plano de lote','number'=>9], //nuevo
               ['name' => 'Fotocopia de Catastro','number'=>10], //nuevo
                ['name' => 'Estado de cuenta original, vigente y emitido por el Banco Unión S.A','number'=>11],
               ['name' => 'Certificado de estado civil en original emitido por el SERECI.','number'=>12],
                /*Estos documentos son solicitados por MUSERPOL en caso de ser requeridos*/
               ['name' => 'Certificado de haberes considerando el último mes percibido.','number'=>0],  // En caso de no tener la boleta de pago
               ['name' => 'Nota de aclaración de Boleta de Pago.','number'=>0],  // En caso de que el afiliado tenga datos erroneos en su boleta de pago
               ['name' => 'Certificado de no adeudo.','number'=>0],  // en caso de que el afiliado haya tenido deudas de otras entidades (Ej: COMIPOL , COVIPOL)
               ['name' => 'Certificado de pago emitido por la entidad correspondiente.','number'=>0]  // en caso de que el afiliado tenga deudas con otras entidades (Ej: COMIPOL , COVIPOL)
               /**/
             ]]  
            ]
          ],
        ]
      ];
      foreach ($data['procedures'] as $procedure) {
        $new_procedure = ProcedureType::firstOrCreate($procedure['type']);
        foreach ($procedure['modalities'] as $modality) {
          $new_modality = ProcedureModality::firstOrCreate([
            'procedure_type_id' => $new_procedure->id,
            'name'=>$modality['name'],
            'shortened'=>$modality['shortened']
          ]);
          foreach ($modality['requirements'] as $requirement) {
            \Log::info($requirement);
            $new_document = ProcedureDocument::firstOrCreate([
              'name'=>$requirement['name']
            ]);
            ProcedureRequirement::firstOrCreate([
              'procedure_modality_id'=>$new_modality->id,
              'procedure_document_id'=>$new_document->id,
              'number'=>$requirement['number']
            ]);
          }
        }
      }
      
   
    }
}



           