@extends('generic/layout')
<?php
/*
    (c) 2014 Castellers de la Vila de Gràcia
    info@cvg.cat

    This file is part of l'Admin Blau.

    L'Admin Blau is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    L'Admin Blau is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
*/
?>
@section('content')

<!-- we use the same code to display three panels. -->
<!-- each second entry in the following array is a function that returns the relevant total -->
@foreach (['Esdeveniment' => 'persones_actives', 
	   'Actuacion' => 'pinya_necessaria', 
	   'Missatge' => null 
	   ] 
	  as $CSN => $detail_total_method) 
  <?php 
    $csn = strtolower($CSN);
    $instance = new $CSN; 
  ?>
  <div id="{{ $csn }}-panel" class="panel panel-default">
    <div>
      <!-- The panel headers -->
      <h2>
	@if ($CSN == 'Actuacion')
          assajos i actuacions
        @else
          {{ $csn }}s
        @endif
      </h2>
    </div>
    <div class="panel-body">
	<!-- Launch a query to find relevant esdeveniments, actuacions, missatges -->
      @foreach ($instance
		->where('data', '>=', date('Y-m-d', strtotime('now')))
		->orderBy('data')
		->get()
		as $res) 
	<!-- inside each panel, generate a row for each instance found -->
      <div id="{{ $csn }}-{{ $res->id }}" class="row cvg-{{ $csn }}">

        <div class="col-md-4">
          <div class="row">
            <!-- first, relevant data: titol, data, lloc -->
            <div id="{{ $csn }}-{{ $res->id }}-titol" class="col-md-5 cvg-{{ $csn }}-titol">
              {{ $res->titol }}
            </div>
            <div class="col-md-7">
              <div id="{{ $csn }}-{{ $res->id }}-data" class="cvg-{{ $csn }}-data">
                {{ $res->data }}
              </div>
              <div id="{{ $csn }}-{{ $res->id }}-llocs_fk" class="cvg-{{ $csn }}-llocs_fk">
                {{ $res->llocs_fk }}
              </div>
            </div> <!-- col-md-7 -->
          </div> <!-- row -->
        </div> <!-- col-md-4 -->

        <div id="{{ $csn }}-{{ $res->id }}-details" detail-id="{{ $res->id }}" class="col-md-8 {{ $csn }}-details">
          <div class="row">
          <!-- next, the details for each instance -->
            @foreach($CSN::details($res->id) as $detail)
              @if($detail_total_method == null) 
	        <!-- this happens for missatge -->
	        <div class="col-md-2">
                  {{ $detail[1] }} 
                </div>
              @else
	        <!-- this happens for esdeveniments and actuacions -->
                <?php
                  $total = $CSN::$detail_total_method($detail[1]);
                ?>
	        <div class="col-md-1">
		<!-- display the hour, viz. the castell -->
                  {{ $detail[1] }}
                </div>
	        <div class="col-md-1">
		  <!-- how many people are currently assigned to the esdeveniment or castell? -->
		  <!-- This will later be updated via ajax -->
                  <span class="current-count-detail-id-{{ $res->id }}">0</span>/{{ $total }}
                </div>
                <div class="col-md-10">
		  <!-- code for the progress bar -->
                  <div class="progress progress-striped">
                    <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="{{ $total }}" style="width: 0%" detail-id="{{ $res->id }}">
                    </div> <!-- progress-bar -->
                  </div> <!-- progress -->
                </div> <!-- detailId -->
              @endif
            @endforeach
          </div> <!-- row -->
        </div> <!-- col-md-8 -->

      </div> <!-- csn-res-id -->
      @endforeach
    </div> <!-- /panel-body -->
  </div> <!-- /panel -->
@endforeach

<script>
$(function() {
	$('.esdeveniment-details').each(function() {
		updateStatusBars($(this), '/esdeveniments/apuntats/', [10, 25, 50]);
	    });
	$('.actuacion-details').each(function() {
		updateStatusBars($(this), '/actuacions/apuntats/', [50, 70, 90]);
	    });
    });

</script>



@stop