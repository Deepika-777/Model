@extends('Style.content')

@section('title')

HETADATAIN|DASHBOARD

@endsection

@section('content')




@endsection

@section('scripts')

@endsection



@extends('Style.content')

@section('title')

HETADATAIN|DASHBOARD

@endsection

@section('content')

<style>


.btn-primary {
  padding: 8px 12px;
  font-size: 20px;
  text-align: center;
  cursor: pointer;
  outline: none;
  color: #fff;
  background-color: black;
  border: none;
  border-radius: 15px;
  box-shadow: 0 9px #999;
}

.btn-primary:hover {background-color: cadetblue}

.btn-primary:active {
  background-color: black;
  box-shadow: 0 5px #666;
  transform: translateY(4px);
}

.font-weight-bold mb-0{
  font-size : 30px;
}

.card-body{
  font-size: 25px; 
}

</style>

<!-- Card 1 -->
<div class="row"> 

 
    <div class="col-md-12">

      <div class="card" >

      <div class="card-header white">
        <p class="text-uppercase small mb-2">Project Name </p>
        <h5 class="font-weight-bold mb-0">
      {{$data['project_name']}}
        </h5>
      </div>

      <div class="card-body">
     
      <table class="table">
  <thead>
    <tr>
      <th scope="col">DEVICE ID</th>
      <th scope="col">CLIENT ID</th>
      <th scope="col">NAME</th>
      <th scope="col">DATE-TIME</th>

    </tr>
  </thead>
  <tbody>
    @foreach($data['detail'] as $key=>$value)
    <tr class="{{$value->color}}">
      <th scope="row">{{$value->device_id}}</th>
      <td>{{$value->client_id}}</td>
      <td>{{$value->device_name}}</td>
      <td>{{$value->dt_time}}</td>
    </tr> 
    @endforeach   
  </tbody>
</table>
      </div>
      </div>
    </div>

</div>



@endsection


@section('scripts')

@endsection
