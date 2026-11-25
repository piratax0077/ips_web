@extends('layouts.app')

@section('content')

<div class="container" data-aos="fade-up-right">
    <div style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="/">Inicio</a></li>
          <li class="breadcrumb-item active" aria-current="page">Aprende con nosotros</li>
        </ol>
      </div>
    <img src="{{asset('storage_original/imagenes/banner_culata.jpeg')}}" alt="" class="w-100" data-aos="flip-left">
    <p class="p-2 justify_text" style="font-size: 20px;">La culata, también denominada cabeza del motor, consiste en un bloque de metal, generalmente de hierro fundido o aleación de aluminio, 
        que sella la parte superior de los cilindros de un motor de combustión evitando así que haya pérdidas de compresión.</p>
    <p class="p-2 justify_text" style="font-size: 20px;">Se fabrica con estos materiales buscando un <strong>equilibrio entre altos niveles de resistencia y rigidez combinados con una buena conductividad 
        térmica</strong> que permita liberar al exterior el calor de la cámara de combustión mejorando así el rendimiento del vehículo al elevar la relación de compresión.</p>
    <p class="p-2 justify_text" style="font-size: 20px;">Las culatas de aluminio, aunque cuentan con mejores propiedades de conducción del calor y son más ligeras, resisten peor la fricción de los pistones, por lo suelen llevar 
        un revestimiento de acero y son más caras que las de fundición de hierro.</p>
    <p class="p-2 justify_text" style="font-size: 20px;">La culata se encuentra unida al bloque motor por medio de tornillos y una empaquetadura, que se encarga de sellar con firmeza y flexibilidad ambos componentes para soportar 
        las altas temperaturas producidas por el motor e impedir fugas de compresión o líquido refrigerante.</p>
    <img src="{{asset('storage_original/imagenes/culata.jpg')}}" alt="" class="p-0 w-100" data-aos="flip-right">
    <p class="text-start" style="font-size: 20px;"><strong>Componentes que forman una culata</strong></p>
    <p class="p-2 justify_text" style="font-size: 20px;" data-aos="flip-up">Aunque visualicemos la <strong>culata como una tapa para los cilindros </strong>, es algo más que una única pieza que cierra el bloque motor; el probablemente el elemento más complejo del 
        vehículo pues sobre ella se asientan numerosos componentes que, en caso de sufrir desgastes u holguras pueden acabar por afectar a la estructura principal. 
        Así que entre sus funciones también se encuentra la de alojar:</p>
    <ul>
        <li style="font-size: 20px;" class="justify_text">El tren alternativo: <strong>la culata como una tapa para los cilindros</strong>así como conexiones o aberturas para dispositivos que se accionen con la rotación del cigüeñal como pueden ser las bombas de agua, aceite y combustible.</li>
        <li style="font-size: 20px;" class="justify_text">Las <strong>válvulas de admisión y escape.</strong></li>
        <li style="font-size: 20px;" class="justify_text">El árbol de levas o apoyos para el mismo en caso de motores donde el eje de levas vaya montado fuera (como los motores OHV).</li>
        <li style="font-size: 20px;" class="justify_text">Orificios para las bujías si se trata de un motor de gasolina o para los inyectores o incluso una precámara de combustión si es un motor diésel.</li>
        <li style="font-size: 20px;" class="justify_text">Conductos para la refrigeración: en motores de refrigeración líquida tiene oquedades para la circulación del agua de enfriamiento y otras cavidades tubulares para el aceite lubricante con un filtro también fijado a la misma.</li>
    </ul>
    <p class="p-2 justify_text" style="font-size: 20px;">Uno de los principales síntomas de avería de la culata es la aparición de humo blanco en el escape de modo continuo, ya que suele indicar que ha entrado agua durante a combustión a causa de una rotura o filtración de la culata.</p>
</div>

@endsection
