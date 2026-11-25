
<div class="gallery container my-5">

    <div class="slider">
      @for($i=0; $i < 5; $i++)
        <div><img src="{{asset('storage_original/imagenes/image_login.jpg')}}" class="w-100" style="height: 140px;" alt="Imagen {{$i}}"></div>
        <div><img src="{{asset('storage_original/imagenes/logo_negro_200.jpeg')}}" class="w-100" style="height: 140px;" alt="Imagen {{$i++}}"></div>
      @endfor
      <!-- Agrega las otras imágenes aquí -->
    </div>
</div>
  