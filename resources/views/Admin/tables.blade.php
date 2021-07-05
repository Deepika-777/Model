@extends('Style.content')

@section('title')

HETA DATAIN|DASHBOARD

@endsection

@section('content')

<style>

.btn-primary {
  padding: 8px 12px;
  font-size: 12px;
  text-align: center;
  cursor: pointer;
  outline: none;
  color: #fff;
  background-color: black;
  border: none;
  border-radius: 15px;
  box-shadow: 0 5px #999;
}

.btn-primary:hover {background-color: #F0EC11}

.btn-primary:active {
  background-color: black;
  box-shadow: 0 5px #666;
  transform: translateY(4px);
}

.font-weight-bold mb-0{
  font-size : 30px;
}

.card-body{
  font-size: 20px !important;
}
.mypill{
	margin : 0.1rem !important;
	border-radius: 0.2rem !important;
  width : 75px;
  }

</style>

<!-- Card 1 -->

<div class="row"> 

  @foreach($data as $key=>$value)
    @foreach($value['detail']['data'] as $key2=>$value2)
      <div class="col-md-12">

          <div class="card" > 


              <div class="card-header white">
                <p class="text-uppercase small mb-2">Project Name </p>
                <h5 class="font-weight-bold mb-0">
                  {{ $value2[0]->project_name}}
                </h5>
              </div>

              <div class="card-body">
            
                  @foreach($value2 as $key3=>$value3)
                        <span class="badge badge-pill mypill {{$value3->color}}" title="" 
                          data-toggle="popover" data-trigger="click"
                          data-content="{{$value3->text}} {{$value3->dt_time}} " 
                          data-original-title="{{$value3->device_name }} [Client ID : {{$value3->project_id}}]">
                            {{$value3->device_id}}
                        </span>
                  @endforeach
              
              </div>      

              <div class="card-footer white">
                <Next type="Next" class="btn-primary" >
          
                    <a href="{{ url('/deviceinfo_vfm/'.$value['id'].'/'.$key2) }}" class="text-sm text-gray-700 underline">Details</a>
                </Next>
              </div>

          </div>
      </div>
      @endforeach
  @endforeach
</div>


@endsection

@section('scripts')

<script>
  $('[data-toggle="popover"]').popover();

  $(document).ready(function(){

$('[data-toggle="popover"]').popover();   
$('[data-toggle="click"]').popover();  
$('body').on('click', function (e) {
  //did not click a popover toggle or popover
console.log(e);
  if ($(e.target).data('toggle') !== 'popover'
      && $(e.target).parents('.popover.in').length === 0) { 
      $('[data-toggle="popover"]').popover('hide');
  }
});

});
</script>

@endsection
