@extends('layout.default')

@php
    $locale = App::getLocale();
@endphp


@section('content')
    <div class="row">
        <div class="col-md-12 col-md-offset-0">
            <h2>Subscription Active Data</h2>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Interval</th>
                        <th>Ammont</th>
                        <th>End Date</th>
                       
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @if(!empty($dataActive))
                        @foreach($dataActive->data as $key => $value)
                            <tr>
                                <td>{{ ++$key }}</td>
                                <td>{{ $value->plan->interval }}</td>
                                <td>${{ $value->plan->amount/100 }}</td>
                                <td>{{ date('Y-m-d',$value->current_period_end) }}</td>
                                <td>
                                    @if($value->status == 'active')
                                    <a class="btn btn-danger" href="{{route('cancel-subscription', $value->id)}}">Cancel Subscription</a> 
                                    @else
                                          {{$value->status}}  
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>

@endsection
