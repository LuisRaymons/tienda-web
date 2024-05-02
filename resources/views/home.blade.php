@extends('layouts.master')

@section('css')
  <style media="screen">
    .canva{
      height: 611px;
      display: block;
      width: 1223px;
      width:1100;
      height:549;
    }
  </style>
@endsection

@section('content')
  <div class="container {{(Auth::user()->type == 'Administrador' || Auth::user()->type == 'Vendedor')   ? '' : 'showcontentuser' }}">
    <input type="hidden" name="iduser" id="iduser" value="{{Auth::user()->id}}">
    <input type="hidden" name="nameuser" id="nameuser" value="{{Auth::user()->name}}">
    <input type="hidden" name="typeuser" id="typeuser" value="{{Auth::user()->type}}">

    <div class="row">
      <div class="col-lg-6">
        <h4 id="faltantesproduct"></h4>
      </div>
      <div class="col-lg-6">
        <p id="totalventasmes" style="float:right"></p>
      </div>
    </div>

    <div class="row" id="productos-inexistentes">
      <!-- loading productos sin existencia-->
    </div>

    <div class="row">
      <div class="col-lg-6">
        <div class="card">
          <div class="chartjs-size-monitor">
            <canvas id="ventasporuser" class="canva"></canvas>
          </div>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="card {{(Auth::user()->type == 'Vendedor')   ? 'showcontentuser' : '' }}">
          <div class="chartjs-size-monitor">
            <canvas id="chartventastotalesmes" class="canva {{(Auth::user()->type == 'Administrador' || Auth::user()->type == 'Vendedor')   ? '' : 'showcontentuser' }}"></canvas>
          </div>
        </div>
      </div>
    </div>


  </div>
@endsection


@section('js')
  <script src="../src/plugging/chart/chart.min.js"></script>
  <script src="../src/js/dashboard.js"></script>
@endsection
